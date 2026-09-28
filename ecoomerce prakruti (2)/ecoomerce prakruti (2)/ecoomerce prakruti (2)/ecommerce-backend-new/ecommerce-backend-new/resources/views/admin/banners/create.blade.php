@extends('admin.layouts.app')

@section('title', 'Add Home Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add Home Banners</h2>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card">
    <div class="card-header">
        <strong>Banner upload guidelines</strong>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            Make banners at <strong>1920 Ã— 720 px</strong>. Use PNG/JPG/WebP, max <strong>5 MB</strong> each. You can choose multiple files together to create a carousel.
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Banner Title *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', 'Prakruti Organic Banner') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Banner Images *</label>
                <input id="bannerImages" type="file" name="images[]" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" accept="image/*" multiple required>
                <div class="form-text">Select one or multiple banner images. If multiple are uploaded, they will be added one after another in order.</div>
                @error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div id="bannerImagePreviews" class="d-flex flex-wrap gap-3 mt-3"></div>
            </div>

            @include('admin.banners._fields')

            <button type="submit" class="btn btn-primary">Create Banner(s)</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const input = document.getElementById('bannerImages');
        const previews = document.getElementById('bannerImagePreviews');
        if (!input || !previews) return;

        input.addEventListener('change', () => {
            previews.replaceChildren();
            [...input.files].forEach((file, index) => {
                const card = document.createElement('div');
                card.className = 'border rounded p-2 bg-light';
                card.style.width = '150px';
                card.innerHTML = `<img src="${URL.createObjectURL(file)}" alt="Selected banner ${index + 1}" style="width: 100%; height: 80px; object-fit: cover; border-radius: 6px;"><div class="small text-muted mt-1">Image ${index + 1}</div>`;
                previews.appendChild(card);
            });
        });
    })();
</script>
@endpush
