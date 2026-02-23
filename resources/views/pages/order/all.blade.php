@extends('layouts.app')
@push('scripts')
<script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Order List</h5>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Delivery Agent</th>
                        <th>Status</th>
                        <th>Date</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>

                    @forelse($orders as $key => $order)
                    <tr>
                        <td>{{ $orders->firstItem() + $key }}</td>

                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer->name ?? '-' }}</td>

                        <td> {{ $order->agent->name ?? 'Not Assigned' }}</td>

                        <td>{{ ucfirst($order->status) }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No assigned orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection