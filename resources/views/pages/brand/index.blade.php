@extends('layouts.app')
@push('scripts')
<script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Brand List</h5>
            <a href="{{ route('brand.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add
            </a>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $index => $brand)
                    <tr>
                        {{-- Sr No --}}
                        <td>{{ $brands->firstItem() + $index }}</td>

                        {{-- Logo --}}
                        <td>
                            @if($brand->image)
                            <img src="{{ asset('storage/'.$brand->image) }}"
                                width="50"
                                class="rounded border">
                            @else
                            <span class="text-muted">No Image</span>
                            @endif
                        </td>

                        {{-- Name --}}
                        <td>{{ $brand->name }}</td>

                        {{-- Slug --}}
                        <td>{{ $brand->slug }}</td>

                        {{-- Status --}}
                        <td>
                            <span class="badge {{ $brand->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                {{ $brand->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        {{-- Action --}}
                        <td>
                            <div class="dropdown">
                                <button type="button"
                                    class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                </button>

                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                        href="{{ route('brand.show', $brand->id) }}">
                                        <i class="icon-base bx bx-show-alt me-1"></i> View
                                    </a>

                                    <a class="dropdown-item"
                                        href="{{ route('brand.edit', $brand->id) }}">
                                        <i class="icon-base bx bx-edit-alt me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('brand.destroy', $brand->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger">
                                            <i class="icon-base bx bx-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No brands found
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
            
        </div>
    </div>

</div>
@endsection