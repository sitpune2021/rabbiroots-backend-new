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
                                    <p class="text-muted">John Smith</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Email</strong></label>
                                    <p class="text-muted">john.smith@example.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Phone Number</strong></label>
                                    <p class="text-muted">+1 (555) 123-4567</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Date of Birth</strong></label>
                                    <p class="text-muted">January 15, 1990</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Aadhar Number</strong></label>
                                    <p class="text-muted">1234-5678-9012</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>PAN Number</strong></label>
                                    <p class="text-muted">AAAPJ5055K</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Permanent Address</strong></label>
                                    <p class="text-muted">456 Oak Lane, London, UK SW1A 2AA</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Temporary Address</strong></label>
                                    <p class="text-muted">123 Main Street, Apartment 4B, New York, NY 10001</p>
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
                                    <p class="text-muted">DL-2024-001234</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>License Type</strong></label>
                                    <p class="text-muted">Commercial (LMV)</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Issue Date</strong></label>
                                    <p class="text-muted">March 10, 2019</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Expiry Date</strong></label>
                                    <p class="text-muted">March 10, 2029</p>
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
                                    <h6 class="text-success">125 Orders</h6>
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
                                    <p class="text-muted">Toyota Hiace</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Vehicle Model</strong></label>
                                    <p class="text-muted">2020</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>License Plate</strong></label>
                                    <p class="text-muted">ABC-1234</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Vehicle Type</strong></label>
                                    <p class="text-muted">Van</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Capacity (kg)</strong></label>
                                    <p class="text-muted">2500</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Registration Number</strong></label>
                                    <p class="text-muted">REG-2024-001234</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Insurance Policy Number</strong></label>
                                    <p class="text-muted">INS-2024-98765432</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
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
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                <i class="bx bx-file me-2"></i> Driving License (PDF)
                            </a>
                            <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                <i class="bx bx-file me-2"></i> Vehicle Registration (PDF)
                            </a>
                            <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                <i class="bx bx-file me-2"></i> Insurance Certificate (PDF)
                            </a>
                            <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                <i class="bx bx-file me-2"></i> Aadhar Card (PDF)
                            </a>
                            <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                <i class="bx bx-file me-2"></i> PAN Card (PDF)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
