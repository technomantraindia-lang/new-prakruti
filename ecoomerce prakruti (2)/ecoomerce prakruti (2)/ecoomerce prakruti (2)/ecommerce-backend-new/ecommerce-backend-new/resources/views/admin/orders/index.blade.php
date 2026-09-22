@extends('admin.layouts.app')
@section('title', 'Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="mb-1">Orders</h2>
        <p class="text-muted mb-0">Manage and track all customer orders</p>
    </div>
</div>

<div class="row mb-4">
    @php
        $pending = \App\Models\Order::where('status','pending')->count();
        $processing = \App\Models\Order::where('status','processing')->count();
        $delivered = \App\Models\Order::where('status','delivered')->count();
        $revenue = \App\Models\Order::where('pay_status','paid')->sum('total');
    @endphp
    <div class="col-md-3 mb-2"><div class="card border-0 shadow-sm"><div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#fef3c7;"><i class="fas fa-clock text-warning"></i></div>
        <div><small class="text-muted">Pending</small><h5 class="mb-0">{{ $pending }}</h5></div>
    </div></div></div>
    <div class="col-md-3 mb-2"><div class="card border-0 shadow-sm"><div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#dbeafe;"><i class="fas fa-cog text-primary"></i></div>
        <div><small class="text-muted">Processing</small><h5 class="mb-0">{{ $processing }}</h5></div>
    </div></div></div>
    <div class="col-md-3 mb-2"><div class="card border-0 shadow-sm"><div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#d1fae5;"><i class="fas fa-check text-success"></i></div>
        <div><small class="text-muted">Delivered</small><h5 class="mb-0">{{ $delivered }}</h5></div>
    </div></div></div>
    <div class="col-md-3 mb-2"><div class="card border-0 shadow-sm"><div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#e8f5e9;"><i class="fas fa-rupee-sign" style="color:#2d5a27;"></i></div>
        <div><small class="text-muted">Revenue</small><h5 class="mb-0">₹{{ number_format($revenue, 0) }}</h5></div>
    </div></div></div>
</div>

<div class="card border-0 shadow-sm mb-4"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Search order # or customer name..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['pending','processing','packed','shipped','delivered','cancelled','failed','refunded'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary" style="background:linear-gradient(135deg,#2d5a27,#193a15);border:none;"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div></div>

<div class="card border-0 shadow-sm"><div class="card-body p-0 table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none fw-bold" style="color:#2d5a27;">#{{ $order->order_num }}</a>
                    @if(($order->order_type ?? 'standard') === 'family_pack')
                        <br><span class="badge bg-success mt-1"><i class="fas fa-users"></i> Family Pack</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $order->user?->name ?? 'N/A' }}</strong>
                    @if($order->user)<br><small class="text-muted">{{ $order->user->email }}</small>@endif
                </td>
                <td><span class="badge bg-light text-dark">{{ $order->items_count ?? $order->items()->count() }} items</span></td>
                <td><strong>₹{{ number_format($order->total, 2) }}</strong></td>
                <td><span class="badge bg-{{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></td>
                <td><span class="badge bg-{{ $order->payStatusBadgeClass() }}">{{ ucfirst($order->pay_status) }}</span></td>
                <td>
                    <small>{{ $order->created_at->format('M d, Y') }}</small><br>
                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                </td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2d5a27,#193a15);">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-shopping-cart fa-2x mb-2 d-block"></i>No orders found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($orders->hasPages())<div class="card-footer bg-white">{{ $orders->links() }}</div>@endif
</div>
@endsection
