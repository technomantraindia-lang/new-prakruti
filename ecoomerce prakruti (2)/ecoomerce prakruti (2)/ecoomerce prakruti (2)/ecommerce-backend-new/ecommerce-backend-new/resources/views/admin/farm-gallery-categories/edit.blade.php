@extends('admin.layouts.app')

@section('title', 'Edit Farm Gallery Category')

@section('content')
<h2 class="mb-4">Edit Farm Gallery Category</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.farm-gallery-categories.update', $category) }}" method="POST">
        @csrf @method('PUT')
        @include('admin.farm-gallery-categories._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.farm-gallery-categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
