@extends('layouts.app')
@push('scripts')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Role List</h5>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Role Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <span class="">Super Admin</span>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <span class="">Store Manager</span>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>
                                <span class="">Picker</span>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>
                                <span class="">Delivery Manager</span>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>
                                <span class="">Finance Manager</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

