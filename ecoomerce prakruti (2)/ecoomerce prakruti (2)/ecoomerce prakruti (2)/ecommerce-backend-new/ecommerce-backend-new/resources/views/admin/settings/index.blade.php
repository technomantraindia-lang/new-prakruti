@extends('admin.layouts.app')
@section('title', 'Settings')
@section('content')
@php
    $discountRules = json_decode($settings['bill_discount_rules'] ?? '[]', true);
    if (!is_array($discountRules) || empty($discountRules)) {
        $discountRules = [[
            'min_amount' => $settings['bill_discount_min_amount'] ?? '',
            'percent' => $settings['bill_discount_percent'] ?? '',
        ]];
    }
@endphp
<h2 class="mb-4">Store Settings</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
        <h5 class="mb-3">General</h5>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Website Name</label><input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Logo</label><input type="file" name="logo" class="form-control" accept="image/*">
                @if(!empty($settings['logo']))<img src="{{ asset('storage/'.$settings['logo']) }}" class="mt-2" style="max-height:60px;">@endif
            </div>
            <div class="col-md-6 mb-3"><label class="form-label">Contact Email</label><input type="email" name="company_email" class="form-control" value="{{ $settings['company_email'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Phone Number</label><input type="text" name="company_phone" class="form-control" value="{{ $settings['company_phone'] ?? '' }}"></div>
            <div class="col-md-12 mb-3"><label class="form-label">Address</label><textarea name="company_address" class="form-control" rows="2">{{ $settings['company_address'] ?? '' }}</textarea></div>
            <div class="col-md-4 mb-3"><label class="form-label">GST Number</label><input type="text" name="gst_number" class="form-control" value="{{ $settings['gst_number'] ?? '' }}"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Currency</label><input type="text" name="currency" class="form-control" value="{{ $settings['currency'] ?? 'INR' }}"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Order Prefix</label><input type="text" name="order_prefix" class="form-control" value="{{ $settings['order_prefix'] ?? 'PRK' }}"></div>
        </div>

        <hr><h5 class="mb-3">Shipping & Billing</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Standard Shipping Charge (₹)</label>
                <input type="number" name="standard_shipping_charge" class="form-control" min="0" step="0.01" value="{{ $settings['standard_shipping_charge'] ?? '50' }}" placeholder="Example: 50">
                <small class="text-muted">This charge applies when the bill is below the free-shipping amount.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Free Shipping Above Bill Amount (₹)</label>
                <input type="number" name="free_shipping_min_amount" class="form-control" min="0" step="0.01" value="{{ $settings['free_shipping_min_amount'] ?? '500' }}" placeholder="Example: 500">
                <small class="text-muted">Example: enter 500 to make shipping free for bills of ₹500 and above.</small>
            </div>
        </div>

        <hr><h5 class="mb-3">Automatic Bill Discount</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" name="bill_discount_enabled" value="1" class="form-check-input" id="billDiscountEnabled" @checked(($settings['bill_discount_enabled'] ?? '0') == '1')>
                    <label class="form-check-label" for="billDiscountEnabled">Enable bill discount</label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-sm align-middle" id="discountRulesTable">
                        <thead>
                            <tr>
                                <th style="width:45%;">Minimum Bill Amount</th>
                                <th style="width:35%;">Discount Percentage</th>
                                <th style="width:20%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discountRules as $rule)
                                <tr class="discount-rule-row">
                                    <td><input type="number" name="bill_discount_min_amount[]" class="form-control" min="0" step="0.01" value="{{ $rule['min_amount'] ?? '' }}" placeholder="Example: 2000"></td>
                                    <td><input type="number" name="bill_discount_percent[]" class="form-control" min="0" max="100" step="0.01" value="{{ $rule['percent'] ?? '' }}" placeholder="Example: 3"></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-discount-rule">Remove</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addDiscountRule">Add More Discount</button>
                <p class="text-muted small mb-0 mt-2">Example: add 2000 = 3%, 5000 = 5%, 10000 = 8%. Checkout will use the highest matching slab.</p>
            </div>
        </div>

        <hr><h5 class="mb-3">Social Links</h5>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Facebook URL</label><input type="url" name="facebook_url" class="form-control" value="{{ $settings['facebook_url'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Instagram URL</label><input type="url" name="instagram_url" class="form-control" value="{{ $settings['instagram_url'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Twitter URL</label><input type="url" name="twitter_url" class="form-control" value="{{ $settings['twitter_url'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">WhatsApp Number</label><input type="text" name="whatsapp_number" class="form-control" value="{{ $settings['whatsapp_number'] ?? '' }}"></div>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#discountRulesTable tbody');
    const addButton = document.getElementById('addDiscountRule');

    function bindRemoveButtons() {
        document.querySelectorAll('.remove-discount-rule').forEach(function (button) {
            button.onclick = function () {
                const rows = tableBody.querySelectorAll('.discount-rule-row');
                if (rows.length > 1) {
                    button.closest('tr').remove();
                } else {
                    button.closest('tr').querySelectorAll('input').forEach(function (input) {
                        input.value = '';
                    });
                }
            };
        });
    }

    addButton.addEventListener('click', function () {
        const row = document.createElement('tr');
        row.className = 'discount-rule-row';
        row.innerHTML = `
            <td><input type="number" name="bill_discount_min_amount[]" class="form-control" min="0" step="0.01" placeholder="Example: 5000"></td>
            <td><input type="number" name="bill_discount_percent[]" class="form-control" min="0" max="100" step="0.01" placeholder="Example: 5"></td>
            <td><button type="button" class="btn btn-outline-danger btn-sm remove-discount-rule">Remove</button></td>
        `;
        tableBody.appendChild(row);
        bindRemoveButtons();
    });

    bindRemoveButtons();
});
</script>
@endsection
