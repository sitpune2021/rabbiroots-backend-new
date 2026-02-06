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
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label"><strong>Status Filter</strong></label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="expired">Expired</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Discount Type</strong></label>
                        <select class="form-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="percentage">Percentage (%)</option>
                            <option value="flat">Flat Amount</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Applicable For</strong></label>
                        <select class="form-select" id="applicableFilter">
                            <option value="">All Stores</option>
                            <option value="all-stores">All Stores</option>
                            <option value="specific-store">Specific Store</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Device Type</strong></label>
                        <select class="form-select" id="deviceFilter">
                            <option value="">All Devices</option>
                            <option value="web">Web</option>
                            <option value="ios">iOS</option>
                            <option value="android">Android</option>
                        </select>
                    </div>
                </div>
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
                        <tr>
                            <td>1</td>
                            <td>
                                <span class="badge bg-label-primary">SAVE20</span>
                            </td>
                            <td>20%</td>
                            <td>₹500</td>
                            <td>₹200</td>
                            <td>Feb 1 - Feb 28</td>
                            <td>
                                <span class="badge bg-label-success">Active</span>
                            </td>
                            <td>156 / 500</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('promo.show', 1) }}"><i
                                                class="icon-base bx bx-show-alt me-1"></i> View</a>
                                        <a class="dropdown-item" href="{{ route('promo.edit', 1) }}"><i
                                                class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"><i
                                                class="icon-base bx bx-trash me-1"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
