<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = [];

        if ($this->image) {
            $images[] = $this->image_url ?: asset('storage/' . ltrim($this->image, '/'));
        }

        if ($this->relationLoaded('images')) {
            foreach ($this->images as $img) {
                if (! empty($img->image)) {
                    $images[] = $img->image_url ?: asset('storage/' . ltrim($img->image, '/'));
                }
            }
        }

        $displayPrice = (float) ($this->sale_price ?? $this->price);
        $activeVariations = $this->relationLoaded('variations')
            ? $this->variations->where('status', 'active')->values()
            : null;
        $customProductInformation = $this->product_information ?? [];
        $productInformation = array_filter([
            'product_type' => $customProductInformation['product_type'] ?? $this->category?->name,
            'shelf_life' => $customProductInformation['shelf_life'] ?? '12 Months',
            'ingredient' => $customProductInformation['ingredient'] ?? ('Pure ' . $this->name),
            'packaging_type' => $customProductInformation['packaging_type'] ?? 'Food Grade Standing Pouch',
            'storage' => $customProductInformation['storage'] ?? 'Store in dry, airtight container',
        ], fn ($value) => filled($value));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'short_description' => $this->short_desc ?? $this->short_description ?? null,
            'nutritional_info' => $this->nutritional_info,
            'product_information' => $productInformation,
            'price' => $displayPrice,
            'regular_price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'stock_status' => $this->available_stock > 0 ? 'in_stock' : 'out_of_stock',
            'available_stock' => (int) $this->available_stock,
            'featured' => (bool) ($this->featured ?? false),
            'rating' => round((float) ($this->active_reviews_avg_rating ?? 4.8), 1),
            'reviews' => (int) ($this->active_reviews_count ?? 0),
            'category' => $this->whenLoaded('category', function () {
                return $this->category?->name;
            }),
            'category_id' => $this->category_id,
            'category_slug' => $this->whenLoaded('category', fn () => $this->category?->slug),
            'category_data' => $this->whenLoaded('category', fn () => $this->category ? new CategoryResource($this->category) : null),
            'brand' => $this->whenLoaded('brand', fn () => $this->brand ? new BrandResource($this->brand) : null),
            'image' => $images[0] ?? null,
            'images' => array_values(array_unique($images)),
            'types' => ['Organic', 'Natural'],
            'weight' => $this->weight ? (string) $this->weight : ($activeVariations?->first()?->attr_val),
            'variations' => $activeVariations ? VariationResource::collection($activeVariations) : VariationResource::collection($this->whenLoaded('variations')),
        ];
    }
}
