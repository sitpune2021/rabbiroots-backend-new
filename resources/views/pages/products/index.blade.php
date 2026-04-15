
<h5>Debug:</h5>
<pre>{{ session('debug') }}</pre>

@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product List</h5>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add Product
            </a>
        </div>

        {{-- FILTER BAR --}}
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2">

                <div class="col-md-4">
                    <input type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by name or slug..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status')=='active' )>Active</option>
                        <option value="inactive" @selected(request('status')=='inactive' )>Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">
                        <i class="bx bx-search"></i> Filter
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>


        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif


        {{-- TABLE --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr.No</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Default Price</th>
                        <th>Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $index => $product)
                    <tr>

                        {{-- SERIAL NUMBER --}}
                        <td>{{ $products->firstItem() + $index }}</td>

                        {{-- PRODUCT INFO --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">

                                {{-- IMAGE --}}
                                @php
                                $image = $product->images->first()?->image ?? null;
                                @endphp

                                @if($image)
                                <img src="{{ asset('storage/' . $image) }}"
                                    class="rounded"
                                    style="height:45px;width:45px;object-fit:cover;">
                                @else
                                <div class="bg-light rounded"
                                    style="height:45px;width:45px;"></div>
                                @endif

                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $product->slug }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        {{-- CATEGORY --}}
                        <td>
                            {{ $product->category?->name ?? '—' }}
                        </td>

                        {{-- BRAND --}}
                        <td>
                            {{ $product->brand?->name ?? '—' }}
                        </td>

                        {{-- DEFAULT VARIANT PRICE --}}
                        <td>
                            @php
                            $defaultVariant = $product->variants->where('is_default', true)->first();
                            @endphp

                            @if($defaultVariant)
                            ₹{{ number_format($defaultVariant->selling_price, 2) }}
                            <br>
                            <small class="text-muted">
                                MRP: ₹{{ number_format($defaultVariant->mrp, 2) }}
                            </small>
                            @else
                            —
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td>
                            @if ($product->is_active)
                            <span class="badge bg-label-success">Active</span>
                            @else
                            <span class="badge bg-label-danger">Inactive</span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td>
                            <div class="dropdown">
                                <button class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>

                                <div class="dropdown-menu">

                                    <a class="dropdown-item"
                                        href="{{ route('products.show', $product->id) }}">
                                        <i class="bx bx-show me-1"></i> View
                                    </a>

                                    <a class="dropdown-item"
                                        href="{{ route('products.edit', $product->id) }}">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('products.destroy', $product->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="dropdown-item text-danger"
                                            type="submit">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No products found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($products->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush