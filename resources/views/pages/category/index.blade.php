@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Category List</h5>
            <a href="{{ route('category.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add
            </a>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            {{-- SERIAL NUMBER (pagination-safe) --}}
                            <td>
                                {{ $categories->firstItem() + $index }}
                            </td>

                            <td>
                                <strong>{{ $category->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $category->slug }}</small>
                            </td>

                            <td>
                                {{ $category->parent?->name ?? '—' }}
                            </td>

                            <td>
                                @if($category->image)
                                    <img src="{{ asset('storage/'.$category->image) }}"
                                         class="rounded"
                                         style="height:40px;">
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>

                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="{{ route('category.edit', $category->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <form action="{{ route('category.destroy', $category->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="6" class="text-center text-muted">
                                No categories found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($categories->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
