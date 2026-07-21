@extends('layouts.public')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0 text-transparent bg-clip-text" style="color: #4a3b32;">
        <i class="fa-solid fa-chart-pie me-2" style="color: #a98467;"></i> Data Visualization Dashboard
    </h3>
    <span class="text-muted small">Real-time Risk Analytics</span>
</div>

<div class="row g-4 mb-4">
    <!-- Top 5 Highest Risk Countries (Bar Chart) -->
    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
                <i class="fa-solid fa-chart-column me-2" style="color: #ef4444;"></i> Top 5 Highest Risk Countries
            </h5>
            <div style="height: 300px; position: relative;">
                <canvas id="topRiskChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Global Risk Distribution (Doughnut Chart) -->
    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
                <i class="fa-solid fa-circle-notch me-2" style="color: #3b82f6;"></i> Global Risk Distribution
            </h5>
            <div style="height: 300px; position: relative; display: flex; justify-content: center;">
                <canvas id="riskDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Economic Impact vs Risk Score (Scatter Chart) -->
    <div class="col-md-8">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
                <i class="fa-solid fa-money-bill-trend-up me-2" style="color: #10b981;"></i> Economic Size (GDP) vs Overall Risk
            </h5>
            <div style="height: 350px; position: relative;">
                <canvas id="gdpRiskScatterChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Average Sentiment by Region (Polar Area) -->
    <div class="col-md-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
                <i class="fa-solid fa-comment-dots me-2" style="color: #8b5cf6;"></i> Average Risk by Region
            </h5>
            <div style="height: 350px; position: relative; display: flex; justify-content: center;">
                <canvas id="regionRiskChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 10 Countries by Inflation -->
    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
                <i class="fa-solid fa-arrow-trend-up me-2" style="color: #ef4444;"></i> Top Countries by Inflation
            </h5>
            <div style="height: 300px; position: relative;">
                <canvas id="inflationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Currency Exchange Rates (Top 10) -->
    <div class="col-md-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: #4a3b32; border-color: rgba(169, 132, 103, 0.3) !important;">
                <i class="fa-solid fa-money-bill-wave me-2" style="color: #10b981;"></i> Currency Exchange Rates vs USD
            </h5>
            <div style="height: 300px; position: relative;">
                <canvas id="currencyChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Prepare Data
    const rawData = @json($countries);
    
    // Default Chart Configurations
    Chart.defaults.font.family = 'Inter';
    Chart.defaults.color = '#8c7a6b';

    // Helper: Parse Risk Score safely
    const getRiskScore = (country) => {
        return (country.risk_scores && country.risk_scores.length > 0) 
            ? parseFloat(country.risk_scores[0].total_score) 
            : 0;
    };

    // --- 1. Top 5 Highest Risk Countries (Horizontal Bar) ---
    const sortedByRisk = [...rawData].sort((a, b) => getRiskScore(b) - getRiskScore(a)).slice(0, 5);
    const topRiskCtx = document.getElementById('topRiskChart').getContext('2d');
    
    // Create gradient
    let barGradient = topRiskCtx.createLinearGradient(0, 0, 400, 0);
    barGradient.addColorStop(0, 'rgba(239, 68, 68, 0.6)');
    barGradient.addColorStop(1, 'rgba(245, 158, 11, 0.6)');

    new Chart(topRiskCtx, {
        type: 'bar',
        data: {
            labels: sortedByRisk.map(c => c.name),
            datasets: [{
                label: 'Overall Risk Score (%)',
                data: sortedByRisk.map(c => getRiskScore(c)),
                backgroundColor: barGradient,
                borderColor: '#ef4444',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { max: 100, grid: { color: 'rgba(169, 132, 103, 0.1)' } },
                y: { grid: { display: false } }
            }
        }
    });

    // --- 2. Global Risk Distribution (Doughnut) ---
    let lowRisk = 0, medRisk = 0, highRisk = 0;
    rawData.forEach(c => {
        let score = getRiskScore(c);
        if (score === 0) return; // skip unanalyzed
        if (score < 30) lowRisk++;
        else if (score < 60) medRisk++;
        else highRisk++;
    });

    const distCtx = document.getElementById('riskDistributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Low Risk (<30)', 'Medium Risk (30-60)', 'High Risk (>60)'],
            datasets: [{
                data: [lowRisk, medRisk, highRisk],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)', // Green
                    'rgba(245, 158, 11, 0.8)', // Yellow
                    'rgba(239, 68, 68, 0.8)'   // Red
                ],
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // --- 3. Economic Impact vs Risk Score (Scatter) ---
    // Plot GDP (in Billions) on X axis, and Risk Score on Y axis
    const scatterData = rawData.filter(c => c.gdp > 0 && getRiskScore(c) > 0).map(c => ({
        x: c.gdp / 1e9, // GDP in Billions
        y: getRiskScore(c),
        country: c.name
    }));

    const scatterCtx = document.getElementById('gdpRiskScatterChart').getContext('2d');
    new Chart(scatterCtx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Countries',
                data: scatterData,
                backgroundColor: 'rgba(56, 189, 248, 0.6)',
                borderColor: '#0284c7',
                pointRadius: 8,
                pointHoverRadius: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            let label = ctx.raw.country || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'GDP $' + ctx.parsed.x.toFixed(1) + 'B, Risk ' + ctx.parsed.y + '%';
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'GDP (Billions USD)' },
                    grid: { color: 'rgba(169, 132, 103, 0.1)' }
                },
                y: {
                    title: { display: true, text: 'Total Risk Score (%)' },
                    grid: { color: 'rgba(169, 132, 103, 0.1)' },
                    min: 0, max: 100
                }
            }
        }
    });

    // --- 4. Average Risk by Region (Polar Area) ---
    const regions = {};
    rawData.forEach(c => {
        let score = getRiskScore(c);
        if (score === 0 || !c.region) return;
        if (!regions[c.region]) {
            regions[c.region] = { sum: 0, count: 0 };
        }
        regions[c.region].sum += score;
        regions[c.region].count++;
    });

    const regionLabels = Object.keys(regions);
    const regionAverages = regionLabels.map(r => (regions[r].sum / regions[r].count).toFixed(1));

    const regionCtx = document.getElementById('regionRiskChart').getContext('2d');
    new Chart(regionCtx, {
        type: 'polarArea',
        data: {
            labels: regionLabels,
            datasets: [{
                data: regionAverages,
                backgroundColor: [
                    'rgba(169, 132, 103, 0.7)',
                    'rgba(212, 163, 115, 0.7)',
                    'rgba(250, 237, 205, 0.7)',
                    'rgba(204, 213, 174, 0.7)',
                    'rgba(233, 237, 201, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    grid: { color: 'rgba(169, 132, 103, 0.1)' },
                    ticks: { backdropColor: 'transparent' }
                }
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // --- 5. Top Countries by Inflation (Bar Chart) ---
    const sortedByInflation = [...rawData].filter(c => c.inflation !== null).sort((a, b) => b.inflation - a.inflation).slice(0, 10);
    const inflationCtx = document.getElementById('inflationChart').getContext('2d');
    
    new Chart(inflationCtx, {
        type: 'bar',
        data: {
            labels: sortedByInflation.map(c => c.name),
            datasets: [{
                label: 'Inflation Rate (%)',
                data: sortedByInflation.map(c => c.inflation),
                backgroundColor: 'rgba(239, 68, 68, 0.6)',
                borderColor: '#ef4444',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(169, 132, 103, 0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // --- 6. Currency Exchange Rates (Bar Chart) ---
    // Exclude USD since it's 1, sort highest exchange rate vs USD
    const sortedByCurrency = [...rawData].filter(c => c.exchange_rate !== null && c.code !== 'US').sort((a, b) => b.exchange_rate - a.exchange_rate).slice(0, 10);
    const currencyCtx = document.getElementById('currencyChart').getContext('2d');
    
    new Chart(currencyCtx, {
        type: 'bar',
        data: {
            labels: sortedByCurrency.map(c => `${c.name} (${c.currency})`),
            datasets: [{
                label: 'Exchange Rate vs USD',
                data: sortedByCurrency.map(c => c.exchange_rate),
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: '#10b981',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(169, 132, 103, 0.1)' } },
                x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } }
            }
        }
    });
</script>
@endpush
