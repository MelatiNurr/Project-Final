@extends('layouts.public')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-0" style="color: #4a3b32;"><i class="fa-solid fa-star me-2" style="color: #f59e0b;"></i> My Watchlist</h3>
        <span class="text-muted">Your curated list of priority countries for active monitoring</span>
    </div>
</div>

<div class="row g-4">
    @forelse($countries as $country)
        @php
            $risk = $country->riskScores->first();
            $riskScore = $risk ? $risk->total_score : 0;
            $riskColor = $riskScore > 60 ? 'danger' : ($riskScore > 30 ? 'warning' : 'success');
            
            // Storm risk from weather risk if available
            $weatherRisk = $risk ? $risk->weather_risk : 0;
            $stormDanger = $weatherRisk > 50;
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card glass-card h-100 overflow-hidden d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom" style="background: rgba(169, 132, 103, 0.1); border-color: rgba(169, 132, 103, 0.2) !important;">
                    <div class="d-flex align-items-center gap-2">
                        @auth
                            @php
                                $isWatched = in_array($country->id, $watchedIds ?? []);
                            @endphp
                            <button class="btn btn-sm border-0 p-0 shadow-none toggle-watchlist" data-id="{{ $country->id }}" title="Toggle Watchlist">
                                <i class="{{ $isWatched ? 'fa-solid text-warning' : 'fa-regular text-secondary' }} fa-star fs-5 transition-all"></i>
                            </button>
                        @endauth
                        <h5 class="mb-0 fw-bold" style="color: #4a3b32;">{{ $country->name }}</h5>
                    </div>
                    <span class="badge bg-{{ $riskColor }} px-2 py-1 shadow-sm rounded-pill" style="font-size: 0.85rem;">
                        Risk: {{ $riskScore }}%
                    </span>
                </div>
                <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                    <div class="d-flex justify-content-between mb-3 text-muted small text-uppercase fw-bold border-bottom pb-2" style="border-color: rgba(169, 132, 103, 0.2) !important;">
                        <span><i class="fa-solid fa-map me-1" style="color: #a98467;"></i> {{ $country->region }}</span>
                        <span><i class="fa-solid fa-coins me-1" style="color: #a98467;"></i> {{ $country->currency }}</span>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-2 rounded bg-white border text-center h-100" style="border-color: rgba(169, 132, 103, 0.2) !important;">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-temperature-half text-danger me-1"></i> Temp</span>
                                <span class="fw-bold text-dark">{{ $country->temperature ?? 'N/A' }}&deg;C</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-white border text-center h-100" style="border-color: rgba(169, 132, 103, 0.2) !important;">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-wind text-info me-1"></i> Wind</span>
                                <span class="fw-bold text-dark">{{ $country->wind_speed ?? 'N/A' }} km/h</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-white border text-center h-100" style="border-color: rgba(169, 132, 103, 0.2) !important;">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-chart-line text-success me-1"></i> GDP</span>
                                <span class="fw-bold text-dark">${{ number_format($country->gdp / 1e9, 1) }}B</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-white border text-center h-100" style="border-color: rgba(169, 132, 103, 0.2) !important;">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-cloud-bolt text-danger me-1"></i> Storm Risk</span>
                                @if($stormDanger)
                                    <span class="badge bg-danger shadow-sm">HIGH</span>
                                @else
                                    <span class="badge bg-success shadow-sm">LOW</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ route('country.show', $country->id) }}" class="btn btn-outline-coksu w-100 fw-bold position-relative" style="z-index: 10;">
                            <i class="fa-solid fa-chart-pie me-1"></i> Full Profile & Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 mt-4">
            <i class="fa-regular fa-star fs-1 text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold text-muted">Your watchlist is empty</h5>
            <p class="text-secondary">Explore the <a href="{{ route('countries.index') }}" class="text-coksu fw-bold text-decoration-none">Global Country Profiles</a> and click the star icon to add them here.</p>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-watchlist').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const countryId = btn.getAttribute('data-id');
            const cardCol = btn.closest('.col-md-6.col-lg-4');
            
            if (!confirm('Remove this country from your watchlist?')) return;

            try {
                const res = await fetch('/watchlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ country_id: countryId })
                });

                if (!res.ok) throw new Error('Failed to toggle');
                
                // Fade out and remove
                if (cardCol) {
                    cardCol.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    cardCol.style.opacity = '0';
                    cardCol.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        cardCol.remove();
                        // Check if list is empty
                        const remaining = document.querySelectorAll('.toggle-watchlist').length;
                        if (remaining === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    }, 300);
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred. Please try again.');
            }
        });
    });
</script>
@endpush
