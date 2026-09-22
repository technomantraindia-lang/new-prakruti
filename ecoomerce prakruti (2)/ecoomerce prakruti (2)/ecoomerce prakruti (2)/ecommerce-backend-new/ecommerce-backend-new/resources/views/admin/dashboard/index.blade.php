@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Products</h6>
                    <h2>{{ $stats['total_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Orders</h6>
                    <h2>{{ $stats['total_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Customers</h6>
                    <h2>{{ $stats['total_customers'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Sales</h6>
                    <h2>₹{{ number_format($stats['total_sales'], 0) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Pending Orders</h6>
                    <h2 class="text-warning">{{ $stats['pending_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Completed Orders</h6>
                    <h2 class="text-success">{{ $stats['completed_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Low Stock Products</h6>
                    <h2 class="text-danger">{{ $stats['low_stock_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Today Orders</h6>
                    <h2>{{ $stats['today_orders'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Recent Orders</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                    <tr>
                        <td><strong>{{ $order->order_num }}</strong></td>
                        <td>{{ $order->user?->name ?? 'Guest' }}</td>
                        <td>₹{{ number_format($order->total, 2) }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No orders yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
