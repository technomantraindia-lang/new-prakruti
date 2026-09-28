<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $item->title ?? '') }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Category *</label>
            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                @foreach($farmGalleryCategories as $galleryCategory)
                    <option value="{{ $galleryCategory->slug }}" @selected(old('category', $item->category ?? '') === $galleryCategory->slug)>
                        {{ $galleryCategory->name }}{{ $galleryCategory->status === 'inactive' ? ' (inactive)' : '' }}
                    </option>
                @endforeach
            </select>
            <div class="form-text"><a href="{{ route('admin.farm-gallery-categories.create') }}">Add a new farm gallery category</a></div>
            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Description *</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $item->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Image {{ isset($item) ? '' : '*' }}</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" @required(! isset($item))>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if(isset($item) && $item->image)
                <img src="{{ $item->image_url }}" class="mt-2 rounded border" style="max-height:100px;">
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Tag *</label>
            <input type="text" name="tag" class="form-control @error('tag') is-invalid @enderror" value="{{ old('tag', $item->tag ?? '') }}" required>
            @error('tag')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Location *</label>
            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $item->location ?? '') }}" required>
            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Details *</label>
    <textarea name="details" class="form-control @error('details') is-invalid @enderror" rows="3" required>{{ old('details', $item->details ?? '') }}</textarea>
    @error('details')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-4">
    <div class="mb-3">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', $item->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $item->status ?? '') === 'inactive')>Inactive</option>
        </select>
    </div>
    </div>
</div>

@if(isset($item) && $item->videos->isNotEmpty())
<div class="mb-3">
    <label class="form-label">Existing Videos</label>
    <div class="row">
        @foreach($item->videos as $video)
            <div class="col-md-4 mb-2">
                <div class="border rounded p-2 bg-light">
                    <video src="{{ $video->video_url }}" controls class="w-100 rounded" style="height: 120px; object-fit: cover;"></video>
                    <div class="small fw-semibold mt-1">{{ $video->title ?: 'Untitled video' }}</div>
                    <label class="small text-danger mt-1">
                        <input type="checkbox" name="remove_videos[]" value="{{ $video->id }}">
                        Remove this video
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="mb-4">
    <label class="form-label">Add Process Videos</label>
    <div id="videoRows">
        <div class="row g-2 mb-2">
            <div class="col-md-5">
                <input type="text" name="videos[0][title]" class="form-control" placeholder="Video title">
            </div>
            <div class="col-md-7">
                <input type="file" name="videos[0][file]" class="form-control" accept="video/*">
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary" id="addVideoRow"><i class="fas fa-plus"></i> Add another video</button>
</div>

@push('styles')
<style>
    textarea.form-control { min-height: 110px; }
</style>
@endpush

@push('scripts')
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const videoRows = document.getElementById('videoRows');
        const addVideoRow = document.getElementById('addVideoRow');
        let index = 1;

        addVideoRow?.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2';
            row.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="videos[${index}][title]" class="form-control" placeholder="Video title">
                </div>
                <div class="col-md-7">
                    <input type="file" name="videos[${index}][file]" class="form-control" accept="video/*">
                </div>
            `;
            videoRows.appendChild(row);
            index += 1;
        });
    });
</script>
