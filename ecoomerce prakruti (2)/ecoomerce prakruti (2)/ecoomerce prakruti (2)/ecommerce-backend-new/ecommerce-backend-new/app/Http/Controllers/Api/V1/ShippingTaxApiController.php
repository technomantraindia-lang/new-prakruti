<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\Tax;

class ShippingTaxApiController extends Controller
{
    public function shippingMethods()
    {
        $methods = ShippingMethod::where('status', 'active')->get();
        return response()->json([
            'success' => true,
            'data' => $methods,
            'settings' => [
                'standard_shipping_charge' => (float) Setting::get('standard_shipping_charge', 50),
                'free_shipping_min_amount' => (float) Setting::get('free_shipping_min_amount', 500),
                'company_email' => Setting::get('company_email', 'info@prakrutiorganic.com'),
                'company_phone' => Setting::get('company_phone', ''),
                'company_address' => Setting::get('company_address', ''),
            ],
        ]);
    }

    public function taxes()
    {
        $taxes = Tax::where('status', 'active')->get();
        return response()->json([
            'success' => true,
            'data' => $taxes,
        ]);
    }
}
