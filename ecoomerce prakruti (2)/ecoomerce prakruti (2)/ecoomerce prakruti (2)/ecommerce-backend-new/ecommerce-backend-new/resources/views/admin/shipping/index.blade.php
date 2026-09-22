@extends('admin.layouts.app')
@section('title', 'Shipping')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Shipping Methods</h2>
    <a href="{{ route('admin.shipping.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Method</a>
</div>
<div class="card"><div class="card-body">
    <table class="table table-hover">
        <thead><tr><th>Name</th><th>Charge (₹)</th><th>Free Above (₹)</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($methods as $method)
            <tr>
                <td><strong>{{ $method->name }}</strong></td>
                <td>₹{{ number_format($method->charge, 2) }}</td>
                <td>{{ $method->min_free_order ? '₹'.number_format($method->min_free_order, 0) : '-' }}</td>
                <td><span class="badge bg-{{ $method->status==='active'?'success':'secondary' }}">{{ ucfirst($method->status) }}</span></td>
                <td>
                    <a href="{{ route('admin.shipping.edit', $method) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.shipping.destroy', $method) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted">No shipping methods found</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $methods->links() }}
</div></div>
@endsection
