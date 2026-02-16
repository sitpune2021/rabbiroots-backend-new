@extends('layouts.app')
{{-- ================= ENTERPRISE KPI PANEL ================= --}}
@php
    $totalVariants = $product->variants->count();

    $totalStock = $product->variants->sum(fn($v) => $v->storeInventories->sum('available_qty'));

    $totalSellable = $product->variants->sum(
        fn($v) => $v->storeInventories->sum('available_qty') - $v->storeInventories->sum('reserved_qty'),
    );

    $totalInventoryValue = $product->variants->sum(
        fn($v) => $v->storeInventories->sum('available_qty') * $v->cost_price,
    );

    $expiredStock = 0;
    $nearExpiryStock = 0;

    foreach ($product->variants as $variant) {
        foreach ($variant->inventoryBatches as $batch) {
            $daysLeft = now()->diffInDays($batch->expiry_date, false);

            if ($daysLeft < 0) {
                $expiredStock += $batch->quantity_available;
            }

            if ($daysLeft >= 0 && $daysLeft <= 7) {
                $nearExpiryStock += $batch->quantity_available;
            }
        }
    }
@endphp


@section('content')
    <div class="container-xxl container-p-y">
        <div class="row mb-4">

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6>Total Sellable</h6>
                        <h3 class="fw-bold text-success">{{ $totalSellable }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6>Expired Stock</h6>
                        <h3 class="fw-bold text-danger">{{ $expiredStock }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6>Near Expiry</h6>
                        <h3 class="fw-bold text-warning">{{ $nearExpiryStock }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6>Inventory Value</h6>
                        <h3 class="fw-bold text-primary">
                            ₹{{ number_format($totalInventoryValue, 2) }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>


        {{-- ================= KPI SUMMARY ================= --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <h6>Total Variants</h6>
                        <h3 class="fw-bold">{{ $product->variants->count() }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <h6>Total Stock</h6>
                        <h3 class="fw-bold text-success">
                            {{ $product->variants->sum(fn($v) => $v->storeInventories->sum('available_qty')) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <h6>GST</h6>
                        <h3 class="fw-bold">{{ $product->gst_percent }}%</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <h6>Status</h6>
                        <h3 class="fw-bold {{ $product->is_active ? 'text-success' : 'text-danger' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">

            {{-- ================= LEFT SECTION ================= --}}
            <div class="col-lg-8">

                {{-- PRODUCT HEADER --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>
                                <h2 class="fw-bold mb-1">{{ $product->name }}</h2>
                                <small class="text-muted">{{ $product->slug }}</small>

                                <div class="mt-2">

                                    <span class="badge bg-label-primary">
                                        {{ ucfirst($product->product_type) }}
                                    </span>

                                    @foreach ([
            'is_featured' => 'Featured',
            'is_new' => 'New',
            'is_bestseller' => 'Bestseller',
        ] as $key => $label)
                                        @if ($product->$key)
                                            <span class="badge bg-primary">{{ $label }}</span>
                                        @endif
                                    @endforeach

                                </div>
                            </div>

                            <div>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                                    Edit Product
                                </a>
                            </div>

                        </div>

                    </div>
                </div>


                {{-- DESCRIPTION --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Description</div>
                    <div class="card-body">
                        <p><strong>Short:</strong> {{ $product->short_description }}</p>
                        <hr>
                        <p class="text-muted">{!! nl2br(e($product->description)) !!}</p>
                    </div>
                </div>


                {{-- PRODUCT IMAGES --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Product Gallery</div>
                    <div class="card-body d-flex flex-wrap gap-3">
                        @forelse($product->images as $img)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $img->image) }}" class="rounded shadow-sm border"
                                    style="height:120px;width:120px;object-fit:cover;">
                                @if ($img->is_primary)
                                    <span class="badge bg-success position-absolute top-0 start-0 m-1">
                                        Primary
                                    </span>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted">No images uploaded</p>
                        @endforelse
                    </div>
                </div>


                {{-- VARIANTS TABLE --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Variants Overview</div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Pack</th>
                                    <th>MRP</th>
                                    <th>Selling</th>
                                    <th>Margin</th>
                                    <th>Cost</th>
                                    <th>Tax %</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Total Stock</th>
                                    <th>Status</th>
                                    <th>priceOverrides</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($product->variants as $variant)
                                    @php
                                        $stock = $variant->storeInventories->sum('available_qty');
                                        $margin = $variant->selling_price - $variant->cost_price;
                                    @endphp

                                    <tr>
                                        <td>
                                            <strong>{{ $variant->sku }}</strong><br>
                                            <small class="text-muted">{{ $variant->barcode }}</small>
                                        </td>

                                        <td>{{ $variant->pack_size }} {{ $variant->unit }}</td>

                                        <td>₹{{ number_format($variant->mrp, 2) }}</td>

                                        <td class="fw-bold text-success">
                                            ₹{{ number_format($variant->selling_price, 2) }}
                                        </td>

                                        <td class="fw-bold {{ $margin > 0 ? 'text-success' : 'text-danger' }}">
                                            ₹{{ number_format($margin, 2) }}
                                        </td>

                                        <td>₹{{ number_format($variant->cost_price, 2) }}</td>
                                        <td>{{ $variant->tax_percent }}%</td>
                                        <td>{{ $variant->min_order_qty }}</td>
                                        <td>{{ $variant->max_order_qty }}</td>

                                        <td>
                                            <span class="badge bg-label-info">
                                                {{ $stock }} Units
                                            </span>
                                        </td>

                                        <td>
                                            @if ($variant->is_default)
                                                <span class="badge bg-primary">Default</span>
                                            @endif

                                            <span class="badge {{ $variant->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>

                                        @php
                                            $override = $variant->priceOverrides
                                                ->where('start_at', '<=', now())
                                                ->where('end_at', '>=', now())
                                                ->first();

                                            $effectivePrice = $override
                                                ? $override->override_price
                                                : $variant->selling_price;
                                        @endphp

                                        <td class="fw-bold text-success">
                                            ₹{{ number_format($effectivePrice, 2) }}

                                            @if ($override)
                                                <span class="badge bg-info ms-1">
                                                    Override
                                                </span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PRICE OVERRIDES --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Store Price Overrides</div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Store</th>
                                    <th>Variant</th>
                                    <th>Override Price</th>
                                    <th>Valid From</th>
                                    <th>Valid To</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($product->variants as $variant)
                                    @foreach ($variant->priceOverrides as $override)
                                        <tr>
                                            <td>{{ $override->store->name ?? '—' }}</td>
                                            <td>{{ $variant->sku }}</td>
                                            <td class="fw-bold text-primary">
                                                ₹{{ $override->override_price }}
                                            </td>
                                            <td>{{ $override->start_at }}</td>
                                            <td>{{ $override->end_at }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= STORE INVENTORY BREAKDOWN ================= --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header fw-semibold bg-light">
                        Store Inventory Breakdown
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Store</th>
                                    <th>Variant SKU</th>
                                    <th>Available</th>
                                    <th>Reserved</th>
                                    <th>Committed</th>
                                    <th>Sellable</th>
                                    <th>Total</th>
                                    <th>Stock Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($product->variants as $variant)
                                    @forelse($variant->storeInventories as $inventory)
                                        @php
                                            $total =
                                                $inventory->available_qty +
                                                $inventory->reserved_qty +
                                                $inventory->committed_qty;

                                            $sellable = $inventory->available_qty - $inventory->reserved_qty;

                                            $isLowStock = $sellable <= 5 && $sellable > 0;
                                        @endphp

                                        <tr>
                                            <td>
                                                <strong>{{ $inventory->store->name ?? 'N/A' }}</strong>
                                            </td>

                                            <td>
                                                {{ $variant->sku }}
                                            </td>

                                            <td class="text-success fw-bold">
                                                {{ $inventory->available_qty }}
                                            </td>

                                            <td class="text-warning fw-bold">
                                                {{ $inventory->reserved_qty }}
                                            </td>

                                            <td class="text-danger fw-bold">
                                                {{ $inventory->committed_qty }}
                                            </td>

                                            <td class="fw-bold {{ $sellable > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $sellable }}
                                            </td>

                                            <td>
                                                <span class="badge bg-label-info">
                                                    {{ $total }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($sellable <= 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @elseif($isLowStock)
                                                    <span class="badge bg-warning text-dark">
                                                        Low Stock
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        Healthy
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                No inventory found for {{ $variant->sku }}
                                            </td>
                                        </tr>
                                    @endforelse

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            No variants available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- ================= INVENTORY BATCHES ================= --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header fw-semibold bg-light">
                        Inventory Batches (Expiry Tracking)
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Store</th>
                                    <th>Variant</th>
                                    <th>Expiry Date</th>
                                    <th>Days Left</th>
                                    <th>Qty Received</th>
                                    <th>Qty Available</th>
                                    <th>Received At</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($product->variants as $variant)
                                    @forelse($variant->inventoryBatches as $batch)
                                        @php
                                            $expiry = \Carbon\Carbon::parse($batch->expiry_date);
                                            $daysLeft = now()->diffInDays($expiry, false);
                                            $isExpired = $daysLeft < 0;
                                            $isNearExpiry = $daysLeft <= 7 && $daysLeft >= 0;
                                        @endphp

                                        <tr>
                                            <td>
                                                {{ $batch->store->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $variant->sku }}
                                            </td>

                                            <td>
                                                @if ($isExpired)
                                                    <span class="text-danger fw-bold">
                                                        {{ $expiry->format('d M Y') }}
                                                    </span>
                                                @elseif($isNearExpiry)
                                                    <span class="text-warning fw-bold">
                                                        {{ $expiry->format('d M Y') }}
                                                    </span>
                                                @else
                                                    {{ $expiry->format('d M Y') }}
                                                @endif
                                            </td>

                                            <td>
                                                @if ($isExpired)
                                                    <span class="text-danger fw-bold">
                                                        Expired {{ abs($daysLeft) }} days ago
                                                    </span>
                                                @elseif($isNearExpiry)
                                                    <span class="text-warning fw-bold">
                                                        {{ $daysLeft }} days
                                                    </span>
                                                @else
                                                    <span class="text-success">
                                                        {{ $daysLeft }} days
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $batch->quantity_received }}
                                            </td>

                                            <td class="fw-bold">
                                                {{ $batch->quantity_available }}
                                            </td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($batch->received_at)->format('d M Y H:i') }}
                                            </td>

                                            <td>
                                                @if ($isExpired)
                                                    <span class="badge bg-danger">Expired</span>
                                                @elseif($isNearExpiry)
                                                    <span class="badge bg-warning text-dark">Near Expiry</span>
                                                @else
                                                    <span class="badge bg-success">Valid</span>
                                                @endif
                                            </td>

                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                No batches available for {{ $variant->sku }}
                                            </td>
                                        </tr>
                                    @endforelse

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            No variant batches found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= FIFO RISK ALERT ================= --}}
                @php
                    $oldestBatch = null;

                    foreach ($product->variants as $variant) {
                        foreach ($variant->inventoryBatches as $batch) {
                            if (!$oldestBatch || $batch->expiry_date < $oldestBatch->expiry_date) {
                                $oldestBatch = $batch;
                            }
                        }
                    }
                @endphp

                @if ($oldestBatch)
                    <div class="card mb-4 border-warning shadow-sm">
                        <div class="card-header bg-warning text-dark fw-bold">
                            FIFO Risk Alert
                        </div>
                        <div class="card-body">

                            <p>
                                Oldest batch expires on:
                                <strong>{{ $oldestBatch->expiry_date }}</strong>
                            </p>

                            <p>
                                Qty Remaining:
                                <strong>{{ $oldestBatch->quantity_available }}</strong>
                            </p>

                            <p>
                                Store:
                                <strong>{{ $oldestBatch->store->name ?? 'N/A' }}</strong>
                            </p>

                        </div>
                    </div>
                @endif

                @php
                    $storesAtRisk = [];

                    foreach ($product->variants as $variant) {
                        foreach ($variant->storeInventories as $inv) {
                            $sellable = $inv->available_qty - $inv->reserved_qty;
                            if ($sellable <= 5) {
                                $storesAtRisk[] = $inv->store->name;
                            }
                        }
                    }
                @endphp

                @if (count($storesAtRisk))
                    <div class="card mb-4 border-danger shadow-sm">
                        <div class="card-header bg-danger text-white fw-bold">
                            Stores At Stock Risk
                        </div>
                        <div class="card-body">
                            @foreach (array_unique($storesAtRisk) as $store)
                                <span class="badge bg-danger me-2">{{ $store }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif


            </div>

            {{-- ================= RIGHT SECTION ================= --}}
            <div class="col-lg-4">

                {{-- PRODUCT INFO --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Product Info</div>
                    <div class="card-body">

                        <p><strong>Category:</strong> {{ $product->category?->name }}</p>
                        <p><strong>Brand:</strong> {{ $product->brand?->name }}</p>
                        <p><strong>HSN:</strong> {{ $product->hsn_code }}</p>
                        <p><strong>GST:</strong> {{ $product->gst_percent }}%</p>
                        <p><strong>Shelf Life:</strong> {{ $product->shelf_life_days }} days</p>
                        <p><strong>Manufactured:</strong> {{ $product->manufactured_at }}</p>
                        <p><strong>Published:</strong> {{ $product->published_at }}</p>

                    </div>
                </div>


                {{-- FLAGS --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Flags</div>
                    <div class="card-body">

                        @foreach ([
            'is_perishable' => 'Perishable',
            'requires_cold_storage' => 'Cold Storage',
            'is_fragile' => 'Fragile',
            'is_veg' => 'Vegetarian',
            'contains_allergens' => 'Allergens',
            'show_out_of_stock' => 'Show OOS',
        ] as $key => $label)
                            <div class="mb-2">
                                <span class="badge {{ $product->$key ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $label }}
                                </span>
                            </div>
                        @endforeach

                    </div>
                </div>


                {{-- ATTRIBUTES --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Attributes</div>
                    <div class="card-body">
                        @foreach ($product->attributes as $attr)
                            <div class="mb-2">
                                <strong>{{ $attr->attribute_key }}:</strong>
                                {{ $attr->attribute_value }}
                            </div>
                        @endforeach
                    </div>
                </div>


                {{-- COMPLIANCE --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Compliance</div>
                    <div class="card-body">

                        @if ($product->compliance)
                            <p><strong>FSSAI:</strong> {{ $product->compliance->fssai_license }}</p>
                            <p><strong>Manufacturer:</strong> {{ $product->compliance->manufacturer }}</p>
                            <p><strong>Country:</strong> {{ $product->compliance->country_of_origin }}</p>
                            <hr>
                            <strong>Storage:</strong>
                            <p>{{ $product->compliance->storage_instructions }}</p>
                            <strong>Ingredients:</strong>
                            <p>{{ $product->compliance->ingredients }}</p>
                            <strong>Usage:</strong>
                            <p>{{ $product->compliance->usage_instructions }}</p>
                            <strong>Disclaimer:</strong>
                            <p>{{ $product->compliance->disclaimer }}</p>
                            <strong>Allergen Info:</strong>
                            <p>{{ $product->compliance->allergen_info }}</p>
                        @else
                            <p class="text-muted">No compliance data</p>
                        @endif

                    </div>
                </div>


                {{-- SEO META --}}
                <div class="card">
                    <div class="card-header fw-semibold">SEO & Meta</div>
                    <div class="card-body">
                        <p><strong>Search Rank:</strong> {{ $product->search_rank }}</p>
                        <p><strong>Popularity Score:</strong> {{ $product->popularity_score }}</p>
                        <p><strong>Keywords:</strong>
                            {{ implode(', ', $product->search_keywords ?? []) }}
                        </p>
                        <hr>
                        <small class="text-muted">
                            Created: {{ $product->created_at }}<br>
                            Updated: {{ $product->updated_at }}
                        </small>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
