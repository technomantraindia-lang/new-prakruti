@php
    $oldPackages = old('packages');
    if ($oldPackages) {
        $packageRows = collect($oldPackages)->values();
    } elseif (isset($product) && $product->relationLoaded('variations') && $product->variations->count()) {
        $packageRows = $product->variations->map(fn ($variation) => [
            'id' => $variation->id,
            'label' => $variation->attr_val,
            'price' => $variation->price,
            'sale_price' => $variation->sale_price,
            'stock_qty' => $variation->stock_qty,
            'status' => $variation->status,
        ])->values();
    } else {
        $packageRows = collect([[
            'label' => '',
            'price' => $product->price ?? '',
            'sale_price' => $product->sale_price ?? '',
            'stock_qty' => $product->stock_qty ?? 0,
            'status' => 'active',
        ]]);
    }

    $productInfo = old('product_information', $product->product_information ?? []);
@endphp

<div class="row g-4">
    <div class="col-12">
        <div class="alert alert-light border mb-0">
            <strong>Product detail essentials only.</strong>
            Fill product content, package sizes, pricing, stock, and images needed for the frontend product card and product detail page.
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Basic Product Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}" placeholder="e.g. A2 Gir Cow Ghee Bilona" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="card border-success-subtle bg-light mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Package Size & Price</h5>
                            <small class="text-muted">Add options like 500g pack, 1kg pack, 5kg family pack.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="addPackageRow">+ Add Package</button>
                    </div>
                    <div class="card-body">
                        @error('packages')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
                        <div id="packageRows" class="vstack gap-3">
                            @foreach($packageRows as $index => $package)
                                <div class="package-row border rounded-3 bg-white p-3">
                                    <input type="hidden" name="packages[{{ $index }}][id]" value="{{ $package['id'] ?? '' }}">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label">Package Size *</label>
                                            <input type="text" name="packages[{{ $index }}][label]" class="form-control @error("packages.$index.label") is-invalid @enderror" value="{{ $package['label'] ?? '' }}" placeholder="500g / 1kg" required>
                                            @error("packages.$index.label")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Regular Price *</label>
                                            <input type="number" step="0.01" name="packages[{{ $index }}][price]" class="form-control @error("packages.$index.price") is-invalid @enderror" value="{{ $package['price'] ?? '' }}" placeholder="750" required>
                                            @error("packages.$index.price")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Sale Price</label>
                                            <input type="number" step="0.01" name="packages[{{ $index }}][sale_price]" class="form-control @error("packages.$index.sale_price") is-invalid @enderror" value="{{ $package['sale_price'] ?? '' }}" placeholder="675">
                                            @error("packages.$index.sale_price")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Stock *</label>
                                            <input type="number" name="packages[{{ $index }}][stock_qty]" class="form-control @error("packages.$index.stock_qty") is-invalid @enderror" value="{{ $package['stock_qty'] ?? 0 }}" min="0" required>
                                            @error("packages.$index.stock_qty")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Status</label>
                                            <select name="packages[{{ $index }}][status]" class="form-select">
                                                <option value="active" @selected(($package['status'] ?? 'active') === 'active')>Active</option>
                                                <option value="inactive" @selected(($package['status'] ?? '') === 'inactive')>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-package-row" title="Remove package">×</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-3">The first active package becomes the product card price. Total package stock is synced as product stock.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Short Description</label>
                    <textarea name="short_desc" class="form-control @error('short_desc') is-invalid @enderror" rows="3" placeholder="Short text shown on product cards and the product detail top section.">{{ old('short_desc', $product->short_desc ?? '') }}</textarea>
                    @error('short_desc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="7" placeholder="Detailed product overview shown on the product detail page.">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-0">
                    <label class="form-label">Nutritional Info</label>
                    <textarea name="nutritional_info" class="form-control @error('nutritional_info') is-invalid @enderror" rows="6" placeholder="Example: Energy: 343 kcal&#10;Protein: 22g&#10;Carbohydrates: 60g">{{ old('nutritional_info', $product->nutritional_info ?? '') }}</textarea>
                    <small class="text-muted">This content appears in the Nutritional Info tab on the product detail page.</small>
                    @error('nutritional_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="card border-0 bg-light mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Product Information</h5>
                        <small class="text-muted">Optional. Leave blank to use the default details on the frontend.</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Type</label>
                                <input type="text" name="product_information[product_type]" class="form-control" value="{{ $productInfo['product_type'] ?? '' }}" placeholder="Default: selected category">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shelf Life</label>
                                <input type="text" name="product_information[shelf_life]" class="form-control" value="{{ $productInfo['shelf_life'] ?? '' }}" placeholder="Default: 12 Months">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ingredient</label>
                                <input type="text" name="product_information[ingredient]" class="form-control" value="{{ $productInfo['ingredient'] ?? '' }}" placeholder="Default: Pure product name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Packaging Type</label>
                                <input type="text" name="product_information[packaging_type]" class="form-control" value="{{ $productInfo['packaging_type'] ?? '' }}" placeholder="Default: Food Grade Standing Pouch">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Storage</label>
                                <input type="text" name="product_information[storage]" class="form-control" value="{{ $productInfo['storage'] ?? '' }}" placeholder="Default: Store in dry, airtight container">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Visibility</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $product->status ?? '') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="form-check">
                    <input type="checkbox" name="featured" value="1" class="form-check-input" id="featured" @checked(old('featured', $product->featured ?? false))>
                    <label class="form-check-label" for="featured">Show in Best Sellers</label>
                    <div class="form-text">Enable this for any product you want to show in the homepage Best Sellers section.</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Product Images</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Main Product Image</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    <small class="text-muted d-block mt-1">Optional card/listing image. If blank, the first product detail photo can be used on the frontend.</small>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if(isset($product) && $product->image)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mt-3 rounded border" style="max-width:100%;max-height:160px;object-fit:contain;">
                    @endif
                </div>

                <div class="mb-0">
                    <label class="form-label">Product Detail Photos <span class="text-danger">*</span></label>
                    <input
                        type="file"
                        name="gallery[]"
                        id="galleryImages"
                        class="form-control @error('gallery') is-invalid @enderror"
                        accept="image/*"
                        multiple
                        data-existing-gallery-count="{{ isset($product) ? $product->images->count() : 0 }}"
                        data-max-gallery="7"
                    >
                    <small class="text-muted d-block mt-1">Add minimum 1 and maximum 7 photos. These photos show as thumbnails on the frontend product detail page.</small>
                    @error('gallery')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @error('gallery.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div id="galleryLimitMessage" class="small mt-2 text-muted"></div>
                    <div id="galleryPreview" class="d-flex flex-wrap gap-2 mt-3"></div>

                    @if(isset($product) && $product->images->count())
                        <label class="form-label mt-3">Existing Product Images</label>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @foreach($product->images as $img)
                                <div class="position-relative border rounded p-1 bg-white">
                                    <img src="{{ $img->image_url }}" alt="{{ $product->name }} gallery image" style="width:80px;height:80px;object-fit:cover;" class="rounded">
                                    <label class="d-block small text-danger mt-1"><input type="checkbox" name="remove_gallery[]" value="{{ $img->id }}"> Remove</label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const galleryInput = document.getElementById('galleryImages');
    const galleryPreview = document.getElementById('galleryPreview');
    const galleryLimitMessage = document.getElementById('galleryLimitMessage');
    const packageRows = document.getElementById('packageRows');
    const addPackageRow = document.getElementById('addPackageRow');
    const maxGalleryPhotos = Number(galleryInput?.dataset.maxGallery || 7);
    const initialExistingGalleryCount = Number(galleryInput?.dataset.existingGalleryCount || 0);
    const selectedGalleryFiles = [];

    function selectedRemoveGalleryCount() {
        return document.querySelectorAll('input[name="remove_gallery[]"]:checked').length;
    }

    function currentExistingGalleryCount() {
        return Math.max(initialExistingGalleryCount - selectedRemoveGalleryCount(), 0);
    }

    function updateGalleryLimitMessage(selectedFilesCount = 0) {
        if (!galleryLimitMessage) return;

        const currentCount = currentExistingGalleryCount();
        const totalCount = currentCount + selectedFilesCount;
        const remainingCount = Math.max(maxGalleryPhotos - currentCount, 0);

        galleryLimitMessage.textContent = `${totalCount}/${maxGalleryPhotos} product detail photos selected. You can upload ${remainingCount} more photo${remainingCount === 1 ? '' : 's'}.`;
        galleryLimitMessage.className = `small mt-2 ${totalCount > maxGalleryPhotos || totalCount < 1 ? 'text-danger' : 'text-muted'}`;
    }

    function packageRowTemplate(index) {
        return `
            <div class="package-row border rounded-3 bg-white p-3">
                <input type="hidden" name="packages[${index}][id]" value="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Package Size *</label>
                        <input type="text" name="packages[${index}][label]" class="form-control" placeholder="500g / 1kg" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Regular Price *</label>
                        <input type="number" step="0.01" name="packages[${index}][price]" class="form-control" placeholder="750" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sale Price</label>
                        <input type="number" step="0.01" name="packages[${index}][sale_price]" class="form-control" placeholder="675">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock *</label>
                        <input type="number" name="packages[${index}][stock_qty]" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="packages[${index}][status]" class="form-select">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100 remove-package-row" title="Remove package">×</button>
                    </div>
                </div>
            </div>
        `;
    }

    function reindexPackageRows() {
        if (!packageRows) return;

        packageRows.querySelectorAll('.package-row').forEach((row, index) => {
            row.querySelectorAll('input, select').forEach((field) => {
                if (field.name) {
                    field.name = field.name.replace(/packages\[\d+\]/, `packages[${index}]`);
                }
            });
        });
    }

    addPackageRow?.addEventListener('click', function () {
        if (!packageRows) return;

        packageRows.insertAdjacentHTML('beforeend', packageRowTemplate(packageRows.querySelectorAll('.package-row').length));
    });

    packageRows?.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-package-row');
        if (!button) return;

        const rows = packageRows.querySelectorAll('.package-row');
        if (rows.length <= 1) {
            alert('At least one package size is required.');
            return;
        }

        button.closest('.package-row')?.remove();
        reindexPackageRows();
    });

    function galleryFileKey(file) {
        return `${file.name}-${file.size}-${file.lastModified}`;
    }

    function syncGalleryInputFiles() {
        if (!galleryInput || typeof DataTransfer === 'undefined') return;

        const transfer = new DataTransfer();
        selectedGalleryFiles.forEach((file) => transfer.items.add(file));
        galleryInput.files = transfer.files;
    }

    function renderGalleryPreview() {
        if (!galleryPreview) return;

        galleryPreview.innerHTML = '';

        selectedGalleryFiles.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'border rounded p-2 bg-white position-relative';
            card.style.width = '110px';

            const img = document.createElement('img');
            img.alt = file.name;
            img.className = 'rounded mb-2';
            img.style.width = '100%';
            img.style.height = '90px';
            img.style.objectFit = 'cover';
            img.src = URL.createObjectURL(file);

            img.onload = () => URL.revokeObjectURL(img.src);

            const name = document.createElement('div');
            name.className = 'small text-muted text-truncate';
            name.title = file.name;
            name.textContent = file.name;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-sm btn-outline-danger w-100 mt-2 remove-selected-gallery';
            removeButton.dataset.index = String(index);
            removeButton.textContent = 'Remove';

            card.appendChild(img);
            card.appendChild(name);
            card.appendChild(removeButton);
            galleryPreview.appendChild(card);
        });
    }

    galleryInput?.addEventListener('change', function () {
        const files = Array.from(this.files || []);
        const remainingCount = Math.max(maxGalleryPhotos - currentExistingGalleryCount(), 0);
        const currentKeys = new Set(selectedGalleryFiles.map(galleryFileKey));
        const newFiles = files.filter((file) => ! currentKeys.has(galleryFileKey(file)));
        const nextFiles = [...selectedGalleryFiles, ...newFiles];

        if (nextFiles.length > remainingCount) {
            alert(`You can keep a maximum of ${maxGalleryPhotos} product detail photos. You can upload only ${remainingCount} more photo${remainingCount === 1 ? '' : 's'} right now.`);
            syncGalleryInputFiles();
            return;
        }

        selectedGalleryFiles.splice(0, selectedGalleryFiles.length, ...nextFiles);
        syncGalleryInputFiles();
        renderGalleryPreview();
        updateGalleryLimitMessage(selectedGalleryFiles.length);
    });

    galleryPreview?.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-selected-gallery');
        if (!button) return;

        selectedGalleryFiles.splice(Number(button.dataset.index), 1);
        syncGalleryInputFiles();
        renderGalleryPreview();
        updateGalleryLimitMessage(selectedGalleryFiles.length);
    });

    document.addEventListener('change', function (event) {
        if (!event.target.matches('input[name="remove_gallery[]"]')) return;

        const remainingCount = Math.max(maxGalleryPhotos - currentExistingGalleryCount(), 0);

        if (selectedGalleryFiles.length > remainingCount) {
            selectedGalleryFiles.splice(remainingCount);
            syncGalleryInputFiles();
            renderGalleryPreview();
        }

        updateGalleryLimitMessage(selectedGalleryFiles.length);
    });

    updateGalleryLimitMessage(selectedGalleryFiles.length);
});
</script>
