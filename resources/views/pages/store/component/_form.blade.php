@php
    $isEdit = isset($store);
@endphp

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== STORE CODE PREVIEW =====
            const nameInput = document.querySelector('input[name="name"]');
            const codePreview = document.getElementById('store_code_preview');

            @if (!$isEdit)
                const serial = "{{ str_pad($nextSerial ?? 0, 3, '0', STR_PAD_LEFT) }}";

                function generatePreview(name) {
                    if (!name) {
                        codePreview.value = '---';
                        return;
                    }

                    let prefix = name.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();
                    prefix = prefix.padEnd(3, 'X');

                    codePreview.value = `${prefix}-${serial}`;
                }

                generatePreview(nameInput.value);
                nameInput.addEventListener('input', e => generatePreview(e.target.value));
            @endif


            // ===== MANAGER TOGGLE =====
            const toggle = document.getElementById('assign_manager_toggle');
            const managerSection = document.getElementById('manager_section');

            function syncManagerSection() {
                if (toggle.checked) {
                    managerSection.classList.remove('d-none');
                } else {
                    managerSection.classList.add('d-none');
                    managerSection.querySelectorAll('input').forEach(i => i.value = '');
                }
            }

            syncManagerSection();
            toggle.addEventListener('change', syncManagerSection);
        });
    </script>
@endpush

<form action="{{ $isEdit ? route('stores.update', $store->id) : route('stores.store') }}" method="POST">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- BASIC INFORMATION --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $isEdit ? 'Edit' : 'Add' }} Store</h5>
        </div>

        <div class="card-body">
            <div class="row">

                {{-- STORE NAME --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Store Name *</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $store->name ?? '') }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- STORE CODE (PREVIEW) --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Store Code (Auto-generated)</label>
                    <input type="text" id="store_code_preview" class="form-control" value="---" disabled>
                    <small class="text-muted">
                        Code will be finalized after saving
                    </small>
                </div>

                {{-- CONTACT PHONE --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" name="contact_phone" class="form-control"
                        value="{{ old('contact_phone', $store->contact_phone ?? '') }}">
                </div>

                {{-- CONTACT EMAIL --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Email</label>
                    <input type="email" name="contact_email" class="form-control"
                        value="{{ old('contact_email', $store->contact_email ?? '') }}">
                </div>

                {{-- ADDRESS --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea name="address" rows="2" class="form-control" required>{{ old('address', $store->address ?? '') }}</textarea>
                </div>

            </div>
        </div>
    </div>

    {{-- LOCATION & DELIVERY --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Location & Delivery</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Latitude *</label>
                    <input type="text" name="latitude" class="form-control"
                        value="{{ old('latitude', $store->latitude ?? '') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Longitude *</label>
                    <input type="text" name="longitude" class="form-control"
                        value="{{ old('longitude', $store->longitude ?? '') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Delivery Radius (KM) *</label>
                    <input type="number" step="0.01" name="delivery_radius_km" class="form-control"
                        value="{{ old('delivery_radius_km', $store->delivery_radius_km ?? 5) }}" required>
                </div>

            </div>
        </div>
    </div>

    {{-- OPERATIONS --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Operations</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Opening Time</label>
                    <input type="time" name="opening_time" class="form-control"
                        value="{{ old('opening_time', $store->opening_time_for_input ?? '') }}">
                </div>


                <div class="col-md-4 mb-3">
                    <label class="form-label">Closing Time</label>
                    <input type="time" name="closing_time" class="form-control"
                        value="{{ old('closing_time', $store->closing_time_for_input ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Max Orders Per Slot</label>
                    <span>Maximum number of new orders a store is allowed to accept in a fixed time window</span>
                    <input type="number" name="max_orders_per_slot" class="form-control"
                        value="{{ old('max_orders_per_slot', $store->max_orders_per_slot ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Order Cutoff Minutes</label>
                    <span>The maximum time (in minutes) after order placement during which a customer is allowed to
                        cancel or modify the order without penalty.</span>
                    <input type="number" name="order_cutoff_minutes" class="form-control"
                        value="{{ old('order_cutoff_minutes', $store->order_cutoff_minutes ?? 5) }}">
                </div>

            </div>
        </div>
    </div>

    {{-- FINANCE --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Finance (COD)</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Daily Cash Limit</label>
                    <input type="number" step="0.01" name="daily_cash_limit" class="form-control"
                        value="{{ old('daily_cash_limit', $store->daily_cash_limit ?? '') }}">
                </div>

            </div>
        </div>
    </div>

    {{-- STATUS --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Status</h5>
        </div>

        <div class="card-body">
            <div class="form-check form-switch mb-2">
                <input type="checkbox" class="form-check-input" name="is_active" value="1"
                    @checked(old('is_active', $store->is_active ?? true))>
                <label class="form-check-label">Active</label>
            </div>

            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="accepting_orders" value="1"
                    @checked(old('accepting_orders', $store->accepting_orders ?? true))>
                <label class="form-check-label">Accepting Orders</label>
            </div>
        </div>
    </div>

    {{-- STORE MANAGER TOGGLE --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Store Manager</h5>
        </div>

        <div class="card-body">
            <div class="form-check form-switch mb-3">
                {{-- <input type="checkbox" class="form-check-input" id="assign_manager_toggle" name="assign_manager"
                    value="1" checked> --}}
                <input type="checkbox" class="form-check-input" id="assign_manager_toggle" name="assign_manager"
                    value="1" @checked(old('assign_manager', isset($store->manager_id)))>

                <label class="form-check-label" for="assign_manager_toggle">
                    Assign Store Manager now
                </label>
            </div>

            {{-- MANAGER DETAILS (HIDDEN BY DEFAULT) --}}
            <div id="manager_section">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Manager Name *</label>
                        <input type="text" name="manager_name" class="form-control"
                            value="{{ old('manager_name', $store->manager->name ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Manager Email *</label>
                        <input type="email" name="manager_email" class="form-control"
                            value="{{ old('manager_email', $store->manager->email ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Manager Phone</label>
                        <input type="text" name="manager_phone" class="form-control"
                            value="{{ old('manager_phone', $store->manager->phone ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Temporary Password *</label>
                        <input type="text" name="manager_password" class="form-control"
                            placeholder="Will be asked to change on first login">
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- ACTIONS --}}
    <div class="d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Update' : 'Save' }}
        </button>
        <a href="{{ route('stores.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
