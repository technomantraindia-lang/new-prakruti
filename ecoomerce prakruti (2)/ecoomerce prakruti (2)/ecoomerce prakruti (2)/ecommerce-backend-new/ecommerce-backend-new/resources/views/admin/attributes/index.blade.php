@extends('admin.layouts.app')
@section('title', 'Attributes')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Product Attributes</h2>
    <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Attribute</a>
</div>
<div class="card"><div class="card-body">
    <table class="table table-hover">
        <thead><tr><th>Name</th><th>Values</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($attributes as $attribute)
            <tr>
                <td><strong>{{ $attribute->name }}</strong></td>
                <td>{{ $attribute->values_count ?? 0 }} values</td>
                <td><span class="badge bg-{{ $attribute->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($attribute->status) }}</span></td>
                <td>
                    <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted">No attributes found</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $attributes->links() }}
</div></div>
@endsection
