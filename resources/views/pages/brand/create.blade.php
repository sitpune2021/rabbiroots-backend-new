@extends('layouts.app')
@push('scripts')
<script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">
        <!-- Form controls -->
        <div class="col-md-12">
            @php
            $isAdd = $mode === 'add';
            $isEdit = $mode === 'edit';
            $isView = $mode === 'view';
            @endphp
            <form action="{{ 
        $isAdd ? route('brand.store') : 
        ($isEdit ? route('brand.update', $brand->id) : '#') 
    }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($isEdit)
                @method('PUT')
                @endif
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> {{ $isAdd ? 'Add Brand' : ($isEdit ? 'Edit Brand' : 'View Brand') }}</h5>
                        <a href="{{ route('brand.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $brand->name ?? '') }}" placeholder="Enter brand name"
                                    {{ $isView ? 'disabled' : '' }} />
                                @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="slug" class="form-control"
                                    value="{{ old('slug', $brand->slug ?? '') }}"
                                    {{ $isView ? 'disabled' : '' }}
                                    placeholder="Enter brand slug" />
                                @error('slug')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="logo" class="form-label">
                                    Logo <span class="text-danger">*</span>
                                </label>

                                {{-- File upload only for add/edit --}}
                                @if(!$isView)
                                <input
                                    type="file"
                                    name="logo"
                                    id="logo"
                                    class="form-control"
                                    accept="image/*">

                                @error('logo')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                @endif

                                {{-- Show existing logo --}}
                                @if(!empty($brand?->logo))
                                <div class="mt-2">
                                    <img
                                        src="{{ asset('storage/' . $brand->logo) }}"
                                        alt="Brand Logo"
                                        class="rounded border"
                                        style="max-width:150px;">
                                </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span
                                        class="text-danger">*</span></label>
                                <textarea placeholder="Enter description" id="description"
                                    name="description"
                                    class="form-control" rows="4"
                                    {{ $isView ? 'disabled' : '' }}>{{ old('description', $brand->description ?? '') }}</textarea>
                                @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="is_active" value="0">
                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox"
                                        id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $brand->is_active ?? 1) ? 'checked' : '' }}
                                        {{ $isView ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO INFORMATION --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Information</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            {{-- META TITLE --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" placeholder="Meta Title"
                                    value="{{ old('meta_title', $brand->meta_title ?? '') }}">
                            </div>

                            {{-- META KEYWORDS --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" placeholder="Meta Keywords"
                                    value="{{ old('meta_keywords', $brand->meta_keywords ?? '') }}">
                            </div>

                            {{-- META DESCRIPTION --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" rows="3"
                                placeholder="Meta Description" 
                                class="form-control">{{ old('meta_description', $brand->meta_description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2" style="margin-top:20px;">
                    @if($isAdd)
                    <button type="submit" class="btn btn-primary w-auto">
                        <i class="bx bx-save me-1"></i> Add
                    </button>
                    @endif

                    @if($isEdit)
                    <button type="submit" class="btn btn-primary w-auto">
                        <i class="bx bx-save me-1"></i> Update
                    </button>
                    @endif

                    <a href="{{ route('brand.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        if (!nameInput || !slugInput) return;

        nameInput.addEventListener('input', function() {
            // Do not overwrite slug if user manually edits it
            if (slugInput.dataset.manual === "true") return;

            slugInput.value = generateSlug(this.value);
        });

        slugInput.addEventListener('input', function() {
            // Mark slug as manually edited
            this.dataset.manual = "true";
        });

        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '') // remove special chars
                .replace(/\s+/g, '-') // spaces to hyphen
                .replace(/-+/g, '-'); // remove duplicate hyphens
        }
    });
</script>
@endpush