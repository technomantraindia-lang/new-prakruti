@extends('admin.layouts.app')
@section('title', 'Add Product')
@section('content')
<h2 class="mb-4">Add Product</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">@csrf
        @include('admin.products._form')
        <button type="submit" class="btn btn-primary">Create Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
