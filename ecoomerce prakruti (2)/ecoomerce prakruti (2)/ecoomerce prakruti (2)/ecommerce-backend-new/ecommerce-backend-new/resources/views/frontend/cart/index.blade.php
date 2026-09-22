@extends('frontend.layouts.app')
@section('title', 'Your Cart')
@section('content')
<h2 class="mb-4">Your Cart</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(count($items))
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item['product']->image)
                                                <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" style="width:60px;height:60px;object-fit:cover;" class="rounded">
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $item['product']->name }}</div>
                                                <small class="text-muted">{{ $item['product']->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item['qty'] }}</td>
                                    <td>₹{{ number_format($item['price'], 2) }}</td>
                                    <td>₹{{ number_format($item['line_total'], 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.remove', $item['product']) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5>Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>₹{{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <p class="text-muted mb-3">This is a simple cart step. Hook your checkout flow here when ready.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mb-2">Continue Shopping</a>
                    <button class="btn btn-primary w-100" type="button" disabled>Checkout Coming Soon</button>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">Your cart is empty.</div>
    <a href="{{ route('products.index') }}" class="btn btn-primary">Shop Products</a>
@endif
@endsection
