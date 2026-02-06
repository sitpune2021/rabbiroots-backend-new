@php
    $isEdit = isset($category);
@endphp

<form
    action="{{ $isEdit ? route('category.update', $category->id) : route('category.store') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- BASIC INFORMATION --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $isEdit ? 'Edit' : 'Add' }} Category</h5>
        </div>

        <div class="card-body">
            <div class="row">

                {{-- NAME --}}
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name *</label>
                    <input id="name" type="text" name="name" class="form-control"
                           value="{{ old('name', $category->name ?? '') }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- SLUG --}}
                <div class="col-md-6 mb-3">
                    <label for="slug" class="form-label">Slug *</label>
                    <input id="slug" type="text" name="slug" class="form-control"
                           value="{{ old('slug', $category->slug ?? '') }}" required>
                    @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- LEVEL --}}
                <div class="col-md-6 mb-3">
                    <label for="level" class="form-label">Level *</label>
                    <select id="level" name="level" class="form-select" required>
                        @foreach(['main','sub','child'] as $level)
                            <option value="{{ $level }}"
                                @selected(old('level', $category->level ?? '') === $level)>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>
                    @error('level') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- PARENT CATEGORY --}}
                <div class="col-md-6 mb-3">
                    <label for="parent_id" class="form-label">Parent Category</label>
                    <select id="parent_id" name="parent_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($parents ?? [] as $parent)
                            <option value="{{ $parent->id }}"
                                @selected(old('parent_id', $category->parent_id ?? '') == $parent->id)>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SORT ORDER --}}
                <div class="col-md-4 mb-3">
                    <label for="sort_order" class="form-label">Sort Order *</label>
                    <input id="sort_order" type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0" required>
                </div>

                {{-- ICON --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label">Icon</label>
                    <input type="file" name="icon" class="form-control" accept="image/*">
                    @if(!empty($category?->icon))
                        <img src="{{ asset('storage/'.$category->icon) }}" class="img-thumbnail mt-2" style="max-height:60px;">
                    @endif
                </div>

                {{-- IMAGE --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    @if(!empty($category?->image))
                        <img src="{{ asset('storage/'.$category->image) }}" class="img-thumbnail mt-2" style="max-height:60px;">
                    @endif
                </div>

                {{-- ACTIVE --}}
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1"
                               @checked(old('is_active', $category->is_active ?? true))>
                        <label class="form-check-label">Active</label>
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
                    <input type="text" name="meta_title" class="form-control"
                           value="{{ old('meta_title', $category->meta_title ?? '') }}">
                </div>

                {{-- META KEYWORDS --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control"
                           value="{{ old('meta_keywords', $category->meta_keywords ?? '') }}">
                </div>

                {{-- META DESCRIPTION --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="form-control">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                </div>

                {{-- CANONICAL URL --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Canonical URL</label>
                    <input type="text" name="canonical_url" class="form-control"
                           value="{{ old('canonical_url', $category->canonical_url ?? '') }}">
                </div>

                {{-- OG IMAGE --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">OG Image</label>
                    <input type="file" name="og_image" class="form-control" accept="image/*">
                    @if(!empty($category?->og_image))
                        <img src="{{ asset('storage/'.$category->og_image) }}" class="img-thumbnail mt-2" style="max-height:60px;">
                    @endif
                </div>

                {{-- INDEXABLE --}}
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="is_indexable" value="1"
                               @checked(old('is_indexable', $category->is_indexable ?? true))>
                        <label class="form-check-label">Allow Indexing</label>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Update' : 'Save' }}
        </button>
        <a href="{{ route('category.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

{{-- SCRIPTS --}}
@push('scripts')
<script>
    // Auto slug generation
    document.getElementById('name').addEventListener('input', function () {
        document.getElementById('slug').value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    });

    // Level ↔ Parent logic
    const level = document.getElementById('level');
    const parent = document.getElementById('parent_id');

    function toggleParent() {
        if (level.value === 'main') {
            parent.value = '';
            parent.setAttribute('disabled', true);
        } else {
            parent.removeAttribute('disabled');
        }
    }

    level.addEventListener('change', toggleParent);
    toggleParent();
</script>

<script>
document.querySelector('form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    // Clear old errors
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

    fetch(form.action, {
        method: form.method === 'POST' ? 'POST' : 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        if (response.status === 422) {
            const data = await response.json();

            // Show validation errors
            Object.keys(data.errors).forEach(field => {
                const errorEl = document.querySelector(`.${field}_error`);
                if (errorEl) {
                    errorEl.textContent = data.errors[field][0];
                }
            });

            throw new Error('Validation failed');
        }

        return response.json().catch(() => ({}));
    })
    .then(() => {
        // SUCCESS
        window.location.href = "{{ route('category.index') }}";
    })
    .catch(error => {
        console.warn(error.message);
    });
});
</script>

@endpush
