@extends('frontend.layouts.app')
@section('title', $product->name)
@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
    @if($product->category)
        <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
    @endif
    @if($product->subCategory)
        <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->subCategory->slug) }}">{{ $product->subCategory->name }}</a></li>
    @endif
    @if($product->subSubCategory)
        <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->subSubCategory->slug) }}">{{ $product->subSubCategory->name }}</a></li>
    @endif
    <li class="breadcrumb-item active">{{ $product->name }}</li>
</ol></nav>
<div class="row">
    <div class="col-md-5">
        @php
            $mainImage = $product->image_url ?: $product->images->first()?->image_url;
        @endphp
        @if($mainImage)
            <img id="mainImg" src="{{ $mainImage }}" class="img-fluid rounded mb-3 w-100" style="max-height:400px;object-fit:cover;" alt="{{ $product->name }}">
            <div class="d-flex gap-2 flex-wrap">
                <img src="{{ $mainImage }}" class="rounded border" style="width:80px;height:80px;object-fit:cover;cursor:pointer;" alt="{{ $product->name }} main photo" onclick="document.getElementById('mainImg').src=this.src">
                @foreach($product->images as $img)
                    <img src="{{ $img->image_url }}" class="rounded border" style="width:80px;height:80px;object-fit:cover;cursor:pointer;" alt="{{ $product->name }} thumbnail" onclick="document.getElementById('mainImg').src=this.src">
                @endforeach
            </div>
        @endif
    </div>
    <div class="col-md-7">
        <h2>{{ $product->name }}</h2>
        <p class="text-muted">SKU: {{ $product->sku }} | Category: {{ $product->category->name }}@if($product->brand) | Brand: {{ $product->brand->name }}@endif</p>
        <div class="mb-3">
            @if($product->sale_price)<del class="text-muted fs-5">₹{{ number_format($product->price,2) }}</del>@endif
            <span class="fs-2 price ms-2">₹{{ number_format($product->display_price,2) }}</span>
            <span class="text-muted">/ {{ $product->unit }}</span>
        </div>
        <p>{{ $product->short_desc }}</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <form action="{{ route('cart.add', $product) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary">Add to Cart</button>
            </form>
            <form action="{{ route('cart.buyNow', $product) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Buy Now</button>
            </form>
        </div>
        <ul class="list-unstyled mb-3">
            <li><strong>Stock:</strong> @if($product->isInStock())<span class="text-success">{{ $product->stock_qty }} available</span>@else<span class="text-danger">Out of Stock</span>@endif</li>
            <li><strong>Min Order:</strong> {{ $product->min_order_qty }} {{ $product->unit }}</li>
            <li><strong>GST:</strong> {{ $product->gst_percentage }}%</li>
            @if($product->weight)<li><strong>Weight:</strong> {{ $product->weight }} kg</li>@endif
        </ul>
        @if($product->featured)<span class="badge bg-warning text-dark mb-3">Best Seller</span>@endif
        <hr>
        <h5>Description</h5>
        <div class="text-muted">{!! nl2br(e($product->description)) !!}</div>
    </div>
</div>
@if($related->count())
    <hr class="my-5">
    <h4>Related Products</h4>
    <div class="row">
        @foreach($related as $rel)
            <div class="col-md-3 mb-3">
                <div class="card product-card h-100">
                    @if($rel->image)<img src="{{ $rel->image_url }}" class="card-img-top" style="height:140px;object-fit:cover;" alt="{{ $rel->name }}">@endif
                    <div class="card-body">
                        <h6>{{ $rel->name }}</h6>
                        <span class="price">₹{{ number_format($rel->display_price,0) }}</span>
                        <a href="{{ route('products.show', $rel->slug) }}" class="btn btn-sm btn-primary float-end">View</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
