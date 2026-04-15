@extends('layouts.app')

@push('scripts')
<script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
<script>
    const filterDropdown = document.getElementById('feature-filter');

    function applyCurrentFilter() {
        const value = filterDropdown.value;
        const tbody = document.querySelector('table tbody');
        const tableRows = Array.from(tbody.querySelectorAll('tr'));
        const featureColumns = document.querySelectorAll('.feature-column');

        if (value === 'both') {
            // Show feature column
            featureColumns.forEach(col => col.style.display = '');

            const mainRows = tableRows.filter(r => r.classList.contains('main-category'));

            const checked = [];
            const unchecked = [];

            mainRows.forEach(row => {
                const checkbox = row.querySelector('.feature-checkbox');
                if (checkbox && checkbox.checked) {
                    checked.push(row);
                } else {
                    unchecked.push(row);
                }
            });

            // Hide all rows first
            tableRows.forEach(r => r.style.display = 'none');

            // 🔥 IMPORTANT: reorder rows in DOM
            [...checked, ...unchecked].forEach(row => {
                row.style.display = '';
                tbody.appendChild(row); // 👈 ye upar-neeche move karega
            });

        } else {
            // All Categories
            featureColumns.forEach(col => col.style.display = 'none');
            tableRows.forEach(row => row.style.display = '');
        }
    }

    // Filter dropdown
    filterDropdown.addEventListener('change', applyCurrentFilter);

    // Checkbox change
    document.querySelectorAll('.feature-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {

            // 🔒 MAX 20 LIMIT
            const checkedCount = document.querySelectorAll('.feature-checkbox:checked').length;

            if (this.checked && checkedCount > 20) {
                alert('You can select maximum 20 featured categories only.');
                this.checked = false; // rollback
                return;
            }

            const categoryId = this.dataset.id;
            const isFeatured = this.checked ? 1 : 0;

            fetch("{{ route('category.update-feature') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: categoryId,
                        featured: isFeatured
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Something went wrong!');
                    }
                    applyCurrentFilter(); // re-sort
                })
                .catch(() => alert('Something went wrong!'));
        });
    });
</script>
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

        <div class="mb-3 d-flex justify-content-end">
            <select id="feature-filter" class="form-select w-auto">
                <option value="" selected>All Categories</option>
                <option value="both">Only Main Categories</option>
            </select>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="feature-column" style="display:none">#</th>
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
                    <tr class="{{ is_null($category->parent_id) ? 'main-category' : 'sub-category' }}">

                        <td class="feature-column" style="display:none">
                            @if ($category->level == 'main')
                            <input type="checkbox" class="feature-checkbox" data-id="{{ $category->id }}"
                                {{ $category->featured ? 'checked' : '' }}>
                            @endif
                        </td>

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
                            @if ($category->level === 'child')
                            <strong>{{ $category->parent?->name }}</strong>
                            <br>
                            <small>{{ $category->parent?->parent?->name }}</small>
                            @elseif($category->level === 'sub')
                            <strong>{{ $category->parent?->name }}</strong>
                            @else
                            —
                            @endif
                        </td>


                        <td>
                            @if ($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" class="rounded"
                                style="height:40px;">
                            @else
                            —
                            @endif
                        </td>

                        <td>
                            @if ($category->is_active)
                            <span class="badge bg-label-success">Active</span>
                            @else
                            <span class="badge bg-label-danger">Inactive</span>
                            @endif
                        </td>

                        <td>
                            <div class="dropdown">
                                <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>

                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('category.edit', $category->id) }}">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('category.destroy', $category->id) }}" method="POST"
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
        @if ($categories->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

@endsection
@push('scripts')
<script>
    setTimeout(function () {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush
