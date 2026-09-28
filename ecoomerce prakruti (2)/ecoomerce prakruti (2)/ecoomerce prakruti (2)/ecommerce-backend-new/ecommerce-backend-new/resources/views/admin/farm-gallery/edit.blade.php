@extends('admin.layouts.app')

@section('title', 'Edit Farm Gallery Item')

@section('content')
<h2 class="mb-4">Edit Farm Gallery Item</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.farm-gallery.update', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.farm-gallery._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.farm-gallery.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
