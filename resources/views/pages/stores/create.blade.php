@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('store.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="name">Store Name <span class="text-danger">*</span></label>
                            <input 
                                class="form-control @error('name') is-invalid @enderror" 
                                type="text" 
                                id="name" 
                                name="name" 
                                placeholder="Enter store name"
                                value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="location">Location</label>
                                <input 
                                    class="form-control @error('location') is-invalid @enderror" 
                                    type="text" 
                                    id="location" 
                                    name="location" 
                                    placeholder="e.g., Downtown"
                                    value="{{ old('location') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    placeholder="e.g., +1234567890"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="address">Address</label>
                            <textarea 
                                class="form-control @error('address') is-invalid @enderror" 
                                id="address" 
                                name="address" 
                                rows="3" 
                                placeholder="Enter complete address">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="latitude">Latitude</label>
                                <input 
                                    class="form-control @error('latitude') is-invalid @enderror" 
                                    type="number" 
                                    id="latitude" 
                                    name="latitude" 
                                    placeholder="e.g., 40.7128"
                                    step="0.0001"
                                    value="{{ old('latitude') }}">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="longitude">Longitude</label>
                                <input 
                                    class="form-control @error('longitude') is-invalid @enderror" 
                                    type="number" 
                                    id="longitude" 
                                    name="longitude" 
                                    placeholder="e.g., -74.0060"
                                    step="0.0001"
                                    value="{{ old('longitude') }}">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2" style="margin-top:20px;">
                            <button type="submit" class="btn btn-primary w-auto">
                                <i class="bx bx-save me-1"></i> Add
                            </button>

                            <a href="{{ route('store.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
