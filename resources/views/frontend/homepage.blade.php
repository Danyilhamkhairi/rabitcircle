@extends('frontend.layout')

@section('content')
    <!-- slider -->
        <div class="slider-area">
            <div class="slider-active owl-carousel">
                @foreach($slides as $slide)
                    <div class="single-slider-4 slider-height-6 bg-img" style="background-image: url({{ Storage::url($slide->path) }})">
                    <div style="position: absolute;
    background-color: rgba(0, 0, 0, .5);
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;"></div>    
					<div class="container">
                            <div class="row">
                                <div class="ml-auto col-lg-6">
                                    <div class="furniture-content fadeinup-animated">
                                        <h2 class="animated text-white">{{ $slide->title }}</h2>
                                        <p class="animated text-white">{{ $slide->body }}</p>
                                        <a class="furniture-slider-btn bg-white text-dark animated" href="{{ $slide->url }}">Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    <!-- end -->
    <!-- products -->
    @if ($products)
	<div class="popular-product-area wrapper-padding-3 pt-115 pb-115">
		<div class="container-fluid">
			<div class="section-title-6 text-center mb-50">
				<h2>Popular Product</h2>
			</div>
			<div class="product-style">
				<div class="popular-product-active owl-carousel">
					@foreach ($products as $product)
						@php
							$product = $product->parent ?: $product;	
						@endphp
						<div class="product-wrapper">
							<div class="product-img">
								<a href="{{ url('product/'. $product->slug) }}">
									@if ($product->productImages->first())
										<img src="{{ Storage::url($product->productImages->first()->path) }}" alt="{{ $product->name }}">
									@else
										<img src="{{ asset('themes/ezone/assets/img/product/fashion-colorful/1.jpg') }}" alt="{{ $product->name }}">
									@endif
								</a>
								<div class="product-action">
									<a class="animate-left add-to-fav" title="Wishlist"  product-slug="{{ $product->slug }}" href="">
										<i class="pe-7s-like"></i>
									</a>
									<a class="animate-top add-to-card" title="Add To Cart" href="" product-id="{{ $product->id }}" product-type="{{ $product->type }}" product-slug="{{ $product->slug }}">
										<i class="pe-7s-cart"></i>
									</a>
									<a class="animate-right quick-view" title="Quick View" product-slug="{{ $product->slug }}" href="">
										<i class="pe-7s-look"></i>
									</a>
								</div>
							</div>
							<div class="funiture-product-content text-center">
								<h4><a href="{{ url('product/'. $product->slug) }}">{{ $product->name }}</a></h4>
								<span>Rp{{ number_format($product->priceLabel(), 0, ",", ".") }}</span>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	@endif
    @if ($categories)
	<div class="popular-product-area wrapper-padding-3 pt-10 pb-115">
		@foreach($categories as $category)
		<div class="container-fluid" style="margin-bottom: 150px;">
			<div class="section-title-6 text-center mb-50">
				<h2>{{ $category->name }}</h2>
			</div>
			<div class="product-style">
				<div class="popular-product-active owl-carousel">
					@foreach ($category->products as $product)
						@php
							$product = $product->parent ?: $product;	
						@endphp
						<div class="product-wrapper">
							<div class="product-img">
								<a href="{{ url('product/'. $product->slug) }}">
									@if ($product->productImages->first())
										<img src="{{ Storage::url($product->productImages->first()->path) }}" alt="{{ $product->name }}">
									@else
										<img src="{{ asset('themes/ezone/assets/img/product/fashion-colorful/1.jpg') }}" alt="{{ $product->name }}">
									@endif
								</a>
								<div class="product-action">
									<a class="animate-left add-to-fav" title="Wishlist"  product-slug="{{ $product->slug }}" href="">
										<i class="pe-7s-like"></i>
									</a>
									<a class="animate-top add-to-card" title="Add To Cart" href="" product-id="{{ $product->id }}" product-type="{{ $product->type }}" product-slug="{{ $product->slug }}">
										<i class="pe-7s-cart"></i>
									</a>
									<a class="animate-right quick-view" title="Quick View" product-slug="{{ $product->slug }}" href="">
										<i class="pe-7s-look"></i>
									</a>
								</div>
							</div>
							<div class="funiture-product-content text-center">
								<h4><a href="{{ url('product/'. $product->slug) }}">{{ $product->name }}</a></h4>
								<span>Rp{{ number_format($product->priceLabel(), 0, ",", ".") }}</span>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
		@endforeach
	</div>
	@endif
    <!-- end -->
@endsection