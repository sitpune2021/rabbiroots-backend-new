@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="fw-bold mb-1">John Doe</h4>
            <p class="text-muted mb-0"><small>Customer ID: #CUST-00001</small></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('customer.edit', 1) }}" class="btn btn-primary btn-sm me-2">
                Edit Profile
            </a>
            <a href="{{ route('customer.index') }}" class="btn btn-outline-secondary btn-sm">
                Back
            </a>
        </div>
    </div>


    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Customer Information Card -->
            <div class="card border-0 mb-4">
                <div class="card-header bg-light border-0 pb-3 pt-3">
                    <h6 class="m-0 fw-bold">Customer Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Full Name</small>
                            <p class="fw-bold mb-0">John Doe</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Email Address</small>
                            <p class="fw-bold mb-0">john.doe@example.com</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Phone Number</small>
                            <p class="fw-bold mb-0">+1 (555) 123-4567</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Account Status</small>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block mb-1">Street Address</small>
                            <p class="fw-bold mb-0">123 Main Street, New York, NY 10001</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Statistics -->
        <div class="col-md-4">
            <div class="card border-0 mb-4">
                <div class="card-header bg-light border-0 pb-3 pt-3">
                    <h6 class="m-0 fw-bold">Account Summary</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Total Orders</small>
                        <p class="fw-bold mb-0">12 orders</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Total Spent</small>
                        <p class="fw-bold mb-0">$2,450.50</p>
                    </div>
                    <div class="pt-3 border-top">
                        <small class="text-muted d-block mb-1">Member Since</small>
                        <p class="fw-bold small mb-0">15 January 2024</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Recent Orders Full Width -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="card border-0">
               
                <div class="card-header border-0 pb-3 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold">Order History</h6>

                <!-- Status Filter Dropdown -->
                <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="orderStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="orderStatusDropdown">
                            <li><a class="dropdown-item" href="#" data-status="all">All</a></li>
                            <li><a class="dropdown-item" href="#" data-status="delivered">Delivered</a></li>
                            <li><a class="dropdown-item" href="#" data-status="in-transit">In Transit</a></li>
                            <li><a class="dropdown-item" href="#" data-status="cancelled">Cancelled</a></li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive px-3">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-bold">Order ID</th>
                                <th class="fw-bold">Date</th>
                                <th class="fw-bold">Items</th>
                                <th class="fw-bold">Payment</th>
                                <th class="fw-bold">Amount</th>
                                <th class="fw-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-bottom">
                                <td class="fw-bold">#ORD-001424</td>
                                <td class="text-muted">08 Feb 2024</td>
                                <td><span class="badge bg-light-primary">5 items</span></td>
                                <td>Visa Card</td>
                                <td class="fw-bold">$245.50</td>
                                <td><span class="badge bg-success">Delivered</span></td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-bold">#ORD-001423</td>
                                <td class="text-muted">05 Feb 2024</td>
                                <td><span class="badge bg-light-secondary">3 items</span></td>
                                <td>Mastercard</td>
                                <td class="fw-bold">$185.00</td>
                                <td><span class="badge bg-info">In Transit</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">#ORD-001422</td>
                                <td class="text-muted">01 Feb 2024</td>
                                <td><span class="badge bg-light-secondary">8 items</span></td>
                                <td>Visa Card</td>
                                <td class="fw-bold">$320.75</td>
                                <td><span class="badge bg-success">Delivered</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews & Support Side by Side -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <!-- Reviews & Ratings -->
            <div class="card border-0 h-100">
                <div class="card-header bg-light border-0 pb-3 pt-3">
                    <h6 class="m-0 fw-bold">Reviews & Ratings</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Average Rating</small>
                        <p class="fw-bold mb-0">4.5 out of 5.0 <span class="badge bg-light-warning">12 reviews</span></p>
                    </div>
                    <div class="pt-3 border-top">
                        <small class="text-muted d-block mb-2">Latest Review</small>
                        <p class="mb-2 small">"Excellent products and fast delivery. Highly recommend."</p>
                        <small class="text-muted">8 February 2024</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <!-- Support Details -->
            <div class="card border-0 h-100">
                <div class="card-header bg-light border-0 pb-3 pt-3">
                    <h6 class="m-0 fw-bold">Support & Tickets</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Open Tickets</small>
                        <p class="fw-bold mb-0">1 active <span class="badge bg-light-warning">In Progress</span></p>
                    </div>
                    <div class="pt-3 border-top">
                        <small class="text-muted d-block mb-2">Latest Request</small>
                        <p class="fw-bold small mb-1">Order Tracking #ORD-001423</p>
                        <small class="text-muted">Submitted on 6 February 2024</small>
                    </div>
                    <div class="pt-3 border-top">
                        <small class="text-muted d-block mb-1">Preferred Contact</small>
                        <p class="fw-bold small mb-0">Email & Phone</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
