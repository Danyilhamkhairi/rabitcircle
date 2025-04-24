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
					<li style="z-index: 9999;position: relative;color: #fff;">register</li>
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
								<form method="POST" action="{{ route('register') }}">
									@csrf

									<div class="form-group row">
										<div class="col-md-12">
											<input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus placeholder="First name">
											@error('first_name')
												<span class="invalid-feedback" role="alert">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus placeholder="Last name">
											@error('last_name')
												<span class="invalid-feedback" role="alert">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">

											@error('email')
												<span class="invalid-feedback" role="alert">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">

											@error('password')
												<span class="invalid-feedback" role="alert">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password">
										</div>
									</div>

									<div class="button-box">
										<button type="submit" style="background-color: #1F82F3;" class="text-white default-btn floatright">Register</button>
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