<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\ProductAttribute;

class CatalogMetaApiController extends Controller
{
    public function brands()
    {
        $brands = Brand::where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Brands fetched successfully.',
            'data' => BrandResource::collection($brands),
        ]);
    }

    public function attributes()
    {
        $attributes = ProductAttribute::with('values')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Attributes fetched successfully.',
            'data' => AttributeResource::collection($attributes),
        ]);
    }
}
