@extends('admin.layouts.app')
@section('title', 'Customers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Customers</h2>
    <form class="d-flex" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search name, email, phone..." value="{{ request('search') }}">
        <button class="btn btn-outline-primary ms-2">Search</button>
    </form>
</div>
<div class="card"><div class="card-body">
    <table class="table table-hover">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
            @forelse ($customers as $customer)
            <tr>
                <td><strong>{{ $customer->name }}</strong></td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone ?? '-' }}</td>
                <td>{{ $customer->orders_count }}</td>
                <td><span class="badge bg-{{ $customer->status==='active'?'success':'secondary' }}">{{ ucfirst($customer->status) }}</span></td>
                <td><a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted">No customers found</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $customers->links() }}
</div></div>
@endsection
