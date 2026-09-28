<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerApiController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Banner $banner) => [
                'id' => $banner->id,
                'title' => $banner->title,
                'image' => $banner->image_url,
                'link' => $banner->link,
                'button_text' => $banner->button_text,
                'sort_order' => $banner->sort_order,
            ]);

        // Do not send records whose media was removed outside the banner
        // delete flow. The storefront can then fall back to its built-in hero.
        $banners = $banners->filter(fn (array $banner) => $banner['image'] !== null)->values();

        return response()->json([
            'success' => true,
            'message' => 'Banners fetched successfully.',
            'data' => $banners,
        ]);
    }
}
