<?php

namespace App\Services;

use App\Models\Product;

class GstService
{
    public function productGstPercentage(Product $product): float
    {
        $category = $product->subSubCategory ?: $product->subCategory ?: $product->category;

        return (float) ($category?->gst_percentage ?? 0);
    }

    public function calculateTotal(array $lines, float $subtotal, float $discount): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        return round(array_reduce($lines, function ($total, $line) use ($subtotal, $discount) {
            $lineTotal = (float) ($line['line_total'] ?? 0);
            $gstPct = (float) ($line['gst_pct'] ?? 0);
            $lineDiscount = $discount > 0 ? round($discount * ($lineTotal / $subtotal), 2) : 0.0;
            $taxableLine = max(0, $lineTotal - $lineDiscount);

            return $total + round($taxableLine * ($gstPct / 100), 2);
        }, 0.0), 2);
    }
}
