<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\ShippingMethod;

class ShippingChargeService
{
    public function calculate(float $subtotal, ?ShippingMethod $method = null): array
    {
        $charge = $method
            ? (float) $method->charge
            : max(0, (float) Setting::get('standard_shipping_charge', 50));

        $threshold = $method
            ? (float) ($method->min_free_order ?? 0)
            : max(0, (float) Setting::get('free_shipping_min_amount', 500));

        $freeApplied = $threshold > 0 && $subtotal >= $threshold;
        $finalCharge = ($subtotal <= 0 || $freeApplied) ? 0.0 : $charge;
        $remaining = $threshold > 0 ? max(0, $threshold - $subtotal) : 0.0;

        return [
            'charge' => round($finalCharge, 2),
            'base_charge' => round($charge, 2),
            'free_shipping_min_amount' => round($threshold, 2),
            'free_shipping_remaining' => round($remaining, 2),
            'free_shipping_unlocked' => $freeApplied || $subtotal <= 0,
            'label' => $freeApplied
                ? 'Free shipping unlocked'
                : ($threshold > 0 ? "Free shipping above Rs. {$threshold}" : null),
        ];
    }
}
