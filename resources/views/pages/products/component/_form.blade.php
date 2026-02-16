@php
    $isEdit = isset($product);
@endphp

<form id="productForm" method="POST"
    action="{{ $isEdit ? route('products.update', $product->id) : route('products.store') }}"
    enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- ================= BASIC INFO ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-semibold">🧾 Basic Information</h5>
            <small class="text-muted">Core product identity & classification</small>
        </div>

        <div class="card-body">
            <div class="row g-3">

                {{-- Product Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Product Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="e.g. Budhani Potato Chips" value="{{ old('name', $product->name ?? '') }}"
                        required>
                    <div class="form-text">
                        Display name shown to customers
                    </div>
                </div>

                {{-- Slug --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Slug <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="slug" id="slug" class="form-control"
                        placeholder="budhani-potato-chips" value="{{ old('slug', $product->slug ?? '') }}" required>
                    <div class="form-text">
                        Auto-generated SEO URL (editable)
                    </div>
                </div>

                {{-- Category --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Category <span class="text-danger">*</span>
                    </label>
                    <select name="category_id" class="form-select" required>
                        <option value="">— Select Category —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                                @php
                                    $main = $cat->parent?->parent;
                                    $sub = $cat->parent;
                                @endphp
                                {{ $cat->name }} >
                                {{ $sub?->name }}
                                {{ $main?->name ? ' > ' . $main?->name : '' }}
                        @endforeach
                    </select>
                </div>

                {{-- Brand --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Brand <span class="text-danger">*</span>
                    </label>
                    <select name="brand_id" class="form-select" required>
                        <option value="">— Select Brand —</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Type --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Product Type</label>
                    <select name="product_type" class="form-select">
                        @foreach (['grocery', 'fresh', 'frozen', 'pharma'] as $type)
                            <option value="{{ $type }}" @selected(old('product_type', $product->product_type ?? '') === $type)>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Used for logistics & compliance rules
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= PRODUCT IMAGES ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-semibold">🖼 Product Images</h5>
            <small class="text-muted">Visuals shown on product listing & details</small>
        </div>

        <div class="card-body">

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Upload Images
                </label>
                <input type="file" name="product_images[]" multiple accept="image/*" class="form-control"
                    onchange="previewImages(this)">
                <div class="form-text">
                    First image becomes the <strong>primary thumbnail</strong>
                </div>
            </div>

            {{-- Image Preview --}}
            {{-- <div id="image-preview" class="d-flex flex-wrap gap-3 mt-3">
            </div> --}}

            <div id="image-preview" class="d-flex flex-wrap gap-3 mt-3">

                @if ($isEdit && $product->images)
                    @foreach ($product->images as $img)
                        <div class="position-relative existing-image">

                            <img src="{{ asset('storage/' . $img->image) }}" class="rounded border"
                                style="width:110px;height:110px;object-fit:cover;">

                            {{-- Primary Badge --}}
                            @if ($img->is_primary)
                                <span class="badge bg-primary position-absolute top-0 start-0">
                                    Primary
                                </span>
                            @endif

                            {{-- Remove Button --}}
                            <button type="button"
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-product-image"
                                data-id="{{ $img->id }}">
                                ✕
                            </button>

                            {{-- Hidden field to keep image --}}
                            <input type="hidden" name="existing_product_images[]" value="{{ $img->id }}">
                        </div>
                    @endforeach
                @endif

            </div>


        </div>
    </div>

    {{-- ================= FLAGS ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-semibold">🚦 Product Flags</h5>
            <small class="text-muted">Operational & food-handling properties</small>
        </div>

        <div class="card-body">
            <div class="row g-3">

                @php
                    $flags = [
                        'is_perishable' => [
                            'label' => 'Perishable',
                            'icon' => '⏳',
                            'hint' => 'Expires quickly, needs expiry tracking',
                        ],
                        'requires_cold_storage' => [
                            'label' => 'Cold Storage',
                            'icon' => '❄️',
                            'hint' => 'Must be stored below temperature threshold',
                        ],
                        'is_fragile' => [
                            'label' => 'Fragile',
                            'icon' => '📦',
                            'hint' => 'Handle carefully during picking & delivery',
                        ],
                        'is_veg' => [
                            'label' => 'Vegetarian',
                            'icon' => '🥬',
                            'hint' => 'Veg-friendly product',
                        ],
                        'contains_allergens' => [
                            'label' => 'Contains Allergens',
                            'icon' => '⚠️',
                            'hint' => 'Show allergen warning to customers',
                        ],
                    ];
                @endphp

                @foreach ($flags as $key => $data)
                    <div class="col-md-4 col-lg-3">
                        <label class="flag-card w-100">
                            <input type="checkbox" name="{{ $key }}" value="1" class="d-none flag-input"
                                @checked(old($key, $product->$key ?? false))>

                            <div class="border rounded p-3 h-100 text-center flag-box">
                                <div class="fs-2">{{ $data['icon'] }}</div>
                                <div class="fw-semibold mt-2">{{ $data['label'] }}</div>
                                <small class="text-muted d-block mt-1">
                                    {{ $data['hint'] }}
                                </small>
                            </div>
                        </label>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- ================= DESCRIPTION ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-semibold">📝 Product Description</h5>
            <small class="text-muted">Customer-facing content</small>
        </div>

        <div class="card-body">

            {{-- Short Description --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Short Description
                </label>
                <textarea name="short_description" class="form-control" rows="2" maxlength="160"
                    placeholder="One-line summary shown in product listing">{{ old('short_description', $product->short_description ?? '') }}</textarea>

                <div class="form-text">
                    Recommended: 80–120 characters (used in listing cards)
                </div>
            </div>

            {{-- Full Description --}}
            <div>
                <label class="form-label fw-semibold">
                    Full Description
                </label>
                <textarea name="description" class="form-control" rows="6"
                    placeholder="Detailed product description, usage, highlights">{{ old('description', $product->description ?? '') }}</textarea>

                <div class="form-text">
                    Appears on product detail page
                </div>
            </div>

        </div>
    </div>

    {{-- ================= VARIANTS ================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
            data-bs-target="#variantsBox" style="cursor:pointer">
            <h5 class="mb-0">Product Variants</h5>
            <span class="text-muted small">SKU • Pricing • Quantity</span>
        </div>

        <div id="variantsBox" class="collapse show">
            <div class="card-body">

                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                        + Add Variant
                    </button>
                </div>

                <div id="variants-wrapper"></div>

                {{-- TEMPLATE --}}
                <template id="variant-template">
                    <div class="card mb-3 variant-item border-start border-4 border-primary">
                        <div class="card-body">

                            {{-- Header --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <strong>Variant</strong>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="this.closest('.variant-item').remove()">
                                    Remove
                                </button>
                            </div>

                            {{-- SKU / PACK --}}
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">SKU *</label>
                                    <input name="variants[__i__][sku]" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Barcode</label>
                                    <input name="variants[__i__][barcode]" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pack Size</label>
                                    <input name="variants[__i__][pack_size]" class="form-control" placeholder="200g">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <input name="variants[__i__][unit]" class="form-control"
                                        placeholder="g / kg / ml">
                                </div>
                            </div>

                            {{-- PRICING --}}
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">MRP *</label>
                                    <input type="number" step="0.01" name="variants[__i__][mrp]"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Selling Price *</label>
                                    <input type="number" step="0.01" name="variants[__i__][selling_price]"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cost Price</label>
                                    <input type="number" step="0.01" name="variants[__i__][cost_price]"
                                        class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tax %</label>
                                    <input type="number" step="0.01" name="variants[__i__][tax_percent]"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- QUANTITY + STATUS --}}
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Min Order Qty</label>
                                    <input type="number" name="variants[__i__][min_order_qty]" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Max Order Qty</label>
                                    <input type="number" name="variants[__i__][max_order_qty]" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input default-variant" type="checkbox"
                                            name="variants[__i__][is_default]" value="1">
                                        <label class="form-check-label">
                                            Default Variant
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox"
                                            name="variants[__i__][is_active]" value="1" checked>
                                        <label class="form-check-label">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- IMAGES --}}
                            <div class="mb-2">
                                <label class="form-label">Variant Images</label>
                                <input type="file" name="variants[__i__][images][]" multiple class="form-control">
                                <small class="text-muted">
                                    First image will be treated as primary
                                </small>
                            </div>

                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>

    {{-- ================= PRODUCT META & TAX ================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Product Meta, Tax & Lifecycle</h5>
            <small class="text-muted">Tax details, shelf life, SEO & publishing controls</small>
        </div>

        <div class="card-body">

            {{-- TAX INFO --}}
            <div class="mb-4">
                <h6 class="text-primary mb-3">Tax Information</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">HSN Code</label>
                        <input name="hsn_code" class="form-control" placeholder="e.g. 190590"
                            value="{{ old('hsn_code', $product->hsn_code ?? '') }}">
                        <small class="text-muted">Used for GST & invoicing</small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">GST %</label>
                        <input name="gst_percent" type="number" step="0.01" min="0" max="100"
                            class="form-control" placeholder="0 - 100"
                            value="{{ old('gst_percent', $product->gst_percent ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- SHELF LIFE --}}
            <div class="mb-4" id="shelfLifeBox">
                <h6 class="text-primary mb-3">Shelf Life & Manufacturing</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Shelf Life (Days)</label>
                        <input name="shelf_life_days" type="number" min="0" class="form-control"
                            placeholder="e.g. 180"
                            value="{{ old('shelf_life_days', $product->shelf_life_days ?? '') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Manufactured At</label>
                        <input name="manufactured_at" type="date" class="form-control"
                            value="{{ old('manufactured_at', $product->manufactured_at ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- SEARCH & RANKING --}}
            <div class="mb-4">
                <h6 class="text-primary mb-3">Search & Ranking</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Search Rank</label>
                        <input name="search_rank" type="number" class="form-control"
                            placeholder="Lower = higher priority"
                            value="{{ old('search_rank', $product->search_rank ?? 0) }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Popularity Score</label>
                        <input name="popularity_score" type="number" class="form-control"
                            placeholder="Auto or manual"
                            value="{{ old('popularity_score', $product->popularity_score ?? 0) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Search Keywords</label>
                        <input name="search_keywords" class="form-control" placeholder="milk, dairy, fresh"
                            value="{{ old('search_keywords', isset($product) ? implode(',', $product->search_keywords ?? []) : '') }}">
                        <small class="text-muted">
                            Comma separated keywords for internal search
                        </small>
                    </div>
                </div>
            </div>

            {{-- PUBLISHING --}}
            <div>
                <h6 class="text-primary mb-3">Publishing</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Published At</label>
                        <input name="published_at" type="datetime-local" class="form-control"
                            value="{{ old('published_at', $product->published_at ?? '') }}">
                        <small class="text-muted">
                            Leave empty to publish immediately
                        </small>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= PRODUCT ATTRIBUTES ================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Product Attributes</h5>
                <small class="text-muted">
                    Attributes help customers filter and understand the product
                </small>
            </div>

            <button type="button" class="btn btn-sm btn-primary" id="addAttributeBtn">
                + Add Attribute
            </button>
        </div>

        <div class="card-body">

            {{-- Table Header --}}
            <div class="row fw-bold border-bottom pb-2 mb-3 text-muted">
                <div class="col-md-3">Attribute Name</div>
                <div class="col-md-4">Value</div>
                <div class="col-md-2 text-center">Filter</div>
                <div class="col-md-2 text-center">Visible</div>
                <div class="col-md-1 text-center">Action</div>
            </div>

            {{-- Attributes List --}}
            <div id="attributes-wrapper"></div>

            {{-- Empty State --}}
            <div id="no-attributes" class="text-center text-muted py-3">
                No attributes added yet.
            </div>

            {{-- Template --}}
            <template id="attribute-template">
                <div class="row align-items-center mb-2 attr-item" data-role="attribute-row">

                    <div class="col-md-3">
                        <input name="attributes[__i__][attribute_key]" class="form-control"
                            placeholder="e.g. Fat %, Pack Type" required>
                    </div>

                    <div class="col-md-4">
                        <input name="attributes[__i__][attribute_value]" class="form-control"
                            placeholder="e.g. 3%, Tetra Pack" required>
                    </div>

                    <div class="col-md-2 text-center">
                        <input type="checkbox" name="attributes[__i__][is_filterable]" value="1">
                    </div>

                    <div class="col-md-2 text-center">
                        <input type="checkbox" name="attributes[__i__][is_visible]" value="1" checked>
                    </div>

                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-attribute"
                            title="Remove attribute">
                            ✕
                        </button>

                    </div>
                </div>
            </template>

        </div>
    </div>

    {{-- ================= COMPLIANCE & LEGAL ================= --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Compliance & Legal Information</h5>
            <small class="text-muted">
                Mandatory for food, grocery & pharma products
            </small>
        </div>

        <div class="card-body row">

            <div class="col-md-4 mb-3">
                <label class="form-label">
                    FSSAI License
                    <span class="text-danger">*</span>
                </label>
                <input name="fssai_license" class="form-control" placeholder="e.g. 11223344556677"
                    value="{{ old('fssai_license', $product->compliance->fssai_license ?? '') }}">
                <small class="text-muted">Required for food items</small>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Manufacturer</label>
                <input name="manufacturer" class="form-control" placeholder="Company / Brand name"
                    value="{{ old('manufacturer', $product->compliance->manufacturer ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Country of Origin</label>
                <input name="country_of_origin" class="form-control" placeholder="India"
                    value="{{ old('country_of_origin', $product->compliance->country_of_origin ?? '') }}">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Ingredients</label>
                <textarea name="ingredients" rows="2" class="form-control" placeholder="List ingredients separated by commas">{{ old('ingredients', $product->compliance->ingredients ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Storage Instructions</label>
                <textarea name="storage_instructions" rows="2" class="form-control"
                    placeholder="e.g. Store in a cool and dry place">{{ old('storage_instructions', $product->compliance->storage_instructions ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Usage Instructions</label>
                <textarea name="usage_instructions" rows="2" class="form-control"
                    placeholder="How to consume or use this product">{{ old('usage_instructions', $product->compliance->usage_instructions ?? '') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label">Legal Disclaimer</label>
                <textarea name="disclaimer" rows="2" class="form-control" placeholder="Regulatory or legal disclaimer">{{ old('disclaimer', $product->compliance->disclaimer ?? '') }}</textarea>
            </div>

        </div>
    </div>

    {{-- ================= STATUS & VISIBILITY ================= --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Status & Visibility</h5>
            <small class="text-muted">
                Controls how the product appears on the app
            </small>
        </div>

        <div class="card-body">

            @php
                $statusFlags = [
                    'is_active' => 'Active (Visible to customers)',
                    'is_featured' => 'Featured Product',
                    'is_new' => 'Mark as New Arrival',
                    'is_bestseller' => 'Bestseller Tag',
                    'show_out_of_stock' => 'Show when out of stock',
                ];
            @endphp

            @foreach ($statusFlags as $key => $label)
                {{-- Hidden input ensures FALSE is sent --}}
                <input type="hidden" name="{{ $key }}" value="0">

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                        id="{{ $key }}" @checked(old($key, $product->$key ?? false))>

                    <label class="form-check-label" for="{{ $key }}">
                        {{ $label }}
                    </label>
                </div>
            @endforeach

        </div>
    </div>

    {{-- ================= ACTIONS ================= --}}
    <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-primary">
            {{ $isEdit ? 'Update Product' : 'Create Product' }}
        </button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

</form>

@push('scripts')
    <script>
        window.productFormConfig = {
            isEdit: @json($isEdit),
            variants: @json($product->variants ?? []),
            attributes: @json($product->attributes ?? [])
        };
    </script>
@endpush


@push('styles')
    <style>
        .flag-card {
            cursor: pointer;
        }

        .flag-box {
            transition: all 0.2s ease;
        }

        .flag-input:checked+.flag-box,
        .flag-card:hover .flag-box {
            background-color: #f0f9ff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 1px rgba(13, 110, 253, .25);
        }
    </style>
@endpush
