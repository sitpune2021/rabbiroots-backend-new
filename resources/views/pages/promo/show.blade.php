@extends('layouts.app')
@push('scripts')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('promo.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Promo Code Information -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Promo Code Information</h5>
                        <a href="{{ route('promo.edit', 1) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Code Name</strong></label>
                                    <p class="text-muted fs-5"><span class="badge bg-primary">SAVE20</span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Description</strong></label>
                                    <p class="text-muted">20% off on all orders</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Status</strong></label>
                                    <p class="text-muted"><span class="badge bg-label-success">Active</span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Created On</strong></label>
                                    <p class="text-muted">January 28, 2026 at 14:35</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discount Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Discount Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Discount Type</strong></label>
                                    <p class="text-muted">Percentage (%)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Discount Value</strong></label>
                                    <p class="text-muted"><span class="badge bg-success">20%</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Minimum Order Value</strong></label>
                                    <p class="text-muted">₹500</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Max Discount Cap</strong></label>
                                    <p class="text-muted">₹200</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validity & Scheduling -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Validity & Scheduling</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Valid From</strong></label>
                                    <p class="text-muted">February 1, 2026</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Valid Until</strong></label>
                                    <p class="text-muted">February 28, 2026</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Active Time Range</strong></label>
                                    <p class="text-muted">12:00 PM to 2:00 PM (Lunch Rush)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Days Remaining</strong></label>
                                    <p class="text-muted"><span class="badge bg-warning">22 Days</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applicability -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Applicability & Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Applicable to Stores</strong></label>
                                    <p class="text-muted">All Stores</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Device Support</strong></label>
                                    <p class="text-muted">
                                        <span class="badge bg-info-lighter text-info">Web</span>
                                        <span class="badge bg-info-lighter text-info">iOS</span>
                                        <span class="badge bg-info-lighter text-info">Android</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>For New Users</strong></label>
                                    <p class="text-muted">Orders < 2 this month</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>For Repeat Users</strong></label>
                                    <p class="text-muted"><i class="bx bx-check text-success me-1"></i> Applicable</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage Rules -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Usage Rules & Limits</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Usage Type</strong></label>
                                    <p class="text-muted">Single-Use Per User</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Per User Limit</strong></label>
                                    <p class="text-muted">1 time</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Total Redemption Limit</strong></label>
                                    <p class="text-muted">500 times</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-4">
                <!-- Analytics Overview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Analytics Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <p class="text-muted mb-0">Total Redemptions</p>
                                <h4 class="mb-0 text-success">156</h4>
                            </div>
                            <div class="text-end">
                                <p class="text-muted mb-0">Limit: 500</p>
                                <div class="progress" style="width: 100px;">
                                    <div class="progress-bar" role="progressbar" style="width: 31.2%;"
                                        aria-valuenow="31.2" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <p class="text-muted mb-0">Redemption Rate</p>
                                <h4 class="mb-0 text-info">31.2%</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <p class="text-muted mb-0">Today's Redemptions</p>
                                <h4 class="mb-0">8</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Impact -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Financial Impact</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <p class="text-muted mb-1">Total Discount Distributed</p>
                            <h5 class="mb-0 text-danger">₹26,340</h5>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <p class="text-muted mb-1">Average Discount per Order</p>
                            <h5 class="mb-0">₹168.97</h5>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <p class="text-muted mb-1">GMV (Gross Merchandise Value)</p>
                            <h5 class="mb-0 text-success">₹2,45,600</h5>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1">Revenue After Discount</p>
                            <h5 class="mb-0">₹2,19,260</h5>
                        </div>
                    </div>
                </div>

                <!-- ROI & Performance -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">ROI & Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <p class="text-muted mb-1">Return on Investment (ROI)</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-success">156.8%</h5>
                                <span class="badge bg-success">Excellent</span>
                            </div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <p class="text-muted mb-1">Avg Order Value Lift</p>
                            <h5 class="mb-0">₹1,574 (+15%)</h5>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1">New Customers Acquired</p>
                            <h5 class="mb-0">24</h5>
                        </div>
                    </div>
                </div>

                <!-- Redemption by Device -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Redemption by Device</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small><i class="bx bx-globe"></i> Web</small>
                                <small>48 (30.8%)</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" style="width: 30.8%;"
                                    aria-valuenow="30.8" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small><i class="bx bxl-apple"></i> iOS</small>
                                <small>62 (39.7%)</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 39.7%;"
                                    aria-valuenow="39.7" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small><i class="bx bxl-android"></i> Android</small>
                                <small>46 (29.5%)</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 29.5%;"
                                    aria-valuenow="29.5" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
