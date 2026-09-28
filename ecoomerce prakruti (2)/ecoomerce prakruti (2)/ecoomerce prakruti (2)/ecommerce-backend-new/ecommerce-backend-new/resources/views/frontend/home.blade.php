@extends('frontend.layouts.app')
@section('title', 'Home')
@section('content')
@if($banners->count())
<div id="bannerCarousel" class="carousel slide mb-5 rounded overflow-hidden" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($banners as $i => $banner)
        <div class="carousel-item {{ $i===0?'active':'' }}">
            <img src="{{ asset('storage/'.$banner->image) }}" class="d-block w-100" style="max-height:400px;object-fit:cover;" alt="{{ $banner->title }}">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                <h3>{{ $banner->title }}</h3>
                @if($banner->button_text && $banner->link)
                <a href="{{ $banner->link }}" class="btn btn-primary">{{ $banner->button_text }}</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @if($banners->count() > 1)
    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    @endif
</div>
@endif

<div class="text-center mb-5">
    <h1>Welcome to {{ $settings['company_name'] ?? 'Prakruti Organic' }}</h1>
    <p class="lead text-muted">Fresh groceries delivered to your doorstep</p>
</div>

@if($categories->count())
<h3 class="mb-3">Shop by Category</h3>
<div class="row mb-5">
    @foreach($categories as $cat)
    <div class="col-md-2 col-4 mb-3">
        <a href="{{ route('categories.show', $cat->slug) }}" class="text-decoration-none text-dark">
            <div class="card product-card text-center p-3">
                <i class="fas fa-shopping-basket fa-2x text-primary mb-2"></i>
                <h6>{{ $cat->name }}</h6>
                <small class="text-muted">{{ $cat->products_count }} items</small>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endif

@if($featuredProducts->count())
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Best Sellers</h3>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
</div>
<div class="row">
    @foreach($featuredProducts as $product)
    <div class="col-md-3 col-6 mb-4">
        <div class="card product-card h-100">
            @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="{{ $product->name }}">
            @else
            <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px;"><i class="fas fa-image fa-3x text-muted"></i></div>
            @endif
            <div class="card-body">
                <h6 class="card-title">{{ $product->name }}</h6>
                <p class="small text-muted">{{ \Illuminate\Support\Str::limit($product->short_desc, 50) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($product->sale_price)<del class="small text-muted">₹{{ number_format($product->price,0) }}</del>@endif
                        <span class="price">₹{{ number_format($product->display_price,0) }}</span>
                    </div>
                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-primary">View</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
