@php
    $formatAddress = function (?string $address, $user = null) {
        $parts = array_values(array_filter(array_map('trim', explode(',', (string) $address))));

        return [
            'name' => $parts[0] ?? ($user?->name ?? '-'),
            'phone' => $parts[1] ?? ($user?->phone ?? '-'),
            'address' => count($parts) > 2 ? implode(', ', array_slice($parts, 2)) : ($parts[2] ?? '-'),
            'email' => $user?->email ?? '-',
        ];
    };

    $formatDecimal = function ($value) {
        $formatted = number_format((float) $value, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    };

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

        return '-';
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
            return '-';
        }

        return $kg >= 1 ? $formatDecimal($kg) . ' kg' : $formatDecimal($kg * 1000) . ' g';
    };

    $wordsBelowThousand = function ($num) use (&$wordsBelowThousand) {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $num = (int) $num;
        if ($num < 20) {
            return $ones[$num];
        }
        if ($num < 100) {
            return trim($tens[intdiv($num, 10)] . ' ' . $ones[$num % 10]);
        }

        return trim($ones[intdiv($num, 100)] . ' Hundred ' . $wordsBelowThousand($num % 100));
    };

    $amountInWords = function ($amount) use ($wordsBelowThousand) {
        $num = (int) round((float) $amount);
        if ($num === 0) {
            return 'Zero Rupees Only';
        }

        $parts = [];
        $crore = intdiv($num, 10000000);
        $num %= 10000000;
        $lakh = intdiv($num, 100000);
        $num %= 100000;
        $thousand = intdiv($num, 1000);
        $num %= 1000;

        if ($crore) $parts[] = $wordsBelowThousand($crore) . ' Crore';
        if ($lakh) $parts[] = $wordsBelowThousand($lakh) . ' Lakh';
        if ($thousand) $parts[] = $wordsBelowThousand($thousand) . ' Thousand';
        if ($num) $parts[] = $wordsBelowThousand($num);

        return trim(implode(' ', $parts)) . ' Rupees Only';
    };

    $billing = $formatAddress($order->bill_addr, $order->user);
    $shipping = $formatAddress($order->ship_addr, $order->user);
    $invoiceNo = 'PRK-INV-' . $order->created_at->format('Y') . '-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoiceNo }}</title>
    <style>
        :root { --green: #1f5c2d; --gold: #d9ad28; --light: #eef7ee; --line: #dfe8dc; --text: #2d332c; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f5f3ed; color: var(--text); font-family: Arial, Helvetica, sans-serif; }
        .no-print { position: sticky; top: 0; z-index: 5; padding: 12px 20px; background: #173f1e; text-align: right; }
        .btn { border: 0; border-radius: 8px; padding: 10px 16px; font-weight: 800; cursor: pointer; }
        .btn-print { background: #fff; color: var(--green); }
        .invoice-shell { width: 980px; max-width: calc(100% - 32px); margin: 22px auto; background: #fff; box-shadow: 0 10px 35px rgba(0,0,0,.08); }
        .gold-strip { height: 18px; background: var(--gold); }
        .invoice { padding: 56px 70px 40px; }
        .top { display: flex; justify-content: space-between; gap: 40px; align-items: center; padding-bottom: 30px; border-bottom: 3px solid var(--green); }
        .brand { display: flex; align-items: center; gap: 22px; }
        .brand-mark { width: 108px; height: 132px; flex: 0 0 auto; display: grid; place-items: center; background: #fff; }
        .brand-logo { display: block; width: 100%; height: 100%; object-fit: contain; }
        .brand-name { font-size: 30px; color: var(--green); font-weight: 500; margin-bottom: 14px; }
        .tagline { color: #7a8278; font-size: 16px; }
        .title { text-align: right; }
        .title h1 { margin: 0 0 22px; color: var(--green); font-size: 44px; letter-spacing: .03em; }
        .title p { margin: 0; color: #747b70; font-size: 15px; font-weight: 900; letter-spacing: .06em; }
        .info-row { display: grid; grid-template-columns: 1.25fr .95fr; gap: 14px; margin-top: 22px; }
        .box { border: 1px solid var(--line); background: #fffefb; padding: 18px; min-height: 120px; }
        .from-label, .addr-title { font-size: 15px; letter-spacing: .03em; color: #444; text-transform: uppercase; margin-bottom: 6px; }
        .from-name, .addr-name { font-size: 22px; color: #333; margin-bottom: 8px; }
        .kv { width: 100%; border-collapse: collapse; }
        .kv th, .kv td { border: 1px solid var(--line); padding: 12px 14px; font-size: 16px; }
        .kv th { background: var(--light); color: var(--green); text-align: left; width: 38%; }
        .kv td { text-align: right; font-weight: 700; }
        .addr-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0; margin-top: 20px; border: 1px solid var(--line); }
        .addr { padding: 18px; min-height: 132px; }
        .addr + .addr { border-left: 1px solid var(--line); }
        .addr p { margin: 6px 0; font-size: 17px; line-height: 1.35; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 24px; }
        .items th { background: var(--green); color: #fff; padding: 14px 12px; text-align: left; font-size: 15px; letter-spacing: .02em; }
        .items td { border: 1px solid var(--line); padding: 14px 12px; vertical-align: top; font-size: 15px; }
        .items .center { text-align: center; }
        .items .right { text-align: right; }
        .item-name { font-size: 17px; margin-bottom: 6px; }
        .muted { color: #7a8278; font-size: 13px; line-height: 1.45; }
        .totals-row { display: grid; grid-template-columns: 1fr 420px; gap: 28px; margin-top: 22px; align-items: start; }
        .payment-box { border: 1px solid var(--line); padding: 18px; min-height: 160px; }
        .payment-box h3 { margin: 0 0 10px; font-size: 17px; font-weight: 500; }
        .payment-box p { margin: 8px 0; font-size: 16px; }
        .total-table { width: 100%; border-collapse: collapse; }
        .total-table td { padding: 10px 0; font-size: 17px; }
        .total-table td:last-child { text-align: right; }
        .grand td { border-top: 3px solid var(--green); background: var(--light); color: var(--green); font-size: 24px; font-weight: 900; padding: 12px; }
        .words { margin-top: 20px; display: flex; justify-content: space-between; gap: 20px; background: var(--light); border: 1px solid var(--line); padding: 15px 16px; color: var(--green); font-weight: 900; }
        .footer-note { display: grid; grid-template-columns: 1fr 260px; gap: 40px; margin-top: 32px; padding-top: 18px; border-top: 1px solid var(--line); color: #737b71; font-size: 14px; line-height: 1.45; }
        .sign { text-align: right; color: #333; font-size: 17px; }
        .thanks { margin-top: 45px; text-align: center; color: var(--green); font-weight: 900; }
        @media print {
            @page { margin: 0; size: A4 portrait; }
            body { background: #fff; }
            .no-print { display: none !important; }
            .invoice-shell { width: 100%; max-width: 100%; margin: 0; box-shadow: none; }
            .invoice { padding: 44px 58px 34px; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Download / Print Invoice</button>
    </div>
    <main class="invoice-shell">
        <div class="gold-strip"></div>
        <section class="invoice">
            <div class="top">
                <div class="brand">
                    <div class="brand-mark">
                        <img src="{{ asset('assets/prakruti-logo.png') }}" alt="Prakruti Organic company logo" class="brand-logo">
                    </div>
                    <div>
                        <div class="brand-name">Prakruti Organic</div>
                        <div class="tagline">Crafted Traditionally. Tested Scientifically.</div>
                    </div>
                </div>
                <div class="title">
                    <h1>TAX INVOICE</h1>
                    <p>ORIGINAL FOR RECIPIENT</p>
                </div>
            </div>

            <div class="info-row">
                <div class="box">
                    <div class="from-label">From</div>
                    <div class="from-name">Prakruti Organic</div>
                    <div>Registered Address: [Add registered business address]</div>
                    <div style="margin-top:6px;">GSTIN: [Add GSTIN] &nbsp; | &nbsp; State: Gujarat</div>
                </div>
                <table class="kv">
                    <tr><th>Invoice No.</th><td>{{ $invoiceNo }}</td></tr>
                    <tr><th>Order No.</th><td>{{ $order->order_num }}</td></tr>
                    <tr><th>Invoice Date</th><td>{{ $order->created_at->format('d M Y') }}</td></tr>
                    <tr><th>Order Date</th><td>{{ $order->created_at->format('d M Y, h:i A') }}</td></tr>
                </table>
            </div>

            <div class="addr-row">
                <div class="addr">
                    <div class="addr-title">Bill To</div>
                    <p class="addr-name">{{ $billing['name'] }}</p>
                    <p>{{ $billing['address'] }}</p>
                    <p>Phone: {{ $billing['phone'] }}</p>
                    <p>Email: {{ $billing['email'] }}</p>
                </div>
                <div class="addr">
                    <div class="addr-title">Ship To</div>
                    <p class="addr-name">{{ $shipping['name'] }}</p>
                    <p>{{ $shipping['address'] }}</p>
                    <p>Phone: {{ $shipping['phone'] }}</p>
                </div>
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th style="width:48px;">#</th>
                        <th>Item Description</th>
                        <th style="width:90px;">HSN</th>
                        <th style="width:70px;">Qty</th>
                        <th style="width:135px;">Pack / Weight</th>
                        <th style="width:110px;">Rate</th>
                        <th style="width:80px;">GST</th>
                        <th style="width:120px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        @php
                            $variation = $resolveVariation($item);
                            $pack = $packageLabel($item);
                            $singleWeightKg = $weightInKg($pack, $variation?->weight ?? $item->product?->weight);
                            $totalWeight = $formatWeight($singleWeightKg ? $singleWeightKg * (int) $item->qty : null);
                        @endphp
                        <tr>
                            <td class="center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="item-name">{{ $item->product?->name ?? $item->product_name ?? 'Product' }}</div>
                                <div class="muted">SKU: {{ $item->sku ?? $item->product?->sku ?? '-' }}</div>
                                <div class="muted">Selected Package: {{ $pack }} · Total Weight: {{ $totalWeight }}</div>
                            </td>
                            <td class="center">{{ $item->product?->hsn_code ?? '-' }}</td>
                            <td class="center">{{ $item->qty }}</td>
                            <td>{{ $item->qty }} × {{ $pack }}<br><span class="muted">Total: {{ $totalWeight }}</span></td>
                            <td class="right">₹{{ number_format($item->price, 2) }}</td>
                            <td class="center">{{ $item->gst_pct ? $formatDecimal($item->gst_pct) . '%' : '-' }}</td>
                            <td class="right"><strong>₹{{ number_format($item->line_total ?? ($item->qty * $item->price), 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals-row">
                <div class="payment-box">
                    <h3>Payment & Order Details</h3>
                    <p>Payment Method: {{ strtoupper(str_replace('_', ' ', $order->payment_method ?? $order->payment?->method ?? 'COD')) }}</p>
                    <p>Payment Status: {{ ucfirst($order->pay_status) }}</p>
                    <p>Order Status: {{ ucfirst($order->status) }}</p>
                    <p>Order Type: {{ ($order->order_type ?? 'standard') === 'family_pack' ? 'Family Pack' : 'Standard' }}</p>
                </div>
                <table class="total-table">
                    <tr><td>Subtotal</td><td>₹{{ number_format($order->subtotal, 2) }}</td></tr>
                    @if($order->discount > 0)
                        <tr><td>Discount</td><td>- ₹{{ number_format($order->discount, 2) }}</td></tr>
                    @endif
                    <tr><td>GST</td><td>₹{{ number_format($order->gst_amt, 2) }}</td></tr>
                    <tr><td>Shipping</td><td>₹{{ number_format($order->ship_charge, 2) }}</td></tr>
                    <tr class="grand"><td>Grand Total</td><td>₹{{ number_format($order->total, 2) }}</td></tr>
                </table>
            </div>

            <div class="words">
                <span>Amount in words</span>
                <span>{{ $amountInWords($order->total) }}</span>
            </div>

            <div class="footer-note">
                <div>
                    <strong>Notes & Terms</strong><br>
                    1. This is a computer-generated invoice and does not require a physical signature.<br>
                    2. Returns and replacements are subject to the store's applicable policy.<br>
                    3. Please quote the invoice or order number for any support request.
                </div>
                <div class="sign">
                    For Prakruti Organic<br><br><br>
                    Authorized Signatory
                </div>
            </div>

            <div class="thanks">Thank you for choosing Prakruti Organic &nbsp; • &nbsp; Crafted Traditionally. Tested Scientifically.</div>
        </section>
    </main>
</body>
</html>
