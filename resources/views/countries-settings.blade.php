@extends('layouts.public')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-light"><i class="fa-solid fa-gear text-info me-2"></i> Active Countries Settings</h4>
    <p class="text-muted mb-0">Select which countries appear on the map and intelligence feed</p>
</div>

<div class="d-flex flex-wrap justify-content-between mb-3 gap-3">
    <div class="input-group" style="max-width: 300px;">
        <span class="input-group-text bg-dark border-secondary text-light"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="searchInput" class="form-control bg-dark border-secondary text-light" placeholder="Search countries..." onkeyup="filterTable()">
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success fw-bold" id="btnActivateAll" onclick="bulkToggle('activate')"><i class="fa-solid fa-check me-2"></i> Activate All</button>
        <button class="btn btn-outline-danger fw-bold" id="btnDeactivateAll" onclick="bulkToggle('deactivate')"><i class="fa-solid fa-xmark me-2"></i> Deactivate All</button>
    </div>
</div>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
            <table class="table table-dark table-hover mb-0 align-middle" id="countriesTable">
                <thead class="table-active" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th class="ps-4">Country Name</th>
                        <th>Region</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($countries as $country)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $country->name }}</td>
                        <td class="text-muted">{{ $country->region }}</td>
                        <td>
                            <span class="badge {{ $country->is_active ? 'bg-success' : 'bg-secondary' }}" id="badge-{{ $country->id }}">
                                {{ $country->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm {{ $country->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                    onclick="toggleStatus({{ $country->id }}, this)">
                                {{ $country->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function toggleStatus(countryId, btnElement) {
        const originalText = btnElement.innerText;
        btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btnElement.disabled = true;

        try {
            const formData = new FormData();
            formData.append('country_id', countryId);
            formData.append('_token', '{{ csrf_token() }}');

            const res = await fetch('/countries/settings/toggle', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if(data.success) {
                const badge = document.getElementById('badge-' + countryId);
                if (data.is_active) {
                    badge.className = 'badge bg-success';
                    badge.innerText = 'Active';
                    btnElement.className = 'btn btn-sm btn-outline-danger';
                    btnElement.innerText = 'Deactivate';
                } else {
                    badge.className = 'badge bg-secondary';
                    badge.innerText = 'Inactive';
                    btnElement.className = 'btn btn-sm btn-outline-success';
                    btnElement.innerText = 'Activate';
                }
            }
        } catch (e) {
            alert('Error toggling status');
            btnElement.innerText = originalText;
        }
        btnElement.disabled = false;
    }

    function filterTable() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toLowerCase();
        const table = document.getElementById("countriesTable");
        const tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            const tdName = tr[i].getElementsByTagName("td")[0];
            const tdRegion = tr[i].getElementsByTagName("td")[1];
            if (tdName || tdRegion) {
                const txtValueName = tdName.textContent || tdName.innerText;
                const txtValueRegion = tdRegion.textContent || tdRegion.innerText;
                if (txtValueName.toLowerCase().indexOf(filter) > -1 || txtValueRegion.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    async function bulkToggle(action) {
        if (!confirm(`Are you sure you want to ${action} ALL countries?`)) return;

        const btnAct = document.getElementById('btnActivateAll');
        const btnDeact = document.getElementById('btnDeactivateAll');
        btnAct.disabled = true; btnDeact.disabled = true;

        try {
            const formData = new FormData();
            formData.append('status', action);
            formData.append('_token', '{{ csrf_token() }}');

            const res = await fetch('/countries/settings/bulk', { method: 'POST', body: formData });
            const data = await res.json();
            
            if(data.success) {
                window.location.reload();
            }
        } catch (e) {
            alert('Error toggling all countries');
            btnAct.disabled = false; btnDeact.disabled = false;
        }
    }
</script>
@endpush
