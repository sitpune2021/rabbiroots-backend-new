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
                            <h5 class="mb-0">Add Product</h5>
                            <a href="{{ route('product.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <!-- Product Name -->
                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="name">Product Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="e.g., Organic Milk"
                                        value="{{ old('name') }}" required>
                                </div>

                                <!-- Slug -->
                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="slug">Slug (URL)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        id="slug" name="slug" placeholder="organic-milk"
                                        value="{{ old('slug') }}">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="category_id">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="">Select Category</option>
                                        <option value="">A</option>
                                        <option value="">B</option>
                                        <option value="">C</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="subcategory_id">Subcategory</label>
                                    <select class="form-select @error('subcategory_id') is-invalid @enderror" id="subcategory_id"
                                        name="subcategory_id" required>
                                        <option value="">Select Subcategory</option>
                                        <option value="">A</option>
                                        <option value="">B</option>
                                        <option value="">C</option>
                                    </select>
                                </div>

                                <!-- Brand -->
                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="brand_id">Brand</label>
                                    <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id"
                                        name="brand_id" required>
                                        <option value="">Select Brand</option>
                                        <option value="">A</option>
                                        <option value="">B</option>
                                        <option value="">C</option>
                                    </select>
                                </div>

                                <!-- Short Description -->
                                <div class="mb-4">
                                    <label class="form-label" for="short_description">Short Description</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
                                        name="short_description" rows="2" placeholder="Brief product description (max 500 characters)">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label class="form-label" for="description">Product Details</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="5" placeholder="Detailed product description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <h5 class="card-header">Meta Information</h5>
                        <div class="card-body">
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label for="meta_name" class="form-label">Meta Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="meta_name" id="meta_name" class="form-control"
                                        value="{{ old('meta_name') }}" placeholder="Enter meta name" />
                                    @error('meta_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="meta_image" class="form-label">Meta Image <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="meta_image" id="meta_image" class="form-control"
                                        accept="image/*" />
                                    @error('image')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label for="meta_keyword" class="form-label">Meta Keywords <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="meta_keyword" id="meta_keyword" class="form-control"
                                        value="{{ old('meta_keyword') }}" placeholder="comma, separated"
                                        placeholder="Enter Meta Keywords" />
                                    @error('meta_keyword')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="meta_description" class="form-label">Meta Description <span
                                            class="text-danger">*</span></label>
                                    <textarea name="meta_description" id="meta_description" placeholder="Enter meta description" class="form-control"
                                        rows="3">{{ old('meta_description') }}</textarea>
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
