@extends('admin.layouts.app')
@section('title', $product->name)

@push('styles')
<style>
    .admin-product-hero { display:grid; grid-template-columns: 430px 1fr; gap:28px; align-items:start; }
    .admin-product-image { background:#fff; border:1px solid #e3ddcf; border-radius:18px; padding:26px; min-height:430px; display:flex; align-items:center; justify-content:center; box-shadow:0 8px 24px rgba(45,90,39,.08); }
    .admin-product-image img { max-height:380px; object-fit:contain; }
    .admin-product-thumbs { display:flex; gap:10px; flex-wrap:wrap; margin-top:14px; }
    .admin-product-thumbs img { width:72px; height:72px; object-fit:cover; border:1px solid #e3ddcf; border-radius:10px; padding:4px; background:#fff; }
    .admin-product-panel { background:#fff; border:1px solid #e3ddcf; border-radius:18px; padding:26px; box-shadow:0 8px 24px rgba(45,90,39,.06); }
    .admin-product-kicker { color:#528b4b; text-transform:uppercase; letter-spacing:.08em; font-size:12px; font-weight:800; }
    .admin-product-title { font-family:Georgia,serif; color:#193a15; font-size:36px; line-height:1.1; margin:8px 0 14px; }
    .admin-price-row { display:flex; gap:12px; align-items:baseline; margin:18px 0; }
    .admin-price-main { color:#8b4513; font-size:32px; font-weight:800; }
    .admin-price-old { color:#999; font-size:20px; text-decoration:line-through; font-weight:700; }
    .admin-stat-grid { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:12px; margin:22px 0; }
    .admin-stat { background:#faf8f3; border:1px solid #e3ddcf; border-radius:12px; padding:14px; }
    .admin-stat span { display:block; color:#777; font-size:12px; font-weight:700; }
    .admin-stat strong { display:block; color:#193a15; font-size:18px; margin-top:4px; }
    .admin-product-desc { color:#555; line-height:1.7; }
    @media(max-width:992px){ .admin-product-hero{ grid-template-columns:1fr; } .admin-stat-grid{ grid-template-columns:repeat(2,1fr); } }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left"></i> Back to Products</a>
        <h2 class="mb-0 mt-1">{{ $product->name }}</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Product</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="admin-product-hero mb-4">
    <div>
        <div class="admin-product-image">
            @if($product->image)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            @else
                <div class="text-muted text-center"><i class="fas fa-image fa-3x mb-2"></i><br>No image</div>
            @endif
        </div>
        @if($product->images->count())
            <div class="admin-product-thumbs">
                @foreach($product->images as $img)
                    <img src="{{ $img->image_url }}" alt="{{ $product->name }} gallery image">
                @endforeach
            </div>
        @endif
    </div>

    <div class="admin-product-panel">
        <div class="admin-product-kicker">{{ $product->category?->name ?? 'Uncategorized' }}</div>
        <h1 class="admin-product-title">{{ $product->name }}</h1>
        <p class="text-muted mb-2">SKU: <strong>{{ $product->sku }}</strong> | Slug: {{ $product->slug }}</p>

        <div class="admin-price-row">
            <span class="admin-price-main">&#8377;{{ number_format($product->sale_price ?: $product->price, 2) }}</span>
            @if($product->sale_price)
                <span class="admin-price-old">&#8377;{{ number_format($product->price, 2) }}</span>
            @endif
        </div>

        <div class="d-flex gap-2 flex-wrap mb-3">
            <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($product->status) }}</span>
            @if($product->featured)<span class="badge bg-warning text-dark">Best Seller</span>@endif
            <span class="badge bg-light text-dark">Category GST {{ number_format((float) ($product->subSubCategory?->gst_percentage ?? $product->subCategory?->gst_percentage ?? $product->category?->gst_percentage ?? 0), 2) }}%</span>
        </div>

        <div class="admin-stat-grid">
            <div class="admin-stat"><span>Physical Stock</span><strong>{{ $product->stock_qty }}</strong></div>
            <div class="admin-stat"><span>Available</span><strong>{{ $product->available_stock }}</strong></div>
            <div class="admin-stat"><span>Unit</span><strong>{{ $product->unit }}</strong></div>
            <div class="admin-stat"><span>Min Order</span><strong>{{ $product->min_order_qty }}</strong></div>
        </div>

        <p class="admin-product-desc">{{ $product->short_desc }}</p>
        @if($product->description)
            <div class="admin-product-desc">{!! nl2br(e($product->description)) !!}</div>
        @endif

        <hr>
        <p class="mb-1"><strong>Category Path:</strong> {{ $product->category?->name }} @if($product->subCategory) &gt; {{ $product->subCategory->name }}@endif @if($product->subSubCategory) &gt; {{ $product->subSubCategory->name }}@endif</p>
        <p class="mb-0"><strong>Brand:</strong> {{ $product->brand?->name ?? 'N/A' }} | <strong>HSN:</strong> {{ $product->hsn_code ?? '-' }}</p>
    </div>
</div>

@if($product->inventoryLogs->count())
<div class="card"><div class="card-header"><h5 class="mb-0">Inventory History</h5></div><div class="card-body p-0">
    <table class="table table-sm mb-0">
        <thead><tr><th>Date</th><th>Type</th><th>Old</th><th>Change</th><th>New</th><th>Note</th></tr></thead>
        <tbody>
            @foreach($product->inventoryLogs as $log)
            <tr>
                <td>{{ $log->created_at->format('M d, Y') }}</td>
                <td>{{ ucfirst($log->type) }}</td>
                <td>{{ $log->old_qty }}</td>
                <td>{{ $log->change_qty > 0 ? '+' : '' }}{{ $log->change_qty }}</td>
                <td>{{ $log->new_qty }}</td>
                <td>{{ $log->note ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div></div>
@endif
@endsection
