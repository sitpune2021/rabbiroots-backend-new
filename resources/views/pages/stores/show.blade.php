@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-3">
                    <h6 class="m-0">Store Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Store Name</label>
                                <p class="fw-bold">Downtown Store</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Location</label>
                                <p class="fw-bold">Downtown</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Phone</label>
                                <p class="fw-bold">+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <p>
                                    <span class="badge bg-success">Active</span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Created</label>
                                <p class="fw-bold">15 Jan 2024 08:30</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Last Updated</label>
                                <p class="fw-bold">15 Jan 2024 08:30</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Address</label>
                            <p class="fw-bold">123 Main Street, New York, NY 10001</p>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Latitude</label>
                            <p class="fw-bold">40.7128</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Longitude</label>
                            <p class="fw-bold">-74.0060</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
