@extends('layouts.app')
@push('scripts')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Form controls -->
            <div class="col-md-12">
                <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                         <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add Subcategory</h5>
                            <a href="{{ route('subcategory.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="mb-4 col-md-6">
                                    <label for="defaultSelect" class="form-label">Category</label>
                                    <select id="defaultSelect" class="form-select">
                                    <option>Select category</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name') }}" placeholder="Enter category name" />
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
                                    <label for="image" class="form-label">Image <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="image" id="image" class="form-control"
                                        accept="image/*" />
                                    @error('image')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-6 mb-3">
                                    <label for="display_order" class="form-label">Display Order <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="display_order" id="display_order" class="form-control"
                                        value="{{ old('display_order', 0) }}" min="0" placeholder="Enter display order" />
                                    @error('display_order')
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

                    <div class="card mt-4">
                        <h5 class="card-header">Meta Information</h5>
                        <div class="card-body">
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label for="meta_name" class="form-label">Meta Name <span class="text-danger">*</span></label>
                                    <input type="text" name="meta_name" id="meta_name" class="form-control"
                                        value="{{ old('meta_name') }}" placeholder="Enter meta name"/>
                                    @error('meta_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="meta_image" class="form-label">Meta Image <span class="text-danger">*</span></label>
                                    <input type="file" name="meta_image" id="meta_image" class="form-control"
                                        accept="image/*" />
                                    @error('image')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label for="meta_keyword" class="form-label">Meta Keywords <span class="text-danger">*</span></label>
                                    <input type="text" name="meta_keyword" id="meta_keyword" class="form-control"
                                        value="{{ old('meta_keyword') }}" placeholder="comma, separated" placeholder="Enter Meta Keywords"/>
                                    @error('meta_keyword')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="meta_description" class="form-label">Meta Description <span class="text-danger">*</span></label>
                                    <textarea name="meta_description" id="meta_description" placeholder="Enter meta description" class="form-control" rows="3">{{ old('meta_description') }}</textarea>
                                    @error('meta_description')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
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
