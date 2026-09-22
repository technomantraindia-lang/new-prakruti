@extends('admin.layouts.app')

@section('title', 'Order ' . $order->order_num)

@push('styles')
<style>
    .od-page { width: 100%; max-width: 1460px; margin: 0 auto; }
    .od-topbar {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 12px; margin-bottom: 20px;
    }
    .od-topbar h2 { font-size: 1.5rem; margin: 0; color: #1a1a2e; }
    .od-topbar .sub { color: #888; font-size: 13px; }
    .od-actions .btn { font-size: 13px; }

    /* Horizontal status pipeline */
    .od-pipeline {
        display: flex; align-items: center; gap: 0;
        background: #fff; border-radius: 12px; padding: 16px 20px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06); margin-bottom: 20px;
        overflow-x: auto;
        border: 1px solid rgba(45,90,39,.08);
    }
    .od-pipeline .pipe-step {
        display: flex; align-items: center; gap: 8px; white-space: nowrap;
        padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
        color: #aaa; background: #f4f4f8;
    }
    .od-pipeline .pipe-step.done { background: #e8f5e9; color: #2d5a27; }
    .od-pipeline .pipe-step.active { background: linear-gradient(135deg,#2d5a27,#193a15); color: #fff; }
    .od-pipeline .pipe-arrow { color: #ccc; margin: 0 4px; font-size: 10px; }

    /* Info tiles row */
    .od-tiles { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 20px; }
    @media(max-width:992px){ .od-tiles{ grid-template-columns: repeat(2,1fr); } }
    @media(max-width:576px){ .od-tiles{ grid-template-columns: 1fr; } }
    .od-tile {
        background: #fff; border-radius: 16px; padding: 18px 20px;
        box-shadow: 0 8px 28px rgba(31,41,55,.06);
        border-left: 4px solid #2d5a27;
    }
    .od-tile.tile-green { border-left-color: #10b981; }
    .od-tile.tile-blue { border-left-color: #3b82f6; }
    .od-tile.tile-orange { border-left-color: #f59e0b; }
    .od-tile .tile-label { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #999; margin-bottom: 4px; }
    .od-tile .tile-value { font-size: 15px; font-weight: 700; color: #1a1a2e; }
    .od-tile .tile-sub { font-size: 12px; color: #888; margin-top: 2px; }

    /* Main grid */
    .od-grid { display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 22px; align-items: start; }
    @media(max-width:992px){ .od-grid{ grid-template-columns: 1fr; } }

    .od-panel {
        background: #fff; border-radius: 16px;
        box-shadow: 0 8px 28px rgba(31,41,55,.06); overflow: hidden;
        border: 1px solid rgba(45,90,39,.08);
    }
    .od-panel-head {
        padding: 16px 22px; border-bottom: 1px solid #f0f0f0;
        font-weight: 800; font-size: 14px; color: #1f2937;
        display: flex; justify-content: space-between; align-items: center;
        background: linear-gradient(135deg,#ffffff,#f8fbf6);
    }

    /* Product rows (not table) */
    .od-product-row {
        display: flex; align-items: center; gap: 16px;
        padding: 18px 22px; border-bottom: 1px solid #f5f5f5;
    }
    .od-product-row:last-child { border-bottom: none; }
    .od-product-row img, .od-product-row .no-img {
        width: 72px; height: 72px; border-radius: 14px; object-fit: contain; flex-shrink: 0;
        border: 1px solid #e8e3d8;
        background: #fff;
    }
    .od-product-row img { padding: 6px; }
    .od-product-row .no-img {
        background: #f0f0f5; display: flex; align-items: center; justify-content: center; color: #bbb;
    }
    .od-product-row .p-info { flex: 1; min-width: 0; }
    .od-product-row .p-name { font-weight: 800; font-size: 15px; color: #111827; margin-bottom: 4px; }
    .od-product-row .p-meta { font-size: 12px; color: #6b7280; line-height: 1.5; }
    .od-product-row .p-weight-line { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
    .od-product-row .p-weight-pill {
        display: inline-flex; align-items: center; gap: 6px;
        border-radius: 999px; background: #eef7ee; color: #245420;
        border: 1px solid #d8ead5; padding: 5px 10px;
        font-size: 12px; font-weight: 800;
    }
    .od-product-row .p-weight-pill.warn { background: #fff7ed; color: #9a4b00; border-color: #fed7aa; }
    .od-product-row .p-qty {
        font-size: 13px; color: #2d5a27; min-width: 54px; text-align: center;
        background: #edf7ed; border-radius: 999px; padding: 6px 10px; font-weight: 800;
    }
    .od-product-row .p-price { font-weight: 900; font-size: 15px; color: #2d5a27; min-width: 90px; text-align: right; }

    /* Summary box */
    .od-summary { padding: 18px 22px; background: #faf8f3; }
    .od-summary .sum-row {
        display: flex; justify-content: space-between; padding: 7px 0; font-size: 14px; color: #4b5563;
    }
    .od-summary .sum-row.total {
        border-top: 2px solid #2d5a27; margin-top: 8px; padding-top: 10px;
        font-size: 18px; font-weight: 900; color: #111827;
    }
    .od-summary .sum-row.total span:last-child { color: #2d5a27; }

    /* Right column panels */
    .od-side-panel { margin-bottom: 16px; }
    .od-side-panel:last-child { margin-bottom: 0; }
    .od-side-body { padding: 16px 20px; }

    .od-customer-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: linear-gradient(135deg,#2d5a27,#193a15);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px; flex-shrink: 0;
    }
    .od-info-line { font-size: 13px; color: #555; margin-bottom: 6px; }
    .od-info-line i { width: 20px; color: #2d5a27; }

    /* Activity log */
    .od-log-item {
        display: flex; gap: 10px; padding: 10px 0;
        border-bottom: 1px solid #f5f5f5; font-size: 12px;
    }
    .od-log-item:last-child { border-bottom: none; }
    .od-log-dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: #2d5a27; margin-top: 4px; flex-shrink: 0;
    }

    /* Update form */
    .od-update-form .form-label { font-size: 12px; font-weight: 600; color: #555; margin-bottom: 4px; }
    .od-update-form .form-control, .od-update-form .form-select { font-size: 13px; border-radius: 8px; }
    .od-update-form .btn-save {
        background: linear-gradient(135deg,#2d5a27,#193a15);
        border: none; color: #fff; font-weight: 600; border-radius: 8px; padding: 10px;
    }
    .od-address-text {
        color: #4b5563;
        line-height: 1.7;
        min-height: 74px;
    }
    .od-address-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }
    .od-address-field {
        background: #faf8f3;
        border: 1px solid #eee7d8;
        border-radius: 12px;
        padding: 12px 14px;
        min-height: 74px;
    }
    .od-address-label {
        display: block;
        font-size: 11px;
        color: #8a7f6f;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 800;
        margin-bottom: 6px;
    }
    .od-address-value {
        color: #1f2937;
        font-weight: 700;
        line-height: 1.5;
        word-break: break-word;
    }
    @media(max-width:768px){ .od-address-grid{ grid-template-columns: 1fr; } }
    .od-side-panel { position: relative; }
    @media(max-width:1199px){
        .od-grid { grid-template-columns: minmax(0, 1fr) 340px; gap: 16px; }
        .od-product-row { gap: 12px; padding-left: 16px; padding-right: 16px; }
    }
    @media(max-width:991px){
        .od-grid { grid-template-columns: 1fr; }
        .od-side-panel { margin-bottom: 16px; }
    }
    @media(max-width:576px){
        .od-page { width: 100%; }
        .od-topbar { align-items: flex-start; margin-bottom: 14px; }
        .od-topbar h2 { font-size: 1.2rem; }
        .od-actions { width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .od-actions .btn { width: 100%; }
        .od-pipeline { padding: 12px; margin-bottom: 14px; }
        .od-pipeline .pipe-step { padding: 6px 10px; font-size: 11px; }
        .od-product-row { align-items: start; display: grid; grid-template-columns: 64px minmax(0, 1fr); gap: 10px 12px; padding: 14px 12px; }
        .od-product-row img, .od-product-row .no-img { width: 64px; height: 64px; grid-row: span 2; }
        .od-product-row .p-info { min-width: 0; }
        .od-product-row .p-name { font-size: 14px; }
        .od-product-row .p-weight-line { gap: 5px; margin-top: 6px; }
        .od-product-row .p-weight-pill { padding: 4px 7px; font-size: 10px; }
        .od-product-row .p-qty { grid-column: 2; grid-row: 2; justify-self: start; min-width: 0; padding: 5px 9px; }
        .od-product-row .p-price { grid-column: 2; grid-row: 2; justify-self: end; min-width: 0; align-self: center; font-size: 14px; }
        .od-panel-head { padding: 14px 12px; }
        .od-summary { padding: 14px 12px; }
    }

    @media print {
        .sidebar, .navbar, .no-print { display: none !important; }
        .col-md-10 { width: 100% !important; max-width: 100% !important; }
        .od-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $isCancelled = in_array($order->status, ['cancelled', 'failed', 'refunded']);
    $stepIndex = $order->statusStepIndex();
    $steps = \App\Models\Order::statusSteps();
    $formatAddress = function (?string $address) {
        $parts = array_values(array_filter(array_map('trim', explode(',', (string) $address))));

        return [
            'name' => $parts[0] ?? '—',
            'phone' => $parts[1] ?? '—',
            'address' => count($parts) > 2 ? implode(', ', array_slice($parts, 2)) : ($parts[2] ?? '—'),
        ];
    };
    $billingAddress = $formatAddress($order->bill_addr);
    $shippingAddress = $formatAddress($order->ship_addr);
    $resolveVariation = function ($item) {
        if ($item->variation) {
            return $item->variation;
        }

        $variations = $item->product?->variations;
        if ($variations && $variations->count() === 1) {
            return $variations->first();
        }

        return null;
    };
    $formatDecimal = function ($value) {
        $formatted = number_format((float) $value, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    };
    $packageLabel = function ($item) use ($resolveVariation, $formatDecimal) {
        $variation = $resolveVariation($item);
        if ($variation?->attr_val) {
            return $variation->attr_val;
        }
        if ($variation?->weight) {
            return $formatDecimal($variation->weight) . ' kg';
        }
        if ($item->product?->weight) {
            return $formatDecimal($item->product->weight) . ' kg';
        }

        return null;
    };
    $weightInKg = function (?string $label, $fallbackWeight = null) {
        if ($label && preg_match('/([\d.]+)\s*(kg|kgs|kilogram|kilograms|g|gm|gram|grams)\b/i', $label, $matches)) {
            $value = (float) $matches[1];
            $unit = strtolower($matches[2]);
            return in_array($unit, ['g', 'gm', 'gram', 'grams'], true) ? $value / 1000 : $value;
        }

        return $fallbackWeight ? (float) $fallbackWeight : null;
    };
    $formatWeight = function ($kg) use ($formatDecimal) {
        if ($kg === null || $kg <= 0) {
            return null;
        }

        return $kg >= 1 ? $formatDecimal($kg) . ' kg' : $formatDecimal($kg * 1000) . ' g';
    };
@endphp

<div class="od-page">

    {{-- Top Bar --}}
    <div class="od-topbar no-print">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left"></i> All Orders</a>
            <h2 class="mt-1">Order #{{ $order->order_num }}</h2>
            @if(($order->order_type ?? 'standard') === 'family_pack')
                <span class="badge bg-success mt-2"><i class="fas fa-users"></i> Family Pack Order</span>
            @endif
            <div class="sub">Placed on {{ $order->created_at->format('l, F d, Y \a\t h:i A') }}</div>
        </div>
        <div class="od-actions d-flex gap-2">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-success btn-sm"><i class="fas fa-file-invoice"></i> Download Invoice</a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>

    {{-- Status Pipeline --}}
    @if(!$isCancelled)
    <div class="od-pipeline no-print">
        @foreach($steps as $i => $step)
            @if($i > 0)<span class="pipe-arrow"><i class="fas fa-chevron-right"></i></span>@endif
            <div class="pipe-step {{ $i < $stepIndex ? 'done' : ($i === $stepIndex ? 'active' : '') }}">
                @if($i < $stepIndex)<i class="fas fa-check-circle"></i>
                @elseif($step === 'pending')<i class="fas fa-clock"></i>
                @elseif($step === 'processing')<i class="fas fa-cog"></i>
                @elseif($step === 'packed')<i class="fas fa-box"></i>
                @elseif($step === 'shipped')<i class="fas fa-truck"></i>
                @else<i class="fas fa-check"></i>
                @endif
                {{ ucfirst($step) }}
            </div>
        @endforeach
    </div>
    @else
    <div class="alert alert-danger mb-3 no-print"><i class="fas fa-ban"></i> Order is <strong>{{ ucfirst($order->status) }}</strong></div>
    @endif

    {{-- Info Tiles --}}
    <div class="od-tiles">
        <div class="od-tile">
            <div class="tile-label">Customer</div>
            <div class="tile-value">{{ $order->user?->name ?? 'Guest' }}</div>
            <div class="tile-sub">{{ $order->user?->email ?? '' }}</div>
        </div>
        <div class="od-tile tile-green">
            <div class="tile-label">Payment</div>
            <div class="tile-value">{{ ucfirst($order->pay_status) }}</div>
            <div class="tile-sub">{{ $order->payment ? strtoupper(str_replace('_',' ',$order->payment->method)) : 'N/A' }} @if($order->payment?->txn_id) · {{ $order->payment->txn_id }}@endif</div>
        </div>
        <div class="od-tile tile-blue">
            <div class="tile-label">Shipping</div>
            <div class="tile-value">{{ $order->shippingMethod?->name ?? ($order->courier ?? 'Standard') }}</div>
            <div class="tile-sub">₹{{ number_format($order->ship_charge, 2) }} @if($order->tracking_num)· Track: {{ $order->tracking_num }}@endif</div>
        </div>
        <div class="od-tile tile-orange">
            <div class="tile-label">Order Total</div>
            <div class="tile-value">₹{{ number_format($order->total, 2) }}</div>
            <div class="tile-sub">{{ $order->items->sum('qty') }} item(s) · <span class="badge bg-{{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="od-grid">

        {{-- LEFT: Products + Summary --}}
        <div>
            <div class="od-panel mb-3">
                <div class="od-panel-head">
                    <span><i class="fas fa-box-open text-primary"></i> Products Ordered</span>
                    <span class="badge bg-light text-dark">{{ $order->items->count() }} items</span>
                </div>
                @foreach($order->items as $item)
                @php
                    $variation = $resolveVariation($item);
                    $packLabel = $packageLabel($item);
                    $singleWeightKg = $weightInKg($packLabel, $variation?->weight ?? $item->product?->weight);
                    $totalWeightLabel = $formatWeight($singleWeightKg ? $singleWeightKg * (int) $item->qty : null);
                    $productImage = $item->product?->image ?: $item->product?->images?->first()?->image;
                    $productImageUrl = $productImage ? route('media.file', ['path' => $productImage]) : null;
                @endphp
                <div class="od-product-row">
                    @if($productImageUrl)
                    <img src="{{ $productImageUrl }}" alt="{{ $item->product?->name ?? $item->product_name }}">
                    @else
                    <div class="no-img"><i class="fas fa-image"></i></div>
                    @endif
                    <div class="p-info">
                        <div class="p-name">{{ $item->product?->name ?? $item->product_name ?? 'Product Removed' }}</div>
                        <div class="p-weight-line">
                            @if($packLabel)
                                <span class="p-weight-pill"><i class="fas fa-weight-hanging"></i> Package: {{ $packLabel }}</span>
                                <span class="p-weight-pill"><i class="fas fa-calculator"></i> Qty {{ $item->qty }} × {{ $packLabel }}{{ $totalWeightLabel ? ' = ' . $totalWeightLabel : '' }}</span>
                            @else
                                <span class="p-weight-pill warn"><i class="fas fa-exclamation-triangle"></i> Package/weight not recorded</span>
                            @endif
                        </div>
                        <div class="p-meta">
                            SKU: {{ $item->product?->sku ?? $item->sku ?? '-' }}
                            @if($item->variation?->attr_val) · Package: {{ $item->variation->attr_val }} @endif
                            @if($item->gst_pct > 0) · GST {{ $item->gst_pct }}%@endif
                        </div>
                    </div>
                    <div class="p-qty">× {{ $item->qty }}</div>
                    <div class="p-price">₹{{ number_format($item->qty * $item->price, 2) }}</div>
                </div>
                @endforeach
                <div class="od-summary">
                    <div class="sum-row"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div>
                    @if($order->discount > 0)
                    <div class="sum-row"><span>Discount</span><span class="text-success">-₹{{ number_format($order->discount, 2) }}</span></div>
                    @endif
                    <div class="sum-row"><span>GST</span><span>₹{{ number_format($order->gst_amt, 2) }}</span></div>
                    <div class="sum-row"><span>Shipping</span><span>₹{{ number_format($order->ship_charge, 2) }}</span></div>
                    <div class="sum-row total"><span>Grand Total</span><span>₹{{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>

            {{-- Addresses --}}
            @if($order->bill_addr || $order->ship_addr)
            <div class="row g-3 mb-3">
                @if($order->bill_addr)
                <div class="col-md-6">
                    <div class="od-panel"><div class="od-panel-head"><span><i class="fas fa-file-invoice text-success"></i> Billing Address</span></div>
                    <div class="od-side-body od-address-text">
                        <div class="od-address-grid">
                            <div class="od-address-field"><span class="od-address-label">Name</span><div class="od-address-value">{{ $billingAddress['name'] }}</div></div>
                            <div class="od-address-field"><span class="od-address-label">Contact No</span><div class="od-address-value">{{ $billingAddress['phone'] }}</div></div>
                            <div class="od-address-field"><span class="od-address-label">Address</span><div class="od-address-value">{{ $billingAddress['address'] }}</div></div>
                        </div>
                    </div></div>
                </div>
                @endif
                @if($order->ship_addr)
                <div class="col-md-6">
                    <div class="od-panel"><div class="od-panel-head"><span><i class="fas fa-map-marker-alt text-success"></i> Shipping Address</span></div>
                    <div class="od-side-body od-address-text">
                        <div class="od-address-grid">
                            <div class="od-address-field"><span class="od-address-label">Name</span><div class="od-address-value">{{ $shippingAddress['name'] }}</div></div>
                            <div class="od-address-field"><span class="od-address-label">Contact No</span><div class="od-address-value">{{ $shippingAddress['phone'] }}</div></div>
                            <div class="od-address-field"><span class="od-address-label">Address</span><div class="od-address-value">{{ $shippingAddress['address'] }}</div></div>
                        </div>
                    </div></div>
                </div>
                @endif
            </div>
            @endif

            {{-- Activity Log --}}
            @if($order->statusHistory->count())
            <div class="od-panel">
                <div class="od-panel-head"><i class="fas fa-history"></i> Activity Log</div>
                <div class="od-side-body">
                    @foreach($order->statusHistory as $history)
                    <div class="od-log-item">
                        <div class="od-log-dot"></div>
                        <div>
                            <strong>{{ ucfirst($history->status) }}</strong>
                            <span class="text-muted">· {{ $history->created_at->format('M d, Y h:i A') }}</span>
                            @if($history->note)<br><span class="text-muted">{{ $history->note }}</span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Customer + Update --}}
        <div>
            {{-- Customer --}}
            <div class="od-panel od-side-panel">
                <div class="od-panel-head"><i class="fas fa-user"></i> Customer Details</div>
                <div class="od-side-body">
                    @if($order->user)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="od-customer-avatar">{{ strtoupper(substr($order->user->name, 0, 1)) }}</div>
                        <div>
                            <strong>{{ $order->user->name }}</strong><br>
                            <small class="text-muted">Member since {{ $order->user->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                    <div class="od-info-line"><i class="fas fa-envelope"></i> {{ $order->user->email }}</div>
                    <div class="od-info-line"><i class="fas fa-phone"></i> {{ $order->user->phone ?? 'N/A' }}</div>
                    <a href="{{ route('admin.customers.show', $order->user) }}" class="btn btn-sm btn-outline-primary w-100 mt-3 no-print">View Profile</a>
                    @else
                    <p class="text-muted mb-0">Guest order</p>
                    @endif
                </div>
            </div>

            {{-- Order Meta --}}
            <div class="od-panel od-side-panel">
                <div class="od-panel-head"><i class="fas fa-info-circle"></i> Order Info</div>
                <div class="od-side-body small">
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Order ID</span><span>#{{ $order->id }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Order Type</span><span>{{ ($order->order_type ?? 'standard') === 'family_pack' ? 'Family Pack' : 'Standard' }}</span></div>
                </div>
            </div>

            {{-- Update Form --}}
            <div class="od-panel od-side-panel no-print">
                <div class="od-panel-head" style="background:linear-gradient(135deg,#2d5a2710,#193a1510);">
                    <span style="color:#2d5a27;"><i class="fas fa-edit"></i> Manage Order</span>
                </div>
                <div class="od-side-body od-update-form">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    @foreach(['pending','processing','packed','shipped','delivered','cancelled','failed','refunded'] as $s)
                                    <option value="{{ $s }}" @selected($order->status===$s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Payment</label>
                                <select name="pay_status" class="form-select form-select-sm">
                                    @foreach(['pending','paid','failed','refunded'] as $s)
                                    <option value="{{ $s }}" @selected($order->pay_status===$s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" name="tracking_num" class="form-control form-control-sm" value="{{ old('tracking_num', $order->tracking_num) }}" placeholder="TRACK123456">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Courier</label>
                            <input type="text" name="courier" class="form-control form-control-sm" value="{{ old('courier', $order->courier) }}" placeholder="Delhivery, BlueDart...">
                        </div>
                        <button type="submit" class="btn btn-save w-100"><i class="fas fa-save"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
