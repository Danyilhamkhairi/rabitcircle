<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

class PaymentController extends Controller
{
    /**
     * Initialize Midtrans Payment Gateway configuration
     */
    protected function initPaymentGateway()
    {
        // Set your Midtrans server key
        Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment
        Config::$isProduction = config('midtrans.isProduction');
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;
    }

    /**
     * Handle payment notification from Midtrans
     */
    public function notification(Request $request)
    {
        $payload = $request->getContent();
        Log::info('Midtrans notification received: ' . $payload);
        $notification = json_decode($payload);

        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('midtrans.serverKey'));

        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid signature'], 403);
        }

        $this->initPaymentGateway();
        $statusCode = null;

        $paymentNotification = new \Midtrans\Notification();
        $order = Order::where('code', $paymentNotification->order_id)->first();

        if (!$order) {
            Log::error('Order not found: ' . $paymentNotification->order_id);
            return response(['message' => 'Order not found'], 404);
        }

        if ($order->isPaid()) {
            return response(['message' => 'The order has been paid before'], 422);
        }

        $transaction = $paymentNotification->transaction_status;
        $type = $paymentNotification->payment_type;
        $orderId = $paymentNotification->order_id;
        $fraud = $paymentNotification->fraud_status;

        $vaNumber = null;
        $vendorName = null;
        if (!empty($paymentNotification->va_numbers[0])) {
            $vaNumber = $paymentNotification->va_numbers[0]->va_number;
            $vendorName = $paymentNotification->va_numbers[0]->bank;
        }

        $paymentStatus = null;
        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    $paymentStatus = Payment::CHALLENGE;
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                    $paymentStatus = Payment::SUCCESS;
                }
            }
        } else if ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $paymentStatus = Payment::SETTLEMENT;
        } else if ($transaction == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $paymentStatus = Payment::PENDING;
        } else if ($transaction == 'deny') {
            // TODO set payment status in merchant's database to 'Denied'
            $paymentStatus = Payment::DENY;
        } else if ($transaction == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $paymentStatus = Payment::EXPIRE;
        } else if ($transaction == 'cancel') {
            // TODO set payment status in merchant's database to 'Denied'
            $paymentStatus = Payment::CANCEL;
        }

        $paymentParams = [
            'order_id' => $order->id,
            'number' => Payment::generateCode(),
            'amount' => $paymentNotification->gross_amount,
            'method' => 'midtrans',
            'status' => $paymentStatus,
            'token' => $paymentNotification->transaction_id,
            'payloads' => $payload,
            'payment_type' => $paymentNotification->payment_type,
            'va_number' => $vaNumber,
            'vendor_name' => $vendorName,
            'biller_code' => $paymentNotification->biller_code ?? null,
            'bill_key' => $paymentNotification->bill_key ?? null,
        ];

        Log::info('Creating payment with params: ' . json_encode($paymentParams));
        $payment = Payment::create($paymentParams);

        if ($paymentStatus && $payment) {
            \DB::transaction(
                function () use ($order, $payment) {
                    if (in_array($payment->status, [Payment::SUCCESS, Payment::SETTLEMENT])) {
                        Log::info('Updating order status to PAID for order: ' . $order->code);
                        $order->payment_status = Order::PAID;
                        $order->status = Order::CONFIRMED;
                        $order->save();
                    }
                }
            );
        }

        $message = 'Payment status is : '. $paymentStatus;

        $response = [
            'code' => 200,
            'message' => $message,
        ];

        return response($response, 200);
    }

    /**
     * Show completed payment status
     *
     * @param Request $request payment data
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completed(Request $request)
    {
        Log::info('Payment completed callback received with data: ' . json_encode($request->all()));
        $code = $request->query('order_id');
        $order = Order::where('code', $code)->first();

        if (!$order) {
            Log::error('Order not found in completed callback: ' . $code);
            return redirect('/')->with('error', 'Order not found');
        }

        // Refresh order data from database to get the latest status
        $order = Order::find($order->id);

        if ($order->payment_status == Order::UNPAID) {
            // If payment is still marked as unpaid, check if the payment was actually processed
            // This is because sometimes the notification webhook might be delayed
            Log::info('Order still marked as unpaid, but redirect shows completed. Checking payment status from Midtrans...');

            try {
                $this->initPaymentGateway();
                $status = \Midtrans\Transaction::status($order->code);

                Log::info('Midtrans status check response: ' . json_encode($status));

                if (isset($status->transaction_status) && in_array($status->transaction_status, ['capture', 'settlement'])) {
                    Log::info('Manual status update for order: ' . $order->code);
                    $order->payment_status = Order::PAID;
                    $order->status = Order::CONFIRMED;
                    $order->save();
                } else {
                    return redirect('payments/failed?order_id='. $code);
                }
            } catch (\Exception $e) {
                Log::error('Error checking transaction status: ' . $e->getMessage());
                // Continue to order received page, but status might still be unpaid
            }
        }

        return redirect('orders/received/'. $order->id)->with('success', 'Thank you for completing the payment process!');
    }

    /**
     * Show unfinish payment page
     *
     * @param Request $request payment data
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unfinish(Request $request)
    {
        Log::info('Payment unfinish callback received with data: ' . json_encode($request->all()));
        $code = $request->query('order_id');
        $order = Order::where('code', $code)->first();

        if (!$order) {
            Log::error('Order not found in unfinish callback: ' . $code);
            return redirect('/')->with('error', 'Order not found');
        }

        return redirect('orders/received/'. $order->id)->with('warning', "Your payment is not completed yet. You can continue payment from your order details.");
    }

    /**
     * Show failed payment page
     *
     * @param Request $request payment data
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failed(Request $request)
    {
        Log::info('Payment failed callback received with data: ' . json_encode($request->all()));
        $code = $request->query('order_id');
        $order = Order::where('code', $code)->first();

        if (!$order) {
            Log::error('Order not found in failed callback: ' . $code);
            return redirect('/')->with('error', 'Order not found');
        }

        return redirect('orders/received/'. $order->id)->with('error', "Sorry, we couldn't process your payment. Please try again or choose another payment method.");
    }
}
