@extends('layouts.app')
@push('scripts')
<script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Promo Code List</h5>
            <a href="{{ route('promo.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('promo.index') }}">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label"><strong>Status Filter</strong></label>
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                            <option value="scheduled" {{ request('status')=='scheduled'?'selected':'' }}>Scheduled</option>
                            <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
                            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Discount Type</strong></label>
                        <select class="form-select" id="typeFilter" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="percentage" {{ request('discount_type')=='percentage'?'selected':'' }}>Percentage</option>
                            <option value="flat" {{ request('discount_type')=='flat'?'selected':'' }}>Flat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Applicable For</strong></label>
                        <select class="form-select" id="applicableFilter" onchange="this.form.submit()">
                            <option value="">All Stores</option>
                            <option value="all_stores" {{ request('store_type')=='all_stores'?'selected':'' }}>All Stores</option>
                            <option value="specific_store" {{ request('store_type')=='specific_store'?'selected':'' }}>Specific Store</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Device Type</strong></label>
                        <select class="form-select" id="deviceFilter" onchange="this.form.submit()">
                            <option value="">All Devices</option>
                            <option value="web" {{ request('device')=='web'?'selected':'' }}>Web</option>
                            <option value="ios" {{ request('device')=='ios'?'selected':'' }}>iOS</option>
                            <option value="android" {{ request('device')=='android'?'selected':'' }}>Android</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Code Name</th>
                        <th>Discount</th>
                        <th>Min Order Value</th>
                        <th>Max Cap</th>
                        <th>Valid Period</th>
                        <th>Status</th>
                        <th>Redemptions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promoCodes as $index => $promo)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            <span class="badge bg-label-primary">
                                {{ $promo->code_name }}
                            </span>
                        </td>

                        <td>
                            @if ($promo->discount_type === 'percentage')
                            {{ $promo->discount_value }}%
                            @else
                            ₹{{ number_format($promo->discount_value) }}
                            @endif
                        </td>

                        <td>
                            ₹{{ number_format($promo->min_order_value) }}
                        </td>

                        <td>
                            {{ $promo->max_discount_cap 
                    ? '₹' . number_format($promo->max_discount_cap) 
                    : '-' 
                }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($promo->start_date)->format('M d') }}
                            -
                            {{ \Carbon\Carbon::parse($promo->end_date)->format('M d') }}
                        </td>

                        <td>
                            @php
                            $statusClass = match ($promo->status) {
                            'active' => 'success',
                            'inactive' => 'secondary',
                            'draft' => 'warning',
                            'scheduled' => 'info',
                            default => 'secondary',
                            };
                            @endphp

                            <span class="badge bg-label-{{ $statusClass }}">
                                {{ ucfirst($promo->status) }}
                            </span>
                        </td>

                        <td>
                            {{ $promo->used_count ?? 0 }} / {{ $promo->total_redemptions_limit }}
                        </td>

                        <td>
                            <div class="dropdown">
                                <button type="button"
                                    class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                </button>

                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                        href="{{ route('promo.show', $promo->id) }}">
                                        <i class="icon-base bx bx-show-alt me-1"></i> View
                                    </a>

                                    <a class="dropdown-item"
                                        href="{{ route('promo.edit', $promo->id) }}">
                                        <i class="icon-base bx bx-edit-alt me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('promo.destroy', $promo->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="icon-base bx bx-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No promo codes found
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection