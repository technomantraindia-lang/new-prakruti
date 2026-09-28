<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmGalleryCategory;
use App\Models\FarmGalleryItem;
use App\Models\FarmGalleryVideo;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FarmGalleryController extends Controller
{
    public function __construct(private ImageUploadService $uploader) {}

    public function index()
    {
        $items = FarmGalleryItem::with('categoryInfo')->withCount('videos')->orderBy('sort_order')->latest()->paginate(15);

        return view('admin.farm-gallery.index', compact('items'));
    }

    public function create()
    {
        return view('admin.farm-gallery.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateItem($request);
        unset($data['videos']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploader->upload($request->file('image'), 'farm-gallery');
        }

        $item = FarmGalleryItem::create($data);
        $this->saveVideos($item, $request);

        return redirect()->route('admin.farm-gallery.index')->with('success', 'Farm gallery item created successfully.');
    }

    public function edit(FarmGalleryItem $farmGallery)
    {
        $farmGallery->load('videos');

        return view('admin.farm-gallery.edit', array_merge(['item' => $farmGallery], $this->formData()));
    }

    public function update(Request $request, FarmGalleryItem $farmGallery)
    {
        $data = $this->validateItem($request, true);
        unset($data['videos']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $this->uploader->delete($farmGallery->image);
            $data['image'] = $this->uploader->upload($request->file('image'), 'farm-gallery');
        }

        $farmGallery->update($data);
        $this->removeVideos($request->input('remove_videos', []));
        $this->saveVideos($farmGallery, $request);

        return redirect()->route('admin.farm-gallery.index')->with('success', 'Farm gallery item updated successfully.');
    }

    public function destroy(FarmGalleryItem $farmGallery)
    {
        $this->uploader->delete($farmGallery->image);

        foreach ($farmGallery->videos as $video) {
            $this->deleteVideoFile($video->video);
        }

        $farmGallery->delete();

        return redirect()->route('admin.farm-gallery.index')->with('success', 'Farm gallery item deleted successfully.');
    }

    private function validateItem(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'category' => 'required|string|max:255|exists:farm_gallery_categories,slug',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => [$updating ? 'nullable' : 'required', 'image'],
            'tag' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'details' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'videos' => 'nullable|array',
            'videos.*.title' => 'nullable|string|max:255',
            'videos.*.file' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:102400',
        ]);
    }

    private function formData(): array
    {
        return [
            'farmGalleryCategories' => FarmGalleryCategory::orderBy('sort_order')->orderBy('name')->get(),
        ];
    }

    private function saveVideos(FarmGalleryItem $item, Request $request): void
    {
        $rows = $request->file('videos', []);
        $titles = $request->input('videos', []);
        $sort = $item->videos()->max('sort_order') ?? 0;

        foreach ($rows as $index => $row) {
            $file = $row['file'] ?? null;

            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $item->videos()->create([
                'title' => $titles[$index]['title'] ?? null,
                'video' => $this->uploadVideo($file),
                'sort_order' => ++$sort,
            ]);
        }
    }

    private function removeVideos(array $ids): void
    {
        foreach (FarmGalleryVideo::whereIn('id', $ids)->get() as $video) {
            $this->deleteVideoFile($video->video);
            $video->delete();
        }
    }

    private function uploadVideo(UploadedFile $file): string
    {
        $name = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('farm-gallery/videos', $name, 'public');
    }

    private function deleteVideoFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
