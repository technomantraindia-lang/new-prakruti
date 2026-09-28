@extends('admin.layouts.app')

@section('title', 'Home Banners')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h2 class="mb-1">Home Banners</h2>
        <p class="text-muted mb-0">Upload homepage banners, preview every image, and drag them into the storefront order.</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Banner Images</a>
</div>

<div class="alert alert-success border-0 shadow-sm">
    <strong>Recommended banner size:</strong> 1920 x 720 px, PNG/JPG/WebP, max 5 MB each. On mobile, the center of each image is used, so keep important text and products near the center.
</div>

<div class="card">
    <div class="card-body">
        <form id="banner-order-form" method="POST" action="{{ route('admin.banners.updateOrder') }}">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 160px;">Preview</th>
                            <th>Title</th>
                            <th>Link</th>
                            <th style="width: 150px;">Order</th>
                            <th>Status</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="banner-order-list">
                        @forelse ($banners as $banner)
                            <tr draggable="true" data-banner-id="{{ $banner->id }}" class="banner-order-row">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="banner-drag-handle text-muted" title="Drag to reorder" aria-label="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
                                        @if($banner->image_url)
                                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="rounded border bg-light" style="width: 110px; height: 58px; object-fit: cover;">
                                        @else
                                            <div class="rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center small" style="width: 110px; height: 58px;">Image unavailable</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $banner->title }}</strong>
                                    <div class="text-muted small">CTA: Shop Now</div>
                                </td>
                                <td>
                                    @if($banner->link)
                                        <span class="small">{{ $banner->link }}</span>
                                    @else
                                        <span class="text-muted small">No link</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border banner-order-label">{{ $loop->iteration }}</span>
                                    <input type="hidden" name="orders[{{ $banner->id }}]" value="{{ $loop->index }}" class="banner-order-input">
                                </td>
                                <td><span class="badge bg-{{ $banner->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($banner->status) }}</span></td>
                                <td>
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <button type="submit" form="delete-banner-{{ $banner->id }}" class="btn btn-sm btn-outline-danger" title="Delete banner"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No banners added yet. The storefront will use the built-in default banner until you upload one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($banners->count())
                <button type="submit" form="banner-order-form" class="btn btn-primary"><i class="fas fa-save"></i> Save Banner Order</button>
            @endif
        </form>

        @foreach ($banners as $banner)
            <form id="delete-banner-{{ $banner->id }}" action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner?')">
                @csrf @method('DELETE')
            </form>
        @endforeach

        <div class="mt-3">
            {{ $banners->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .banner-order-row { cursor: grab; transition: background-color .15s ease, opacity .15s ease; }
    .banner-order-row:active { cursor: grabbing; }
    .banner-order-row.is-dragging { opacity: .45; background: #f3edd7; }
    .banner-order-row.drop-target { box-shadow: inset 0 3px 0 #2d5a27; }
    .banner-drag-handle { cursor: grab; font-size: 1.05rem; }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const list = document.getElementById('banner-order-list');
        if (!list) return;

        let draggedRow = null;

        const refreshOrder = () => {
            [...list.querySelectorAll('.banner-order-row')].forEach((row, index) => {
                row.querySelector('.banner-order-label').textContent = index + 1;
                row.querySelector('.banner-order-input').value = index;
            });
        };

        list.querySelectorAll('.banner-order-row').forEach((row) => {
            row.addEventListener('dragstart', (event) => {
                draggedRow = row;
                row.classList.add('is-dragging');
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', row.dataset.bannerId);
            });

            row.addEventListener('dragend', () => {
                row.classList.remove('is-dragging');
                list.querySelectorAll('.drop-target').forEach((item) => item.classList.remove('drop-target'));
                draggedRow = null;
                refreshOrder();
            });

            row.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!draggedRow || draggedRow === row) return;

                const bounds = row.getBoundingClientRect();
                const insertAfter = event.clientY > bounds.top + bounds.height / 2;
                list.querySelectorAll('.drop-target').forEach((item) => item.classList.remove('drop-target'));
                row.classList.add('drop-target');
                list.insertBefore(draggedRow, insertAfter ? row.nextSibling : row);
            });

            row.addEventListener('dragleave', () => row.classList.remove('drop-target'));
        });

        refreshOrder();
    })();
</script>
@endpush

