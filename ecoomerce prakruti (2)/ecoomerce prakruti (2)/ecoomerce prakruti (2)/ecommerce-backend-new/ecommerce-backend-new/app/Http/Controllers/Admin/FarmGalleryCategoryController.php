<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmGalleryCategory;
use App\Models\FarmGalleryItem;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FarmGalleryCategoryController extends Controller
{
    public function index()
    {
        $categories = FarmGalleryCategory::orderBy('sort_order')->latest()->paginate(15);

        return view('admin.farm-gallery-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.farm-gallery-categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateCategory($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        FarmGalleryCategory::create($data);

        return redirect()->route('admin.farm-gallery-categories.index')->with('success', 'Farm gallery category created successfully.');
    }

    public function edit(FarmGalleryCategory $farmGalleryCategory)
    {
        return view('admin.farm-gallery-categories.edit', ['category' => $farmGalleryCategory]);
    }

    public function update(Request $request, FarmGalleryCategory $farmGalleryCategory)
    {
        $data = $this->validateCategory($request);
        $data['slug'] = $this->uniqueSlug($data['name'], $farmGalleryCategory->id);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $oldSlug = $farmGalleryCategory->slug;
        $farmGalleryCategory->update($data);

        if ($oldSlug !== $farmGalleryCategory->slug) {
            FarmGalleryItem::where('category', $oldSlug)->update(['category' => $farmGalleryCategory->slug]);
        }

        return redirect()->route('admin.farm-gallery-categories.index')->with('success', 'Farm gallery category updated successfully.');
    }

    public function destroy(FarmGalleryCategory $farmGalleryCategory)
    {
        if (FarmGalleryItem::where('category', $farmGalleryCategory->slug)->exists()) {
            return back()->with('error', 'This category is used by farm gallery items. Move those items before deleting it.');
        }

        try {
            $farmGalleryCategory->delete();
        } catch (QueryException) {
            return back()->with('error', 'This category is linked to existing records and cannot be deleted.');
        }

        return redirect()->route('admin.farm-gallery-categories.index')->with('success', 'Farm gallery category deleted successfully.');
    }

    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'farm-gallery-category';
        $slug = $base;
        $counter = 2;

        while (
            FarmGalleryCategory::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
