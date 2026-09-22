@extends('frontend.layouts.app')
@section('title', 'Products')
@section('content')
<h2 class="mb-4">All Products</h2>
<form method="GET" class="row mb-4">
    <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
</form>
<div class="row">
    @forelse($products as $product)
    <div class="col-md-3 col-6 mb-4">
        <div class="card product-card h-100">
            @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height:180px;object-fit:cover;" alt="{{ $product->name }}">
            @else
            <div class="bg-light d-flex align-items-center justify-content-center" style="height:180px;"><i class="fas fa-image fa-3x text-muted"></i></div>
            @endif
            <div class="card-body d-flex flex-column">
                <span class="badge bg-light text-dark mb-1 align-self-start">{{ $product->category?->name }}</span>
                <h6>{{ $product->name }}</h6>
                <p class="small text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($product->short_desc, 60) }}</p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <div>
                        @if($product->sale_price)<del class="small text-muted">₹{{ number_format($product->price,0) }}</del>@endif
                        <span class="price">₹{{ number_format($product->display_price,0) }}</span>
                        <small class="text-muted">/ {{ $product->unit }}</small>
                    </div>
                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-primary">View</a>
                </div>
                @if(!$product->isInStock())<small class="text-danger">Out of Stock</small>@endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5"><h4>No products found</h4></div>
    @endforelse
</div>
{{ $products->links() }}
@endsection
