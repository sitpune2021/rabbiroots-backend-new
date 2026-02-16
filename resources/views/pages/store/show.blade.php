@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

{{-- ================= STORE IDENTITY ================= --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">{{ $store->name }}</h3>
                <div class="text-muted">
                    {{ $store->code }} • {{ $store->address }}
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-primary">
                    <i class="bx bx-edit"></i> Edit
                </a>
                <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ================= STATUS & HEALTH ================= --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="mb-3">Store Status</h5>

        <div class="d-flex flex-wrap gap-2">
            <span class="badge {{ $store->is_active ? 'bg-success' : 'bg-danger' }}">
                {{ $store->is_active ? 'Active' : 'Inactive' }}
            </span>

            <span class="badge {{ $store->is_open ? 'bg-success' : 'bg-secondary' }}">
                {{ $store->is_open ? 'Open' : 'Closed' }}
            </span>

            <span class="badge {{ $store->accepting_orders ? 'bg-success' : 'bg-warning' }}">
                {{ $store->accepting_orders ? 'Accepting Orders' : 'Orders Paused' }}
            </span>

            @if($store->daily_cash_limit && $store->pending_cash_amount >= $store->daily_cash_limit)
                <span class="badge bg-danger">Cash Risk</span>
            @endif
        </div>
    </div>
</div>

{{-- ================= KPI ROW ================= --}}
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <div class="text-muted">Delivery Radius</div>
                <h4>{{ $store->delivery_radius_km }} km</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <div class="text-muted">Max Orders / Slot</div>
                <h4>{{ $store->max_orders_per_slot ?? '∞' }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <div class="text-muted">Order Cutoff</div>
                <h4>{{ $store->order_cutoff_minutes }} min</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body">
                <div class="text-muted">Pending Cash</div>
                <h4>₹{{ number_format($store->pending_cash_amount, 0) }}</h4>
            </div>
        </div>
    </div>

</div>

{{-- ================= OPERATIONS & LOCATION ================= --}}
<div class="row g-4 mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">Operations</h5>

                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="text-muted">Opening Time</div>
                        <div class="fw-semibold">{{ $store->opening_time_for_input ?? '—' }}</div>
                    </div>

                    <div class="col-6 mb-3">
                        <div class="text-muted">Closing Time</div>
                        <div class="fw-semibold">{{ $store->closing_time_for_input ?? '—' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="text-muted">Max Orders</div>
                        <div class="fw-semibold">{{ $store->max_orders_per_slot ?? 'Unlimited' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="text-muted">Cutoff Minutes</div>
                        <div class="fw-semibold">{{ $store->order_cutoff_minutes }} min</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">Location</h5>

                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="text-muted">Latitude</div>
                        <div class="fw-semibold">{{ $store->latitude }}</div>
                    </div>

                    <div class="col-6 mb-3">
                        <div class="text-muted">Longitude</div>
                        <div class="fw-semibold">{{ $store->longitude }}</div>
                    </div>

                    <div class="col-12">
                        <div class="text-muted">Delivery Radius</div>
                        <div class="fw-semibold">{{ $store->delivery_radius_km }} km</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ================= CONTACT & MANAGER ================= --}}
<div class="row g-4 mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">Contact</h5>

                <div class="mb-3">
                    <div class="text-muted">Phone</div>
                    <div class="fw-semibold">{{ $store->contact_phone ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-muted">Email</div>
                    <div class="fw-semibold">{{ $store->contact_email ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">Store Manager</h5>

                @if($store->manager)
                    <div class="mb-2">
                        <div class="text-muted">Name</div>
                        <div class="fw-semibold">{{ $store->manager->name }}</div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted">Email</div>
                        <div class="fw-semibold">{{ $store->manager->email }}</div>
                    </div>

                    <div>
                        <div class="text-muted">Phone</div>
                        <div class="fw-semibold">{{ $store->manager->phone ?? '—' }}</div>
                    </div>
                @else
                    <p class="text-muted">No manager assigned</p>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ================= FINANCE ================= --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="mb-3">Finance</h5>

        <div class="row">
            <div class="col-md-4">
                <div class="text-muted">Daily Cash Limit</div>
                <div class="fw-semibold">
                    {{ $store->daily_cash_limit ? '₹'.$store->daily_cash_limit : 'Not Set' }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="text-muted">Pending Cash</div>
                <div class="fw-semibold">₹{{ number_format($store->pending_cash_amount, 2) }}</div>
            </div>

            <div class="col-md-4">
                <div class="text-muted">Risk Status</div>
                @if($store->daily_cash_limit && $store->pending_cash_amount >= $store->daily_cash_limit)
                    <span class="badge bg-danger">High Risk</span>
                @else
                    <span class="badge bg-success">Normal</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ================= AUDIT ================= --}}
<div class="card shadow-sm">
    <div class="card-body small text-muted">
        <h6 class="mb-2">Audit & Timeline</h6>
        <div>Created: {{ $store->created_at->format('d M Y, h:i A') }}</div>
        <div>Updated: {{ $store->updated_at->format('d M Y, h:i A') }}</div>
        <div>Last Opened: {{ $store->last_opened_at?->format('d M Y, h:i A') ?? '—' }}</div>
        <div>Last Closed: {{ $store->last_closed_at?->format('d M Y, h:i A') ?? '—' }}</div>
    </div>
</div>

</div>
@endsection
