@extends('admin.layouts.app')

@section('title', 'Consultation Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Consultation Requests</h2>
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach(['new' => 'New', 'assigned' => 'Assigned', 'contacted' => 'Contacted', 'consultation_scheduled' => 'Consultation Scheduled', 'completed' => 'Completed', 'closed' => 'Closed'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Preferred</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $requestItem)
                    <tr>
                        <td class="fw-bold">{{ $requestItem->reference_no }}</td>
                        <td>{{ $requestItem->name }}</td>
                        <td>
                            <div>{{ $requestItem->email ?: '-' }}</div>
                            <small class="text-muted">{{ $requestItem->phone ?: '' }}</small>
                        </td>
                        <td>{{ ucfirst($requestItem->preferred_contact_method) }}</td>
                        <td><span class="badge bg-primary">{{ str_replace('_', ' ', ucfirst($requestItem->status)) }}</span></td>
                        <td>{{ $requestItem->created_at?->format('d M Y, h:i A') }}</td>
                        <td><a href="{{ route('admin.consultations.show', $requestItem) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No consultation requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $requests->links() }}</div>
@endsection
