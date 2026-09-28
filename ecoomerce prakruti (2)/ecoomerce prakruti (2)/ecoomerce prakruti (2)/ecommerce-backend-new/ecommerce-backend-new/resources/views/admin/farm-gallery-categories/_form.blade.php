<div class="mb-3">
    <label class="form-label">Name *</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name ?? '') }}" required>
    <div class="form-text">The frontend filter name. Slug is generated automatically.</div>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-4">
    <div class="mb-3">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', $category->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $category->status ?? '') === 'inactive')>Inactive</option>
        </select>
    </div>
    </div>
</div>
