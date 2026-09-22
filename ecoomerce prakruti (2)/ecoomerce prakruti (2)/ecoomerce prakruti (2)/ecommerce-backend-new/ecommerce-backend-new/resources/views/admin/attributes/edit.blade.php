@extends('admin.layouts.app')
@section('title', 'Edit Attribute')
@section('content')
<h2 class="mb-4">Edit Attribute</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">@csrf @method('PUT')
        <div class="mb-3"><label class="form-label">Attribute Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $attribute->name) }}" required></div>
        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" @selected($attribute->status==='active')>Active</option><option value="inactive" @selected($attribute->status==='inactive')>Inactive</option></select></div>
        <div class="mb-3"><label class="form-label">Values (one per line)</label>
            <textarea class="form-control" rows="5" name="values_text" id="valuesText">{{ $attribute->values->pluck('value')->implode("\n") }}</textarea>
            <small class="text-muted">One value per line</small>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const text = document.getElementById('valuesText').value;
    const values = text.split(/[\n,]+/).map(v => v.trim()).filter(v => v);
    values.forEach((v, i) => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = 'values[' + i + ']'; input.value = v;
        this.appendChild(input);
    });
});
</script>
@endsection
