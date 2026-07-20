@extends('layouts.public')

@section('content')
@php
    $risk = $country->riskScores->first();
    $riskScore = $risk ? $risk->total_score : 0;
    $riskColor = $riskScore > 60 ? 'danger' : ($riskScore > 30 ? 'warning' : 'success');
@endphp

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="/" class="btn btn-sm btn-outline-secondary mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard</a>
        <div class="d-flex align-items-center gap-3">
            <h3 class="fw-bold mb-0" style="color: #4a3b32;"><i class="fa-solid fa-flag me-2" style="color: #a98467;"></i> {{ $country->name }} Profile</h3>
            @auth
                @php
                    $isWatched = in_array($country->id, $watchedIds ?? []);
                @endphp
                <button class="btn btn-sm border bg-white shadow-sm toggle-watchlist" data-id="{{ $country->id }}" title="Toggle Watchlist" style="border-color: rgba(169, 132, 103, 0.3) !important; border-radius: 8px;">
                    <i class="{{ $isWatched ? 'fa-solid text-warning' : 'fa-regular text-secondary' }} fa-star transition-all"></i> <span class="watchlist-text fw-semibold ms-1" style="color: #4a3b32;">{{ $isWatched ? 'Favorited' : 'Add to Watchlist' }}</span>
                </button>
            @endauth
        </div>
        <span class="text-muted d-block mt-2">{{ $country->region }} Region</span>
    </div>
    <div class="text-end">
        <span class="d-block small text-muted text-uppercase fw-bold mb-1">Overall Risk Level</span>
        <span class="badge bg-{{ $riskColor }} fs-5 px-3 py-2">{{ $riskScore }}%</span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Macro Environment -->
    <div class="col-md-8">
        <div class="glass-card h-100 p-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;"><i class="fa-solid fa-chart-pie me-2" style="color: #a98467;"></i> Macro Environment</h5>
            <div class="row g-4">
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                        <i class="fa-solid fa-users text-primary fs-1 mb-3 opacity-75"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Population</h6>
                        <span class="fs-4 fw-bold text-dark">{{ $country->population ? number_format($country->population / 1e6, 1) . 'M' : 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                        <i class="fa-solid fa-money-bill-wave text-success fs-1 mb-3 opacity-75"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">GDP</h6>
                        <span class="fs-4 fw-bold text-dark">${{ number_format($country->gdp / 1e9, 1) }}B</span>
                    </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                        <i class="fa-solid fa-arrow-trend-up text-danger fs-1 mb-3 opacity-75"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Inflation</h6>
                        <span class="fs-4 fw-bold text-dark">{{ $country->inflation }}%</span>
                    </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                        <i class="fa-solid fa-coins fs-1 mb-3 opacity-75" style="color: #a98467;"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Currency ({{ $country->currency }})</h6>
                        <span class="fs-5 fw-bold text-dark">1 USD = {{ number_format($country->exchange_rate, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Weather & Geography -->
    <div class="col-md-4">
        <div class="glass-card h-100 p-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;"><i class="fa-solid fa-cloud-sun me-2" style="color: #a98467;"></i> Regional Conditions</h5>
            <div class="d-flex align-items-center mb-3">
                <div class="bg-white p-3 rounded text-center me-3 border" style="width: 80px; border-color: rgba(169, 132, 103, 0.3) !important;">
                    <i class="fa-solid fa-temperature-half text-danger fs-3 mb-1"></i>
                </div>
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-0">Temperature</h6>
                    <span class="fs-4 fw-bold">{{ $country->temperature ?? 'N/A' }}&deg;C</span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="bg-white p-3 rounded text-center me-3 border" style="width: 80px; border-color: rgba(169, 132, 103, 0.3) !important;">
                    <i class="fa-solid fa-wind text-info fs-3 mb-1"></i>
                </div>
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-0">Wind Speed</h6>
                    <span class="fs-4 fw-bold">{{ $country->wind_speed ?? 'N/A' }} km/h</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Active Ports -->
    <div class="col-md-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;"><i class="fa-solid fa-anchor me-2" style="color: #a98467;"></i> Operational Ports</h5>
            @if($country->ports->count() > 0)
                <ul class="list-group list-group-flush bg-transparent">
                    @foreach($country->ports as $port)
                    <li class="list-group-item bg-transparent text-dark px-0 d-flex justify-content-between align-items-center" style="border-color: rgba(169, 132, 103, 0.2) !important;">
                        <div>
                            <i class="fa-solid fa-location-dot me-2" style="color: #a98467;"></i> {{ $port->name }}
                        </div>
                        <span class="badge bg-success rounded-pill">Active</span>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-muted py-3">
                    <i class="fa-solid fa-ship fs-1 mb-2 opacity-50"></i>
                    <p class="mb-0">No active ports recorded.</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Local Intelligence News -->
    <div class="col-md-8">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
                <span><i class="fa-solid fa-newspaper me-2" style="color: #a98467;"></i> Local Intelligence Reports</span>
                <span class="badge bg-white border text-dark shadow-sm" style="border-color: rgba(169, 132, 103, 0.3) !important;">{{ $country->articles->count() }} Updates</span>
            </h5>
            
            <div class="row g-3">
                @forelse($country->articles as $article)
                    @php
                        $badgeClass = 'bg-secondary';
                        if ($article->sentiment_score > 0) $badgeClass = 'bg-success';
                        else if ($article->sentiment_score < 0) $badgeClass = 'bg-danger';
                    @endphp
                    <div class="col-md-6">
                        <div class="card bg-white border h-100 p-3 shadow-sm" style="border-color: rgba(169, 132, 103, 0.2) !important; border-left: 3px solid {{ $article->sentiment_score < 0 ? '#ef4444' : ($article->sentiment_score > 0 ? '#10b981' : '#a98467') }} !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted"><i class="fa-solid fa-rss me-1" style="color: #a98467;"></i> {{ $article->source }}</span>
                                <span class="badge {{ $badgeClass }}">{{ $article->sentiment_score }}</span>
                            </div>
                            <h6 class="fw-bold" style="font-size: 0.95rem; color: #4a3b32;">{{ $article->title }}</h6>
                            <div class="mt-auto pt-2 text-end">
                                <span class="small text-muted me-3">{{ $article->published_at->diffForHumans() }}</span>
                                <a href="{{ $article->url }}" target="_blank" class="btn btn-sm btn-outline-coksu p-1 px-2" style="font-size: 0.75rem;">Read</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">
                        <i class="fa-solid fa-satellite-dish fs-1 mb-3 opacity-50"></i>
                        <p>No recent intelligence intercepted for {{ $country->name }}.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Historical Trends -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <h4 class="fw-bold mb-3 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
            <i class="fa-solid fa-clock-rotate-left me-2" style="color: #a98467;"></i> 6-Month Historical Trends
        </h4>
    </div>
    
    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-muted text-uppercase small">GDP Trend (Billions USD)</h6>
            <div style="height: 250px;"><canvas id="gdpChart"></canvas></div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-muted text-uppercase small">Inflation Trend (%)</h6>
            <div style="height: 250px;"><canvas id="inflationChart"></canvas></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-muted text-uppercase small">Currency vs USD ({{ $country->currency }})</h6>
            <div style="height: 250px;"><canvas id="currencyChart"></canvas></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-muted text-uppercase small">Overall Risk Score (%)</h6>
            <div style="height: 250px;"><canvas id="riskChart"></canvas></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-watchlist').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const countryId = btn.getAttribute('data-id');
            const icon = btn.querySelector('i');
            const textSpan = btn.querySelector('.watchlist-text');
            
            // Optimistic UI update
            const isCurrentlyWatched = icon.classList.contains('fa-solid');
            if (isCurrentlyWatched) {
                icon.classList.remove('fa-solid', 'text-warning');
                icon.classList.add('fa-regular', 'text-secondary');
                if (textSpan) textSpan.innerText = 'Add to Watchlist';
            } else {
                icon.classList.remove('fa-regular', 'text-secondary');
                icon.classList.add('fa-solid', 'text-warning');
                if (textSpan) textSpan.innerText = 'Favorited';
            }

            try {
                const res = await fetch('/watchlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ country_id: countryId })
                });

                if (!res.ok) {
                    throw new Error('Failed to toggle');
                }
            } catch (error) {
                console.error(error);
                // Revert UI on failure
                if (isCurrentlyWatched) {
                    icon.classList.add('fa-solid', 'text-warning');
                    icon.classList.remove('fa-regular', 'text-secondary');
                    if (textSpan) textSpan.innerText = 'Favorited';
                } else {
                    icon.classList.add('fa-regular', 'text-secondary');
                    icon.classList.remove('fa-solid', 'text-warning');
                    if (textSpan) textSpan.innerText = 'Add to Watchlist';
                }
                alert('An error occurred. Please try again.');
            }
        });
    });

    // --- Mock Data Generator for Trend Charts ---
    // Generate an array of 6 elements ending with the current real value.
    // The previous 5 elements are randomized slightly to simulate a trend.
    function generateTrendData(currentValue, variancePercent = 0.05) {
        let data = [];
        let val = currentValue;
        for (let i = 0; i < 5; i++) {
            // randomize between -variance and +variance
            let change = val * (Math.random() * variancePercent * 2 - variancePercent);
            val = val - change; 
            data.unshift(val);
        }
        data.push(currentValue); // the 6th element is the real current value
        return data;
    }

    // Get current DB values
    const currentGdp = {{ $country->gdp / 1e9 }};
    const currentInflation = {{ $country->inflation }};
    const currentExchange = {{ $country->exchange_rate }};
    const currentRisk = {{ $riskScore }};

    // Generate last 6 months labels
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const d = new Date();
    let labels = [];
    for (let i = 5; i >= 0; i--) {
        let dCopy = new Date(d);
        dCopy.setMonth(d.getMonth() - i);
        labels.push(monthNames[dCopy.getMonth()] + " '" + dCopy.getFullYear().toString().substr(-2));
    }

    // Shared Chart Options (Coksu Theme)
    Chart.defaults.font.family = 'Inter';
    Chart.defaults.color = '#8c7a6b';
    
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: 'rgba(169, 132, 103, 0.1)' } }
        },
        interaction: { intersect: false, mode: 'index' }
    };

    function createLineChart(ctxId, data, colorPrimary, colorBg) {
        const ctx = document.getElementById(ctxId).getContext('2d');
        
        // Gradient fill
        let gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, colorBg);
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderColor: colorPrimary,
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: colorPrimary,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: colorPrimary,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: commonOptions
        });
    }

    // Initialize 4 Charts
    document.addEventListener("DOMContentLoaded", function() {
        createLineChart('gdpChart', generateTrendData(currentGdp, 0.02), '#10b981', 'rgba(16, 185, 129, 0.2)');
        createLineChart('inflationChart', generateTrendData(currentInflation, 0.1), '#ef4444', 'rgba(239, 68, 68, 0.2)');
        createLineChart('currencyChart', generateTrendData(currentExchange, 0.05), '#d4a373', 'rgba(212, 163, 115, 0.2)');
        createLineChart('riskChart', generateTrendData(currentRisk, 0.1), '#f59e0b', 'rgba(245, 158, 11, 0.2)');
    });
</script>
@endpush
