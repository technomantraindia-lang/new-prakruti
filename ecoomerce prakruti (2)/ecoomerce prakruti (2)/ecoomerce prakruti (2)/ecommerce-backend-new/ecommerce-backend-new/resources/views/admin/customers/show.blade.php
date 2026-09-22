@extends('admin.layouts.app')
@section('title', $customer->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $customer->name }}</h2>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Back</a>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4"><div class="card-header"><h5>Customer Info</h5></div><div class="card-body">
            <p><strong>Email:</strong> {{ $customer->email }}<br>
            <strong>Phone:</strong> {{ $customer->phone ?? '-' }}<br>
            <strong>Status:</strong> <span class="badge bg-{{ $customer->status==='active'?'success':'secondary' }}">{{ ucfirst($customer->status) }}</span><br>
            <strong>Joined:</strong> {{ $customer->created_at->format('M d, Y') }}</p>
        </div></div>
        @if($customer->addresses->count())
        <div class="card"><div class="card-header"><h5>Addresses</h5></div><div class="card-body">
            @foreach($customer->addresses as $addr)
            <div class="mb-2 p-2 border rounded">
                <small class="text-muted">{{ ucfirst($addr->type) }}</small><br>
                {{ $addr->fname }} {{ $addr->lname }}<br>
                {{ $addr->address }}, {{ $addr->city }}, {{ $addr->state }} {{ $addr->zip }}
            </div>
            @endforeach
        </div></div>
        @endif
    </div>
    <div class="col-md-8">
        <div class="card"><div class="card-header"><h5>Recent Orders</h5></div><div class="card-body">
            <table class="table table-sm">
                <thead><tr><th>Order #</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                    @forelse ($customer->orders as $order)
                    <tr>
                        <td>{{ $order->order_num }}</td>
                        <td>₹{{ number_format($order->total, 2) }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>
</div>
@endsection
