@extends('admin.layouts.app')
@section('title', 'Edit Product')
@section('content')
<h2 class="mb-4">Edit Product</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
        @include('admin.products._form')
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
