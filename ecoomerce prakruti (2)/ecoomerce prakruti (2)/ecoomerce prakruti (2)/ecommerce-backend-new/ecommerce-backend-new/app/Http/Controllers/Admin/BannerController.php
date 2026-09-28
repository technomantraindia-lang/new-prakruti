<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(private ImageUploadService $uploader) {}

    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|max:5120',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $sortOrder = (int) ($data['sort_order'] ?? 0);
        foreach ($request->file('images', []) as $index => $image) {
            Banner::create([
                'title' => count($request->file('images', [])) > 1 ? $data['title'] . ' ' . ($index + 1) : $data['title'],
                'image' => $this->uploader->upload($image, 'banners'),
                'link' => $data['link'] ?? null,
                'button_text' => 'Shop Now',
                'sort_order' => $sortOrder + $index,
                'status' => $data['status'],
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner image(s) added successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            $this->uploader->delete($banner->image);
            $data['image'] = $this->uploader->upload($request->file('image'), 'banners');
        }

        $data['button_text'] = 'Shop Now';
        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function updateOrder(Request $request)
    {
        $data = $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'nullable|integer|min:0',
        ]);

        foreach ($data['orders'] as $id => $sortOrder) {
            Banner::whereKey($id)->update(['sort_order' => (int) $sortOrder]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner order saved successfully.');
    }

    public function destroy(Banner $banner)
    {
        $image = $banner->image;
        $banner->delete();
        $this->uploader->delete($image);

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
