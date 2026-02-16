@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Store List</h5>
            <a href="{{ route('stores.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add Store
            </a>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Store</th>
                        <th>Code</th>
                        <th>Open</th>
                        <th>Accepting Orders</th>
                        <th>Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($stores as $index => $store)
                        <tr>
                            {{-- SERIAL NUMBER (pagination-safe) --}}
                            <td>
                                {{ $stores->firstItem() + $index }}
                            </td>

                            {{-- STORE NAME --}}
                            <td>
                                <strong>{{ $store->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $store->address }}</small>
                            </td>

                            {{-- STORE CODE --}}
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $store->code }}
                                </span>
                            </td>

                            {{-- OPEN / CLOSED --}}
                            <td>
                                @if($store->is_open)
                                    <span class="badge bg-label-success">Open</span>
                                @else
                                    <span class="badge bg-label-secondary">Closed</span>
                                @endif
                            </td>

                            {{-- ACCEPTING ORDERS --}}
                            <td>
                                @if($store->accepting_orders)
                                    <span class="badge bg-label-success">Yes</span>
                                @else
                                    <span class="badge bg-label-warning">No</span>
                                @endif
                            </td>

                            {{-- ACTIVE / INACTIVE --}}
                            <td>
                                @if($store->is_active)
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
                                           href="{{ route('stores.edit', $store->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <a class="dropdown-item"
                                           href="{{ route('stores.show', $store->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> View
                                        </a>

                                        <form action="{{ route('stores.destroy', $store->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this store?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No stores found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($stores->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $stores->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
