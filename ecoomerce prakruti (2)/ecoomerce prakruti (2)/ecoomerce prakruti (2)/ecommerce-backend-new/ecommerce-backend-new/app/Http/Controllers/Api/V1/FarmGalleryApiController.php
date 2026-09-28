<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FarmGalleryItem;

class FarmGalleryApiController extends Controller
{
    public function index()
    {
        $items = FarmGalleryItem::with(['videos', 'categoryInfo'])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->latest()
            ->get()
            ->map(fn (FarmGalleryItem $item) => [
                'id' => $item->id,
                'category' => $item->category,
                'category_label' => $item->category_name,
                'title' => $item->title,
                'description' => $item->description,
                'image' => $item->image_url,
                'tag' => $item->tag,
                'location' => $item->location,
                'details' => $item->details,
                'videos' => $item->videos->map(fn ($video) => [
                    'title' => $video->title,
                    'src' => $video->video_url,
                ])->values(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Farm gallery fetched successfully.',
            'data' => $items,
        ]);
    }
}
