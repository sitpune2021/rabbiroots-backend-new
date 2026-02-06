@extends('layouts.app')
@push('scripts')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Form controls -->
            <div class="col-md-12">
                @include('pages.category.component._form')
            </div>

        </div>
    </div>
@endsection
