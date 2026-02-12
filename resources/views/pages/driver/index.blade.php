@extends('layouts.app')
@push('scripts')
<script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Driver List</h5>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Driver Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Vehicle Name</th>
                        <th>License Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($agents as $key => $agent)
                    <tr>
                        <td>{{ $agents->firstItem() + $key }}</td>

                        <td>{{ $agent->user->name ?? '-' }}</td>
                        <td>{{ $agent->user->email ?? '-' }}</td>
                        <td>{{ $agent->user->phone ?? '-' }}</td>

                        <td>{{ ucfirst($agent->vehicle_name) ?? '-' }}</td>
                        <td>{{ $agent->license_plate ?? '-' }}</td>

                        <td>
                            @if($agent->status === 'active')
                            <span class="badge bg-label-success me-1">Active</span>

                            @elseif($agent->status === 'inactive')
                            <span class="badge bg-label-secondary me-1">Inactive</span>

                            @elseif($agent->status === 'on-leave')
                            <span class="badge bg-label-warning me-1">On Leave</span>

                            @elseif($agent->status === 'suspended')
                            <span class="badge bg-label-danger me-1">Suspended</span>

                            @else
                            <span class="badge bg-label-info me-1">Pending</span>
                            @endif
                        </td>

                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('driver.show', $agent->id) }}">
                                        <i class="icon-base bx bx-show-alt me-1"></i> View
                                    </a>
                                    @if($agent->status === 'pending')
                                    <form action="{{ route('driver.approve', $agent->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="bx bx-check me-1"></i> Approve
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No Delivery Agents Found</td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection