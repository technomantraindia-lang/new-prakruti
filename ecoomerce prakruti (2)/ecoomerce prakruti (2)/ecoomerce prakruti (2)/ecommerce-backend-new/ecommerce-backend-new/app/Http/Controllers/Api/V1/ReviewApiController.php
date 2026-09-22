<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReviewApiController extends Controller
{
    public function testimonials()
    {
        if (! Schema::hasTable('testimonials')) {
            return response()->json([
                'success' => true,
                'message' => 'Testimonials fetched successfully.',
                'data' => [],
            ]);
        }

        $items = Testimonial::active()
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn (Testimonial $testimonial) => $this->serializeTestimonial($testimonial));

        return response()->json([
            'success' => true,
            'message' => 'Testimonials fetched successfully.',
            'data' => $items,
        ]);
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'location' => 'nullable|string|max:120',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1200',
        ]);

        $testimonial = Testimonial::create([
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'rating' => $validated['rating'],
            'msg' => $validated['review'],
            'status' => 'active',
            'source' => 'frontend',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for sharing your experience.',
            'data' => $this->serializeTestimonial($testimonial),
        ], 201);
    }

    public function productReviews(string $product)
    {
        $resolved = $this->resolveProduct($product);

        if (! $resolved || ! Schema::hasTable('product_reviews')) {
            return response()->json([
                'success' => true,
                'message' => 'Product reviews fetched successfully.',
                'data' => [],
            ]);
        }

        $reviews = ProductReview::with('product')
            ->where('product_id', $resolved->id)
            ->active()
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (ProductReview $review) => $this->serializeProductReview($review));

        return response()->json([
            'success' => true,
            'message' => 'Product reviews fetched successfully.',
            'data' => $reviews,
        ]);
    }

    public function storeProductReview(Request $request, string $product)
    {
        $resolved = $this->resolveProduct($product);

        if (! $resolved) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'location' => 'nullable|string|max:120',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1200',
        ]);

        $review = ProductReview::create([
            'product_id' => $resolved->id,
            'user_id' => $request->user()?->id,
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'active',
            'source' => 'frontend',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for reviewing this product.',
            'data' => $this->serializeProductReview($review->load('product')),
        ], 201);
    }

    private function resolveProduct(string $value): ?Product
    {
        return Product::where('status', 'active')
            ->where(function ($query) use ($value) {
                if (is_numeric($value)) {
                    $query->where('id', $value);
                } else {
                    $query->where('slug', $value);
                }
            })
            ->first();
    }

    private function serializeTestimonial(Testimonial $testimonial): array
    {
        return [
            'id' => $testimonial->id,
            'name' => $testimonial->name,
            'location' => $testimonial->location ?: 'Verified Customer',
            'rating' => (int) $testimonial->rating,
            'review' => $testimonial->msg,
            'status' => $testimonial->status,
            'created_at' => $testimonial->created_at?->toISOString(),
        ];
    }

    private function serializeProductReview(ProductReview $review): array
    {
        return [
            'id' => $review->id,
            'product_id' => $review->product_id,
            'product_name' => $review->product?->name,
            'name' => $review->name,
            'location' => $review->location ?: 'Verified Customer',
            'rating' => (int) $review->rating,
            'comment' => $review->comment,
            'status' => $review->status,
            'created_at' => $review->created_at?->toISOString(),
        ];
    }
}
