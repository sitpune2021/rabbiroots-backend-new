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
                    <div class="card mb-4">
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
                                    <label class="form-label" for="name">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="e.g., Organic Milk" value="{{ old('name') }}" required>
                                </div>

                                <!-- Slug -->
                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="slug">Slug (URL) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" placeholder="organic-milk" value="{{ old('slug') }}">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="">Select Category</option>
                                        <option value="">A</option>
                                        <option value="">B</option>
                                        <option value="">C</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="subcategory_id">Subcategory <span class="text-danger">*</span></label>
                                    <select class="form-select @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id" required>
                                        <option value="">Select Subcategory</option>
                                        <option value="">A</option>
                                        <option value="">B</option>
                                        <option value="">C</option>
                                    </select>
                                </div>

                                <!-- Brand -->
                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="brand_id">Brand <span class="text-danger">*</span></label>
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
                                    <label class="form-label" for="short_description">Short Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
                                        name="short_description" rows="2" placeholder="Brief product description (max 500 characters)">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label class="form-label" for="description">Product Details <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="5" placeholder="Detailed product description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Product Images <span class="text-danger">*</span></h5>
                        </div>
                        <div class="card-body">
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                id="images" name="images[]" accept="image/*" multiple required
                                onchange="previewImages(event)">
                            @error('images.*')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                            <div class="form-text">You can select multiple images. First image will be the primary image.
                            </div>

                            <!-- Images Preview -->
                            <div id="imagesPreview" class="row mt-3 g-2"></div>
                        </div>
                    </div>

                    <!-- Product Variants -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Product Variants <span class="text-danger">*</span></h5>
                            <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                                <i class="bx bx-plus me-1"></i> Add Variant
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="variantsContainer">
                                <!-- Variant rows will be added here -->
                            </div>
                            <small class="text-muted">Add at least one variant (e.g., 250g, 500g, 1kg, Pack of 6)</small>
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
