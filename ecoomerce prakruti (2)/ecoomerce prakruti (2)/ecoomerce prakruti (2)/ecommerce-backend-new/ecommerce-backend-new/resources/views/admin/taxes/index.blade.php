@extends('admin.layouts.app')
@section('title', 'GST/Tax')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>GST / Tax Rates</h2>
    <a href="{{ route('admin.taxes.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Tax Rate</a>
</div>
<div class="card"><div class="card-body">
    <table class="table table-hover">
        <thead><tr><th>Name</th><th>Percentage</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($taxes as $tax)
            <tr>
                <td><strong>{{ $tax->name }}</strong></td>
                <td>{{ $tax->percentage }}%</td>
                <td>{{ $tax->desc ?? '-' }}</td>
                <td><span class="badge bg-{{ $tax->status==='active'?'success':'secondary' }}">{{ ucfirst($tax->status) }}</span></td>
                <td>
                    <a href="{{ route('admin.taxes.edit', $tax) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.taxes.destroy', $tax) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted">No tax rates found</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $taxes->links() }}
</div></div>
@endsection
