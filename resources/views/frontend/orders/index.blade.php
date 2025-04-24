@extends('frontend.layout')

@section('content')
<div class="breadcrumb-area pt-30 position-relative breadcrumb-padding pb-30" style="background-image: url({{ asset('themes/images/slide1.jpg') }})">
	<div style="position: absolute;
    background-color: rgba(0, 0, 0, .5);
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;"></div>		
	<div class="container-fluid">
			<div class="breadcrumb-content text-center">
				<ul>
				<li><a style="color: #1F82F3;" href="{{ url('/') }}">home</a></li>
					<li style="z-index: 9999;position: relative;color: #fff;">Daftar Order</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="shop-page-wrapper shop-page-padding ptb-100">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-3">
					@include('frontend.partials.user_menu')
				</div>
				<div class="col-lg-9">
					<div class="shop-product-wrapper res-xl">
						<div class="table-content table-responsive">
							<table class="table table-bordered table-striped">
								<thead>
									<th>Order ID</th>
									<th>Grand Total</th>
									<th>Nomer Resi</th>
									<th>Status</th>
									<th>Payment</th>
									<th>Action</th>
								</thead>
								<tbody>
									@forelse($orders as $order)
										<tr>    
											<td>
												{{ $order->code }}<br>
												<span style="font-size: 12px; font-weight: normal"> {{ date('d M Y', strtotime($order->order_date)) }}</span>
											</td>
											<td>Rp{{ number_format($order->grand_total, 0, ",", ".") }}</td>
											<td>{{ $order->shipment->track_number }}</td>
											<td>{{ $order->status }}</td>
											<td>{{ $order->payment_status }}</td>
											<td>
												<a href="{{ url('orders/'. $order->id) }}" class="btn btn-info btn-sm">details</a>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="5">No records found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection