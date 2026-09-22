<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'product');

        $productReviews = ProductReview::with('product')
            ->when($request->filled('status') && $type === 'product', fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(12, ['*'], 'product_page')
            ->withQueryString();

        $testimonials = Testimonial::query()
            ->when($request->filled('status') && $type === 'testimonial', fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(12, ['*'], 'testimonial_page')
            ->withQueryString();

        return view('admin.reviews.index', compact('productReviews', 'testimonials', 'type'));
    }

    public function updateProductReview(Request $request, ProductReview $review)
    {
        $data = $request->validate([
            'status' => 'required|in:active,pending,hidden',
        ]);

        $review->update($data);

        return redirect()->route('admin.reviews.index', ['type' => 'product'])->with('success', 'Product review updated successfully.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $testimonial->update($data);

        return redirect()->route('admin.reviews.index', ['type' => 'testimonial'])->with('success', 'Testimonial updated successfully.');
    }
}
