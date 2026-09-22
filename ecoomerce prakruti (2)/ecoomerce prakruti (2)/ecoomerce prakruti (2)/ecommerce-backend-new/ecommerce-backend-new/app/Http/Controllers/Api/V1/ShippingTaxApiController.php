<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
