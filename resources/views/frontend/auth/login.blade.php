@extends('frontend.layout')

@section('content')
<div class="breadcrumb-area pt-30 position-relative breadcrumb-padding pb-30" style="background-image: url({{ asset('themes/images/slide2.jpg') }})">
<div style="position: absolute;
    background-color: rgba(0, 0, 0, .5);
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;"></div>  	
<div class="container-fluid">
		<div class="breadcrumb-content text-center">
			<ul>
				<li><a style="color: #1F82F3;" href="#">home</a></li>
				<li style="z-index: 9999;position: relative;color: #fff;">login</li>
			</ul>
		</div>
	</div>
</div>
<!-- register-area start -->
<div class="register-area ptb-100">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 col-12 col-lg-12 col-xl-6 ml-auto mr-auto">
				<div class="login">
					<div class="login-form-container">
						<div class="login-form">
							<form method="POST" action="{{ route('login') }}">
								@csrf
								<div class="form-group row">
									<div class="col-md-12">
										<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('E-Mail Address') }}">
										@error('email')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-12">
										<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">
										@error('password')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
								</div>
								<div class="form-group row mb-0">
									<div class="col-md-12">
										<div class="button-box">
                                            <div class="login-toggle-btn">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label for="remember">{{ __('Remember Me') }}</label>
                                                <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                                            </div>
                                            <button type="submit" style="background-color: #1F82F3;" class="default-btn text-white floatright">Login</button>
                                        </div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- register-area end -->
@endsection