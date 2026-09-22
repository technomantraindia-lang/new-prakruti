<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(private ImageUploadService $uploader) {}

    public function index()
    {
        $categories = Category::withCount('mainProducts')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['parent_id'] = null;
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploader->upload($request->file('image'), 'categories');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['parent_id'] = null;
        if ($request->hasFile('image')) {
            $this->uploader->delete($category->image);
            $data['image'] = $this->uploader->upload($request->file('image'), 'categories');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $assignedProducts = Product::where(function ($query) use ($category) {
            $query->where('category_id', $category->id)
                ->orWhere('sub_category_id', $category->id)
                ->orWhere('sub_sub_category_id', $category->id);
        })->count();

        $childCategories = $category->children()->count();

        if ($assignedProducts > 0 || $childCategories > 0) {
            $reasons = [];
            if ($assignedProducts > 0) {
                $reasons[] = $assignedProducts . ' product' . ($assignedProducts === 1 ? '' : 's');
            }
            if ($childCategories > 0) {
                $reasons[] = $childCategories . ' subcategor' . ($childCategories === 1 ? 'y' : 'ies');
            }

            return back()->with('error', 'This category cannot be deleted because it is linked to ' . implode(' and ', $reasons) . '. Move or deactivate them first.');
        }

        try {
            $image = $category->image;
            $category->delete();
            $this->uploader->delete($image);
        } catch (QueryException) {
            return back()->with('error', 'This category is linked to existing store records and cannot be deleted. Deactivate it instead.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
