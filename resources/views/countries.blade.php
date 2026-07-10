@extends('layouts.public')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold text-light mb-0"><i class="fa-solid fa-flag text-warning me-2"></i> Global Country Profiles</h3>
        <span class="text-muted">Comprehensive risk & environmental data for all monitored regions</span>
    </div>
</div>

<div class="row g-4">
    @foreach($countries as $country)
        @php
            $risk = $country->riskScores->first();
            $riskScore = $risk ? $risk->total_score : 0;
            $riskColor = $riskScore > 60 ? 'danger' : ($riskScore > 30 ? 'warning' : 'success');
            
            // Storm risk from weather risk if available
            $weatherRisk = $risk ? $risk->weather_risk : 0;
            $stormDanger = $weatherRisk > 50;
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-secondary">
                <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-light">{{ $country->name }}</h5>
                    <span class="badge bg-{{ $riskColor }} px-2 py-1 border border-{{ $riskColor }} rounded-pill" style="font-size: 0.85rem;">
                        Risk: {{ $riskScore }}%
                    </span>
                </div>
                <div class="card-body p-4 bg-dark bg-opacity-50">
                    <div class="d-flex justify-content-between mb-3 text-muted small text-uppercase fw-bold border-bottom border-secondary pb-2">
                        <span><i class="fa-solid fa-map text-info me-1"></i> {{ $country->region }}</span>
                        <span><i class="fa-solid fa-coins text-success me-1"></i> {{ $country->currency }}</span>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-2 rounded bg-dark border border-secondary text-center h-100">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-temperature-half text-warning me-1"></i> Temp</span>
                                <span class="fw-bold text-light">{{ $country->temperature ?? 'N/A' }}&deg;C</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-dark border border-secondary text-center h-100">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-wind text-info me-1"></i> Wind</span>
                                <span class="fw-bold text-light">{{ $country->wind_speed ?? 'N/A' }} km/h</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-dark border border-secondary text-center h-100">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-chart-line text-success me-1"></i> GDP</span>
                                <span class="fw-bold text-light">${{ number_format($country->gdp / 1e9, 1) }}B</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-dark border border-secondary text-center h-100">
                                <span class="d-block small text-muted mb-1"><i class="fa-solid fa-cloud-bolt text-danger me-1"></i> Storm Risk</span>
                                @if($stormDanger)
                                    <span class="badge bg-danger">HIGH</span>
                                @else
                                    <span class="badge bg-success">LOW</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('country.show', $country->id) }}" class="btn btn-outline-info w-100 fw-bold">
                        <i class="fa-solid fa-chart-pie me-1"></i> Full Profile & Reports
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
