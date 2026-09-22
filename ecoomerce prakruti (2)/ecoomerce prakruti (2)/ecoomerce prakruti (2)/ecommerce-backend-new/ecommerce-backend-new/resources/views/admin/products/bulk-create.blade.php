@extends('admin.layouts.app')
@section('title', 'Bulk Add Products')
@section('content')
<h2 class="mb-4">Bulk Add Products</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.products.bulkStore') }}" method="POST" id="bulkForm">@csrf
        <div id="productRows">
            <div class="row product-row border rounded p-3 mb-3 bg-light">
                <div class="col-md-3 mb-2"><label class="form-label">Name *</label><input type="text" name="products[0][name]" class="form-control" required></div>
                <div class="col-md-2 mb-2"><label class="form-label">SKU *</label><input type="text" name="products[0][sku]" class="form-control" required></div>
                <div class="col-md-2 mb-2"><label class="form-label">Category *</label>
                    <select name="products[0][category_id]" class="form-select" required>
                        @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-2"><label class="form-label">Price</label><input type="number" step="0.01" name="products[0][price]" class="form-control" value="0"></div>
                <div class="col-md-1 mb-2"><label class="form-label">Stock</label><input type="number" name="products[0][stock_qty]" class="form-control" value="0"></div>
                <div class="col-md-1 mb-2"><label class="form-label">Unit</label>
                    <select name="products[0][unit]" class="form-select">@foreach($units as $u)<option value="{{ $u }}">{{ $u }}</option>@endforeach</select>
                </div>
                <div class="col-md-1 mb-2"><label class="form-label">GST%</label><input type="number" name="products[0][gst_percentage]" class="form-control" value="5"></div>
                <div class="col-md-1 mb-2"><label class="form-label">Status</label>
                    <select name="products[0][status]" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-outline-primary mb-3" id="addRow"><i class="fas fa-plus"></i> Add More Product</button>
        <div><button type="submit" class="btn btn-primary">Save All Products</button> <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a></div>
    </form>
</div></div>
<script>
let rowIndex = 1;
const categoryOptions = `@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach`;
const unitOptions = `@foreach($units as $u)<option value="{{ $u }}">{{ $u }}</option>@endforeach`;
document.getElementById('addRow').addEventListener('click', function() {
    const html = `<div class="row product-row border rounded p-3 mb-3 bg-light">
        <div class="col-md-3 mb-2"><label class="form-label">Name *</label><input type="text" name="products[${rowIndex}][name]" class="form-control" required></div>
        <div class="col-md-2 mb-2"><label class="form-label">SKU *</label><input type="text" name="products[${rowIndex}][sku]" class="form-control" required></div>
        <div class="col-md-2 mb-2"><label class="form-label">Category *</label><select name="products[${rowIndex}][category_id]" class="form-select" required>${categoryOptions}</select></div>
        <div class="col-md-1 mb-2"><label class="form-label">Price</label><input type="number" step="0.01" name="products[${rowIndex}][price]" class="form-control" value="0"></div>
        <div class="col-md-1 mb-2"><label class="form-label">Stock</label><input type="number" name="products[${rowIndex}][stock_qty]" class="form-control" value="0"></div>
        <div class="col-md-1 mb-2"><label class="form-label">Unit</label><select name="products[${rowIndex}][unit]" class="form-select">${unitOptions}</select></div>
        <div class="col-md-1 mb-2"><label class="form-label">GST%</label><input type="number" name="products[${rowIndex}][gst_percentage]" class="form-control" value="5"></div>
        <div class="col-md-1 mb-2"><label class="form-label">Status</label><select name="products[${rowIndex}][status]" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    </div>`;
    document.getElementById('productRows').insertAdjacentHTML('beforeend', html);
    rowIndex++;
});
</script>
@endsection
