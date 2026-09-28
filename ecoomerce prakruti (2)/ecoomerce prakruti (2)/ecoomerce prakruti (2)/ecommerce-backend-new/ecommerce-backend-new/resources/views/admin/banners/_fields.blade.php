<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Link URL</label>
            <input type="text" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $banner->link ?? '') }}" placeholder="#categories or https://example.com">
            <div class="form-text">Optional. Use hashes like #categories, #about, #contact, #family-pack, or a full URL.</div>
            @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" required>
                <option value="active" @selected(old('status', $banner->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $banner->status ?? '') === 'inactive')>Inactive</option>
            </select>
        </div>
    </div>
</div>
