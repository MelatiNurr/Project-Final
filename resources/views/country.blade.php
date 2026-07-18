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
        <h3 class="fw-bold text-light mb-0"><i class="fa-solid fa-flag text-info me-2"></i> {{ $country->name }} Profile</h3>
        <span class="text-muted">{{ $country->region }} Region</span>
    </div>
    <div class="text-end">
        <span class="d-block small text-muted text-uppercase fw-bold mb-1">Overall Risk Level</span>
        <span class="badge bg-{{ $riskColor }} fs-5 px-3 py-2">{{ $riskScore }}%</span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Macro Environment -->
    <div class="col-md-8">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-4 border-bottom border-secondary pb-2"><i class="fa-solid fa-chart-pie text-warning me-2"></i> Macro Environment</h5>
            <div class="row g-4">
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-dark rounded border border-secondary h-100">
                        <i class="fa-solid fa-users text-primary fs-1 mb-3 opacity-75"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Population</h6>
                        <span class="fs-4 fw-bold text-light">{{ $country->population ? number_format($country->population / 1e6, 1) . 'M' : 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-dark rounded border border-secondary h-100">
                        <i class="fa-solid fa-money-bill-wave text-success fs-1 mb-3 opacity-75"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">GDP</h6>
                        <span class="fs-4 fw-bold text-light">${{ number_format($country->gdp / 1e9, 1) }}B</span>
                    </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-dark rounded border border-secondary h-100">
                        <i class="fa-solid fa-arrow-trend-up text-danger fs-1 mb-3 opacity-75"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Inflation</h6>
                        <span class="fs-4 fw-bold text-light">{{ $country->inflation }}%</span>
                    </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="p-3 bg-dark rounded border border-secondary h-100">
                        <i class="fa-solid fa-coins text-info fs-1 mb-3 opacity-75"></i>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Currency ({{ $country->currency }})</h6>
                        <span class="fs-5 fw-bold text-light">1 USD = {{ number_format($country->exchange_rate, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Weather & Geography -->
    <div class="col-md-4">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-4 border-bottom border-secondary pb-2"><i class="fa-solid fa-cloud-sun text-info me-2"></i> Regional Conditions</h5>
            <div class="d-flex align-items-center mb-3">
                <div class="bg-dark p-3 rounded text-center me-3 border border-secondary" style="width: 80px;">
                    <i class="fa-solid fa-temperature-half text-warning fs-3 mb-1"></i>
                </div>
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-0">Temperature</h6>
                    <span class="fs-4 fw-bold">{{ $country->temperature ?? 'N/A' }}&deg;C</span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="bg-dark p-3 rounded text-center me-3 border border-secondary" style="width: 80px;">
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
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-4 border-bottom border-secondary pb-2"><i class="fa-solid fa-anchor text-secondary me-2"></i> Operational Ports</h5>
            @if($country->ports->count() > 0)
                <ul class="list-group list-group-flush bg-transparent">
                    @foreach($country->ports as $port)
                    <li class="list-group-item bg-transparent border-secondary text-light px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fa-solid fa-location-dot text-info me-2"></i> {{ $port->name }}
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
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-4 border-bottom border-secondary pb-2 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-newspaper text-primary me-2"></i> Local Intelligence Reports</span>
                <span class="badge bg-dark border border-secondary text-light">{{ $country->articles->count() }} Updates</span>
            </h5>
            
            <div class="row g-3">
                @forelse($country->articles as $article)
                    @php
                        $badgeClass = 'bg-secondary';
                        if ($article->sentiment_score > 0) $badgeClass = 'bg-success';
                        else if ($article->sentiment_score < 0) $badgeClass = 'bg-danger';
                    @endphp
                    <div class="col-md-6">
                        <div class="card bg-dark border border-secondary h-100 p-3" style="border-left: 3px solid {{ $article->sentiment_score < 0 ? '#ef4444' : ($article->sentiment_score > 0 ? '#10b981' : '#64748b') }} !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted"><i class="fa-solid fa-rss me-1"></i> {{ $article->source }}</span>
                                <span class="badge {{ $badgeClass }}">{{ $article->sentiment_score }}</span>
                            </div>
                            <h6 class="fw-bold" style="font-size: 0.95rem;">{{ $article->title }}</h6>
                            <div class="mt-auto pt-2 text-end">
                                <span class="small text-muted me-3">{{ $article->published_at->diffForHumans() }}</span>
                                <a href="{{ $article->url }}" target="_blank" class="btn btn-sm btn-outline-info p-1 px-2" style="font-size: 0.75rem;">Read</a>
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

@endsection
