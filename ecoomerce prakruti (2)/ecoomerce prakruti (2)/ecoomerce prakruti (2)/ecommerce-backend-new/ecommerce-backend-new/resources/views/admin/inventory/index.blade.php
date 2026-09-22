@extends('admin.layouts.app')
@section('title', 'Inventory')
@section('content')
<h2 class="mb-4">Inventory Management</h2>
<div class="row mb-4">
    <div class="col-md-4"><div class="card"><div class="card-body"><h6 class="text-muted">Low Stock</h6><h3 class="text-warning">{{ $lowStockCount }}</h3></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><h6 class="text-muted">Out of Stock</h6><h3 class="text-danger">{{ $outOfStockCount }}</h3></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><h6 class="text-muted">Total Products</h6><h3>{{ $products->total() }}</h3></div></div></div>
</div>
<form method="GET" class="mb-3"><input type="text" name="search" class="form-control d-inline-block w-auto" placeholder="Search..." value="{{ request('search') }}"> <button class="btn btn-primary">Search</button></form>
<div class="card mb-4"><div class="card-body table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Current Stock</th><th>Update Stock</th></tr></thead>
        <tbody>
            @foreach($products as $product)
            <tr class="{{ $product->stock_qty==0?'table-danger':($product->isLowStock()?'table-warning':'') }}">
                <td><strong>{{ $product->name }}</strong></td>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->category?->name ?? '-' }}</td>
                <td><strong>{{ $product->stock_qty }}</strong> {{ $product->unit ?? 'pcs' }}</td>
                <td>
                    <form action="{{ route('admin.inventory.update', $product) }}" method="POST" class="d-flex gap-1 flex-wrap">
                        @csrf @method('PUT')
                        <select name="action" class="form-select form-select-sm" style="width:90px">
                            <option value="add">Add</option><option value="reduce">Reduce</option><option value="set">Set</option>
                        </select>
                        <input type="number" name="quantity" class="form-control form-control-sm" style="width:80px" min="0" required>
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="Note" style="width:120px">
                        <button class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $products->links() }}
</div></div>
@if($recentLogs->count())
<div class="card"><div class="card-header"><h5>Recent Inventory Logs</h5></div><div class="card-body p-0">
    <table class="table table-sm mb-0">
        <thead><tr><th>Date</th><th>Product</th><th>Type</th><th>Old</th><th>Change</th><th>New</th><th>By</th><th>Note</th></tr></thead>
        <tbody>
            @foreach($recentLogs as $log)
            <tr>
                <td>{{ $log->created_at->format('M d, H:i') }}</td>
                <td>{{ $log->product?->name }}</td>
                <td>{{ ucfirst($log->type) }}</td>
                <td>{{ $log->old_qty }}</td>
                <td>{{ $log->change_qty > 0 ? '+' : '' }}{{ $log->change_qty }}</td>
                <td>{{ $log->new_qty }}</td>
                <td>{{ $log->user?->name ?? 'System' }}</td>
                <td>{{ $log->note ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div></div>
@endif
@endsection
