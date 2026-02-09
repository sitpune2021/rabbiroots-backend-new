@extends('layouts.app')
@push('scripts')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Form controls -->
            <div class="col-md-12">
                <form action="{{ route('brand.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                         <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add Brand</h5>
                            <a href="{{ route('brand.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name') }}" placeholder="Enter brand name" />
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        value="{{ old('slug') }}" placeholder="Enter category slug" />
                                    @error('slug')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="logo" class="form-label">Logo <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="logo" id="logo" class="form-control"
                                        accept="image/*" />
                                    @error('logo')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span
                                            class="text-danger">*</span></label>
                                    <textarea placeholder="Enter description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3 d-flex align-items-center">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1" @if (old('is_active')) checked @endif>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-end gap-2" style="margin-top:20px;">
                        <button type="submit" class="btn btn-primary w-auto">
                            <i class="bx bx-save me-1"></i> Add
                        </button>

                        <a href="{{ route('category.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection
