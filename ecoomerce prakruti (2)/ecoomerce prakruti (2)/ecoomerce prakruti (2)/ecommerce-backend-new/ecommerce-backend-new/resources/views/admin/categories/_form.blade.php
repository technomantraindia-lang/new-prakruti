<div class="mb-3">
    <label class="form-label">Name *</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name ?? '') }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Category Image</label>
    <input type="file" name="image" class="form-control" accept="image/*">
    @if(isset($category) && $category->image)
    <img src="{{ $category->image_url }}" class="mt-2 rounded border" style="max-height:100px;">
    @endif
</div>
<div class="mb-3">
    <label class="form-label">GST Percentage *</label>
    <input type="number" name="gst_percentage" class="form-control @error('gst_percentage') is-invalid @enderror" min="0" max="100" step="0.01" value="{{ old('gst_percentage', $category->gst_percentage ?? 5) }}" required>
    <div class="form-text">Products in this category will use this GST percentage during cart and checkout.</div>
    @error('gst_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Status *</label>
    <select name="status" class="form-select" required>
        <option value="active" @selected(old('status', $category->status ?? 'active')==='active')>Active</option>
        <option value="inactive" @selected(old('status', $category->status ?? '')==='inactive')>Inactive</option>
    </select>
</div>
