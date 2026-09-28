@extends('admin.layouts.app')

@section('title', 'Farm Gallery Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Farm Gallery Categories</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.farm-gallery.index') }}" class="btn btn-outline-secondary">Farm Gallery</a>
        <a href="{{ route('admin.farm-gallery-categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Category</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td><span class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($category->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.farm-gallery-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.farm-gallery-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No farm gallery categories found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $categories->links() }}
    </div>
</div>
@endsection
