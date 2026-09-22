<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()->featured()->with('category')->latest()->limit(8)->get();
        $categories = Category::where('status', 'active')->whereNull('parent_id')->withCount('products')->limit(6)->get();
        $banners = Banner::where('status', 'active')->orderBy('sort_order')->get();
        $settings = Setting::pluck('value', 'key');

        return view('frontend.home', compact('featuredProducts', 'categories', 'banners', 'settings'));
    }
}
