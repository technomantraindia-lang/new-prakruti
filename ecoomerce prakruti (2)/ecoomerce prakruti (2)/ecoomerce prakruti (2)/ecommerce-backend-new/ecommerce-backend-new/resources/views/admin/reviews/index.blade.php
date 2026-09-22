@extends('admin.layouts.app')

@section('title', 'Frontend Reviews')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Frontend Reviews</h2>
        <p class="text-muted mb-0">Manage product reviews and homepage testimonials submitted from the React frontend.</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.reviews.index', ['type' => 'product']) }}" class="btn btn-{{ $type === 'product' ? 'primary' : 'outline-primary' }}">
            Product Reviews
        </a>
        <a href="{{ route('admin.reviews.index', ['type' => 'testimonial']) }}" class="btn btn-{{ $type === 'testimonial' ? 'primary' : 'outline-primary' }}">
            Testimonials
        </a>
    </div>
</div>

@if($type === 'testimonial')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Homepage Testimonials</h5>
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="type" value="testimonial">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 190px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testimonials as $testimonial)
                        <tr>
                            <td>
                                <strong>{{ $testimonial->name }}</strong>
                                <div class="small text-muted">{{ $testimonial->location ?: 'Verified Customer' }}</div>
                            </td>
                            <td class="text-warning">{{ str_repeat('★', (int) $testimonial->rating) }}</td>
                            <td style="max-width: 460px;">{{ \Illuminate\Support\Str::limit($testimonial->msg, 150) }}</td>
                            <td>
                                <span class="badge bg-{{ $testimonial->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($testimonial->status) }}
                                </span>
                            </td>
                            <td>{{ $testimonial->created_at?->format('d M Y') }}</td>
                            <td>
                                <form action="{{ route('admin.reviews.testimonial.update', $testimonial) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="active" @selected($testimonial->status === 'active')>Active</option>
                                        <option value="inactive" @selected($testimonial->status === 'inactive')>Inactive</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No testimonials yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $testimonials->links() }}</div>
@else
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product Reviews</h5>
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="type" value="product">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['active' => 'Active', 'pending' => 'Pending', 'hidden' => 'Hidden'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 210px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productReviews as $review)
                        <tr>
                            <td>
                                <strong>{{ $review->product?->name ?? 'Deleted Product' }}</strong>
                                <div class="small text-muted">#{{ $review->product_id }}</div>
                            </td>
                            <td>
                                <strong>{{ $review->name }}</strong>
                                <div class="small text-muted">{{ $review->location ?: 'Verified Customer' }}</div>
                            </td>
                            <td class="text-warning">{{ str_repeat('★', (int) $review->rating) }}</td>
                            <td style="max-width: 420px;">{{ \Illuminate\Support\Str::limit($review->comment, 150) }}</td>
                            <td>
                                @php
                                    $statusClass = ['active' => 'success', 'pending' => 'warning', 'hidden' => 'secondary'][$review->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($review->status) }}</span>
                            </td>
                            <td>{{ $review->created_at?->format('d M Y') }}</td>
                            <td>
                                <form action="{{ route('admin.reviews.product.update', $review) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="active" @selected($review->status === 'active')>Active</option>
                                        <option value="pending" @selected($review->status === 'pending')>Pending</option>
                                        <option value="hidden" @selected($review->status === 'hidden')>Hidden</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No product reviews yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $productReviews->links() }}</div>
@endif
@endsection
