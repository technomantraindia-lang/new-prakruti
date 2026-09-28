@extends('admin.layouts.app')

@section('title', 'Edit Home Banner')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Home Banner</h2>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label">Banner Title *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $banner->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Replace Banner Image</label>
                <input id="bannerImage" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                <div class="form-text">Recommended size: 1920 Ã— 720 px. Leave blank to keep current image.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($banner->image_url)
                    <div class="small text-muted mt-3 mb-1">Current banner image</div>
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="mt-3 rounded border bg-light" style="max-width: 420px; width: 100%; height: auto;">
                @else
                    <div class="alert alert-warning mt-3 mb-0">The saved image is unavailable. Upload a replacement image to repair this banner.</div>
                @endif
                <div id="bannerImagePreview" class="mt-3"></div>
            </div>

            @include('admin.banners._fields')

            <button type="submit" class="btn btn-primary">Update Banner</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const input = document.getElementById('bannerImage');
        const preview = document.getElementById('bannerImagePreview');
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            preview.replaceChildren();
            const file = input.files[0];
            if (!file) return;

            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = 'New banner preview';
            image.className = 'rounded border bg-light';
            image.style.cssText = 'max-width: 420px; width: 100%; height: auto;';
            preview.appendChild(image);
        });
    })();
</script>
@endpush
