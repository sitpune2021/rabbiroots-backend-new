@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Store List</h5>
            <a href="{{ route('stores.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add Store
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('stores.show', 1) }}"><i
                                                class="icon-base bx bx-show-alt me-1"></i> View</a>
                                        <a class="dropdown-item" href="{{ route('stores.edit', 1) }}"><i
                                                class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"><i
                                                class="icon-base bx bx-trash me-1"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
