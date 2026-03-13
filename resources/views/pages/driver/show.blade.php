@extends('layouts.app')
@push('scripts')
<script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('driver.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Driver Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Driver Name</strong></label>
                                <p class="text-muted"> {{ optional($agent->user)->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Email</strong></label>
                                <p class="text-muted"> {{ optional($agent->user)->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Phone Number</strong></label>
                                <p class="text-muted"> +91 {{ optional($agent->user)->phone }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Date of Birth</strong></label>
                                <p class="text-muted"> {{ optional($agent->dob)->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Aadhar Number</strong></label>
                                <p class="text-muted">{{ $agent->aadhar_number ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>PAN Number</strong></label>
                                <p class="text-muted">{{ $agent->pan_number ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Permanent Address</strong></label>
                                <p class="text-muted">{{ $agent->permanent_address ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Temporary Address</strong></label>
                                <p class="text-muted">{{ $agent->temporary_address ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">License Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>License Number</strong></label>
                                <p class="text-muted">{{ $agent->license_number ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>License Type</strong></label>
                                <p class="text-muted">{{ $agent->license_type ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Issue Date</strong></label>
                                <p class="text-muted">{{ $agent->license_issue_date ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Expiry Date</strong></label>
                                <p class="text-muted">{{ $agent->license_expiry_date ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment & Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Total Orders</strong></label>
                                <h6 class="text-success"></h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Today's Orders</strong></label>
                                <h6 class="text-info">8 Orders</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Total Earnings</strong></label>
                                <h6 class="text-success">₹25,450</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Pending Amount</strong></label>
                                <h6 class="text-warning">₹2,350</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Completed Orders</strong></label>
                                <h6 class="text-success">120</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Cancelled Orders</strong></label>
                                <h6 class="text-danger">5</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Vehicle Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Vehicle Name</strong></label>
                                <p class="text-muted"> {{ $agent->vehicle_name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Vehicle Model</strong></label>
                                <p class="text-muted">{{ $agent->vehicle_model ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>License Plate</strong></label>
                                <p class="text-muted">{{ $agent->license_plate ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Vehicle Type</strong></label>
                                <p class="text-muted"> {{ $agent->vehicle_type ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Capacity (kg)</strong></label>
                                <p class="text-muted">{{ $agent->vehicle_capacity ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Registration Number</strong></label>
                                <p class="text-muted">{{ $agent->registration_number ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label"><strong>Insurance Policy Number</strong></label>
                                <p class="text-muted">{{ $agent->insurance_policy_number ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Change Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Current Status</strong></label>
                        <div class="mb-2">
                            <span class="badge bg-label-success">Active</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label"><strong>Update Status</strong></label>
                        <select class="form-select" id="statusSelect">
                            <option value="">Select Status</option>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on-leave">On Leave</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bx bx-save me-1"></i> Update Status
                    </button>
                </div>
            </div> -->

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Change Status</h5>
                </div>
                <div class="card-body">

                    {{-- Success Message --}}
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label"><strong>Current Status</strong></label>
                        <div class="mb-2">

                            @php
                            $badgeClass = match($agent->status) {
                            'active' => 'bg-label-success',
                            'inactive' => 'bg-label-secondary',
                            'on-leave' => 'bg-label-warning',
                            'suspended' => 'bg-label-danger',
                            default => 'bg-label-secondary'
                            };
                            @endphp

                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst($agent->status) }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('driver.update', $agent->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label"><strong>Update Status</strong></label>
                            <select class="form-select" name="status">
                                <option value="">Select Status</option>
                                <option value="active" {{ $agent->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $agent->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="on-leave" {{ $agent->status == 'on-leave' ? 'selected' : '' }}>On Leave</option>
                                <option value="suspended" {{ $agent->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save me-1"></i> Update Status
                        </button>
                    </form>

                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Documents</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <!-- <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i> Driving License (PDF)
                        </a> -->
                        {{-- Driving License --}}
                        @if($agent->driving_license_doc)
                        <a href="{{ asset('storage/' . $agent->driving_license_doc) }}"
                            class="list-group-item list-group-item-action"
                            download>
                            <i class="bx bx-file me-2"></i> Driving License
                        </a>
                        @endif
                        <!-- <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i> Vehicle Registration (PDF)
                        </a> -->
                        {{-- Vehicle Registration --}}
                        @if($agent->vehicle_registration_doc)
                        <a href="{{ asset('storage/' . $agent->vehicle_registration_doc) }}"
                            class="list-group-item list-group-item-action"
                            download>
                            <i class="bx bx-file me-2"></i> Vehicle Registration
                        </a>
                        @endif
                        <!-- <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i> Insurance Certificate (PDF)
                        </a> -->
                        {{-- Insurance --}}
                        @if($agent->insurance_doc)
                        <a href="{{ asset('storage/' . $agent->insurance_doc) }}"
                            class="list-group-item list-group-item-action"
                            download>
                            <i class="bx bx-file me-2"></i> Insurance Certificate
                        </a>
                        @endif
                        <!-- <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i> Aadhar Card (PDF)
                        </a> -->
                        {{-- Aadhar --}}
                        @if($agent->aadhar_doc)
                        <a href="{{ asset('storage/' . $agent->aadhar_doc) }}"
                            class="list-group-item list-group-item-action"
                            download>
                            <i class="bx bx-file me-2"></i> Aadhar Card
                        </a>
                        @endif
                        <!-- <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i> PAN Card (PDF)
                        </a> -->
                        {{-- PAN --}}
                        @if($agent->pan_doc)
                        <a href="{{ asset('storage/' . $agent->pan_doc) }}"
                            class="list-group-item list-group-item-action"
                            download>
                            <i class="bx bx-file me-2"></i> PAN Card
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection