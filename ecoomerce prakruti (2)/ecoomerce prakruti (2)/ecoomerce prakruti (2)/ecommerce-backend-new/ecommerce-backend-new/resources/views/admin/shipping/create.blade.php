@extends('admin.layouts.app')
@section('title', 'Add Shipping Method')
@section('content')
<h2 class="mb-4">Add Shipping Method</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.shipping.store') }}" method="POST">@csrf
        @include('admin.shipping._form')
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('admin.shipping.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
