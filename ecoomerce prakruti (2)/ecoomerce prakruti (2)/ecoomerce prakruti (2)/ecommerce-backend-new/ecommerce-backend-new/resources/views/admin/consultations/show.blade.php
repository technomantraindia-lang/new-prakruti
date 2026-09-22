@extends('admin.layouts.app')

@section('title', 'Consultation ' . $consultation->reference_no)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Consultation Request {{ $consultation->reference_no }}</h2>
    <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-bold">Customer Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $consultation->name }}</dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $consultation->email ?: '-' }}</dd>
                    <dt class="col-sm-4">Mobile</dt><dd class="col-sm-8">{{ $consultation->phone ?: '-' }}</dd>
                    <dt class="col-sm-4">Preferred Contact</dt><dd class="col-sm-8">{{ ucfirst($consultation->preferred_contact_method) }}</dd>
                    <dt class="col-sm-4">Age Range</dt><dd class="col-sm-8">{{ $consultation->age_range ?: '-' }}</dd>
                    <dt class="col-sm-4">Dietary Preference</dt><dd class="col-sm-8">{{ $consultation->dietary_preference ?: '-' }}</dd>
                    <dt class="col-sm-4">Preferred Time</dt><dd class="col-sm-8">{{ $consultation->preferred_time ?: '-' }}</dd>
                    <dt class="col-sm-4">Consent</dt><dd class="col-sm-8">{{ $consultation->consent ? 'Yes' : 'No' }}</dd>
                    <dt class="col-sm-4">Concern / Goal</dt><dd class="col-sm-8">{{ $consultation->concern }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header fw-bold">Professional Follow-up</div>
            <div class="card-body">
                <form action="{{ route('admin.consultations.update', $consultation) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['new' => 'New', 'assigned' => 'Assigned', 'contacted' => 'Contacted', 'consultation_scheduled' => 'Consultation Scheduled', 'completed' => 'Completed', 'closed' => 'Closed'] as $value => $label)
                                <option value="{{ $value }}" @selected($consultation->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assigned Professional</label>
                        <input type="text" name="assigned_to" class="form-control" value="{{ old('assigned_to', $consultation->assigned_to) }}" placeholder="Nutritionist / Doctor name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internal Follow-up Note</label>
                        <textarea name="internal_note" class="form-control" rows="5">{{ old('internal_note', $consultation->internal_note) }}</textarea>
                    </div>
                    <button class="btn btn-primary">Save Follow-up</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
