@extends('admin.layouts.app')

@section('title', 'Brands')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0"><i class="fas fa-copyright me-2 text-primary"></i>Brands</h3>
    @if(auth()->user()->hasPermission('brands.manage'))
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Brand
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search brands by name or slug..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-grow-1"><i class="fas fa-search me-1"></i> Filter</button>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                        <tr>
                            <td>#{{ $brand->id }}</td>
                            <td>
                                @if($brand->image)
                                    <img src="{{ route('media.file', ['path' => $brand->image]) }}" alt="{{ $brand->name }}" class="rounded border" style="width: 40px; height: 40px; object-fit: contain;">
                                @else
                                    <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $brand->name }}</td>
                            <td><code>{{ $brand->slug }}</code></td>
                            <td><span class="badge bg-info text-dark">{{ $brand->products_count }} Products</span></td>
                            <td>
                                <span class="badge {{ $brand->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($brand->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('brands.manage'))
                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete or deactivate this brand?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No brands found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($brands->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $brands->links() }}
        </div>
    @endif
</div>
@endsection
