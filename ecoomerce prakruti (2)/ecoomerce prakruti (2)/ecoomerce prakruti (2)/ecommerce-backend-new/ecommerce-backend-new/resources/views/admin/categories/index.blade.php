@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Category</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr><th>Name</th><th>GST</th><th>Products</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td>{{ number_format((float) ($category->gst_percentage ?? 0), 2) }}%</td>
                    <td>{{ $category->main_products_count }}</td>
                    <td><span class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($category->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No categories found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $categories->links() }}
    </div>
</div>
@endsection
