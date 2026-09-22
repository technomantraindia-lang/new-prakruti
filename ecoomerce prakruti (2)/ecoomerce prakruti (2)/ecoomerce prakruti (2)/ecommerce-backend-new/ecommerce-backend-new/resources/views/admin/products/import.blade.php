@extends('admin.layouts.app')
@section('title', 'Import Products')
@section('content')
<h2 class="mb-4">Import Products (CSV)</h2>
<div class="card"><div class="card-body">
    <p class="text-muted">CSV columns: <code>name, sku, category, price, sale_price, stock_qty, unit, status, gst_percentage, slug</code></p>
    <form action="{{ route('admin.products.importStore') }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="mb-3"><label class="form-label">CSV File *</label><input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required></div>
        <button type="submit" class="btn btn-primary">Import</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
    <hr>
    <h6>Sample CSV:</h6>
    <pre class="bg-light p-3 rounded">name,sku,category,price,sale_price,stock_qty,unit,status,gst_percentage
Wheat Flour 1kg,WF001,Grocery,45,40,100,kg,active,5
Sugar 500g,SUG001,Grocery,30,,50,packet,active,5</pre>
</div></div>
@endsection
