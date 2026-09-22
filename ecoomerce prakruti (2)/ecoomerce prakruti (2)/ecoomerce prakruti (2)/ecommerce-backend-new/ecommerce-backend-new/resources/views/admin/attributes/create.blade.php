@extends('admin.layouts.app')
@section('title', 'Add Attribute')
@section('content')
<h2 class="mb-4">Add Attribute</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.attributes.store') }}" method="POST" id="attrForm">@csrf
        <div class="mb-3"><label class="form-label">Attribute Name *</label><input type="text" name="name" class="form-control" placeholder="e.g. Size, Weight, Flavor" required></div>
        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
        <div class="mb-3"><label class="form-label">Values (one per line)</label><textarea class="form-control" rows="4" id="valuesText" placeholder="Small&#10;Medium&#10;Large"></textarea></div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
<script>
document.getElementById('attrForm').addEventListener('submit', function(e) {
    const values = document.getElementById('valuesText').value.split(/[\n,]+/).map(v => v.trim()).filter(v => v);
    values.forEach((v, i) => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = 'values[' + i + ']'; input.value = v;
        this.appendChild(input);
    });
});
</script>
@endsection
