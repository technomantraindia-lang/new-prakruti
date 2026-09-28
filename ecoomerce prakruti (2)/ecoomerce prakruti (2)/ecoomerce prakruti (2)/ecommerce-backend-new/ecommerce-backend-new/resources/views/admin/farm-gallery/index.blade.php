@extends('admin.layouts.app')

@section('title', 'Farm Gallery')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Farm Gallery</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.farm-gallery-categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-tags"></i> Manage Categories</a>
        <a href="{{ route('admin.farm-gallery.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Gallery Item</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Videos</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                <tr>
                    <td>
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="rounded border" style="width: 80px; height: 56px; object-fit: cover;">
                    </td>
                    <td>
                        <strong>{{ $item->title }}</strong>
                        <div class="text-muted small">{{ $item->tag }} | {{ $item->location }}</div>
                    </td>
                    <td>{{ $item->category_name }}</td>
                    <td>{{ $item->videos_count }}</td>
                    <td><span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($item->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.farm-gallery.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.farm-gallery.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this gallery item?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No farm gallery items found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection
