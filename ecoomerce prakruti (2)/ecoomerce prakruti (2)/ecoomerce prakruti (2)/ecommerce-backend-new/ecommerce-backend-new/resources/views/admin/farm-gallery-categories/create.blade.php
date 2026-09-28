@extends('admin.layouts.app')

@section('title', 'Add Farm Gallery Category')

@section('content')
<h2 class="mb-4">Add Farm Gallery Category</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.farm-gallery-categories.store') }}" method="POST">
        @csrf
        @include('admin.farm-gallery-categories._form')
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('admin.farm-gallery-categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
