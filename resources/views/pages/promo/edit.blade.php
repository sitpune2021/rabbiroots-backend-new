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

        <form action="{{ route('promo.update', 1) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-12">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="codeName"><strong>Promo Code Name *</strong></label>
                                    <input type="text" class="form-control" id="codeName" name="code_name"
                                        placeholder="e.g., SAVE20" value="SAVE20" required>
                                    <small class="form-text text-muted">Unique code visible to customers</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="description"><strong>Description</strong></label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        placeholder="e.g., Summer Sale 20% Off" value="20% off on all orders">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount Configuration -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Discount Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Discount Type *</strong></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="discount_type"
                                            id="percentageType" value="percentage" checked>
                                        <label class="form-check-label" for="percentageType">
                                            Percentage (%)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="discount_type"
                                            id="flatType" value="flat">
                                        <label class="form-check-label" for="flatType">
                                            Flat Amount (₹)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="discountValue"><strong>Discount Value *</strong></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discountValue"
                                            name="discount_value" placeholder="20" value="20" min="0" required>
                                        <span class="input-group-text" id="discountUnit">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="minOrderValue"><strong>Minimum Order Value
                                            *</strong></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="minOrderValue"
                                            name="min_order_value" placeholder="500" value="500" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="maxDiscountCap"><strong>Max Discount Cap
                                            *</strong></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="maxDiscountCap"
                                            name="max_discount_cap" placeholder="200" value="200" min="0" required>
                                    </div>
                                    <small class="form-text text-muted">Maximum discount amount per order</small>
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
                                    <label class="form-label" for="startDate"><strong>Start Date *</strong></label>
                                    <input type="date" class="form-control" id="startDate" name="start_date"
                                        value="2026-02-01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="endDate"><strong>End Date *</strong></label>
                                    <input type="date" class="form-control" id="endDate" name="end_date"
                                        value="2026-02-28" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="startTime"><strong>Active From (Time)</strong></label>
                                    <input type="time" class="form-control" id="startTime" name="active_start_time"
                                        value="12:00">
                                    <small class="form-text text-muted">Time-based activation (optional)</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="endTime"><strong>Active Until (Time)</strong></label>
                                    <input type="time" class="form-control" id="endTime" name="active_end_time"
                                        value="14:00">
                                    <small class="form-text text-muted">e.g., Lunch rush 12-2 PM</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="status"><strong>Status</strong></label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="draft">Draft</option>
                                        <option value="active" selected>Active</option>
                                        <option value="scheduled">Scheduled (Pending Approval)</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applicability & Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Applicability Filters & Store Selection</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Applicable to Stores *</strong></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="store_type"
                                            id="allStores" value="all_stores" checked>
                                        <label class="form-check-label" for="allStores">
                                            All Stores
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="store_type"
                                            id="specificStore" value="specific_store">
                                        <label class="form-check-label" for="specificStore">
                                            Specific Store(s)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>User Applicability</strong></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="new_users"
                                            id="newUsers" value="1" checked>
                                        <label class="form-check-label" for="newUsers">
                                            New Users Only (Orders < 2 this month)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="repeat_users"
                                            id="repeatUsers" value="1" checked>
                                        <label class="form-check-label" for="repeatUsers">
                                            Repeat Users
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Store Selection -->
                            <div id="storeSelection" style="display: none;">
                                <hr class="my-3">
                                <label class="form-label"><strong>Select Stores to Apply Promo</strong></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="stores" value="store1"
                                                id="store1" checked>
                                            <label class="form-check-label" for="store1">
                                                <i class="bx bx-store me-1"></i> Store 1 - Mumbai
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="stores" value="store2"
                                                id="store2" checked>
                                            <label class="form-check-label" for="store2">
                                                <i class="bx bx-store me-1"></i> Store 2 - Delhi
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="stores" value="store3"
                                                id="store3">
                                            <label class="form-check-label" for="store3">
                                                <i class="bx bx-store me-1"></i> Store 3 - Bangalore
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="stores" value="store4"
                                                id="store4">
                                            <label class="form-check-label" for="store4">
                                                <i class="bx bx-store me-1"></i> Store 4 - Pune
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Device & Campaign Configuration -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Device & Campaign Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Device-Specific Campaign *</strong></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="device_web"
                                            id="deviceWeb" value="1" checked>
                                        <label class="form-check-label" for="deviceWeb">
                                            <i class="bx bx-globe"></i> Web
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="device_ios"
                                            id="deviceIOS" value="1" checked>
                                        <label class="form-check-label" for="deviceIOS">
                                            <i class="bx bxl-apple"></i> iOS
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="device_android"
                                            id="deviceAndroid" value="1" checked>
                                        <label class="form-check-label" for="deviceAndroid">
                                            <i class="bx bxl-android"></i> Android
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Usage Rules</strong></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usage_type"
                                            id="singleUse" value="single_use" checked>
                                        <label class="form-check-label" for="singleUse">
                                            Single-Use Per User
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usage_type"
                                            id="multiUse" value="multi_use">
                                        <label class="form-check-label" for="multiUse">
                                            Multi-Use (Limit Per User)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="usageLimit"><strong>Usage Limit</strong></label>
                                    <input type="number" class="form-control" id="usageLimit"
                                        name="usage_limit_per_user" placeholder="5" value="1" min="1">
                                    <small class="form-text text-muted">How many times a user can use this code</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="totalRedemptions"><strong>Total Redemption
                                            Limit</strong></label>
                                    <input type="number" class="form-control" id="totalRedemptions"
                                        name="total_redemptions_limit" placeholder="500" value="500" min="1">
                                    <small class="form-text text-muted">Maximum total redemptions allowed</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Pricing Rules -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Dynamic Pricing Rules</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <i class="bx bx-info-circle me-2"></i> Apply additional base price adjustments during
                                rush hours
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Rush Hour Base Price Adjustment</strong></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="apply_rush_pricing"
                                            id="rushPricing" value="1">
                                        <label class="form-check-label" for="rushPricing">
                                            Enable Dynamic Pricing during Rush Hours
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="morningRush"><strong>Morning Rush (6-9
                                            AM)</strong></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="morningRush"
                                            name="morning_rush_adjustment" placeholder="10" value="0" min="-100"
                                            max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="lunchRush"><strong>Lunch Rush (12-2
                                            PM)</strong></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="lunchRush"
                                            name="lunch_rush_adjustment" placeholder="15" value="15" min="-100"
                                            max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="eveningRush"><strong>Evening Rush (6-9
                                            PM)</strong></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="eveningRush"
                                            name="evening_rush_adjustment" placeholder="12" value="0" min="-100"
                                            max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2" style="margin-top:20px;">
                        <button type="submit" class="btn btn-primary w-auto">
                            <i class="bx bx-save me-1"></i> Update
                        </button>

                        <a href="{{ route('promo.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>

                </div>
            </div>
        </form>

    </div>
@endsection
