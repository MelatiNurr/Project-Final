@extends('layouts.public')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Global Risk Average</h6>
                    <p class="small text-secondary mb-0">Based on monitored regions</p>
                </div>
                <div class="pulse rounded-circle bg-info p-2" style="width:12px;height:12px;"></div>
            </div>
            <div class="mt-3">
                <span class="stat-value" id="global-risk-val">0%</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Countries Tracked</h6>
                    <p class="small text-secondary mb-0">Active watchlists</p>
                </div>
                <i class="fa-solid fa-flag text-secondary fs-4"></i>
            </div>
            <div class="mt-3">
                <span class="stat-value" id="countries-val">0</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Active Ports</h6>
                    <p class="small text-secondary mb-0">Operational nodes</p>
                </div>
                <i class="fa-solid fa-anchor text-secondary fs-4"></i>
            </div>
            <div class="mt-3">
                <span class="stat-value" id="ports-val">0</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted fw-semibold mb-1">System Status</h6>
                    <p class="small text-secondary mb-0">API & Sync health</p>
                </div>
                <i class="fa-solid fa-server text-success fs-4"></i>
            </div>
            <div class="mt-3">
                <span class="stat-value text-success" style="font-size: 2rem;">Online</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                <h5 class="card-title fw-bold mb-0"><i class="fa-solid fa-map-location-dot text-primary me-2"></i> Supply Chain Risk Map</h5>
                <span class="badge bg-secondary">Live</span>
            </div>
            <div id="map"></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 h-100">
            <h5 class="card-title fw-bold mb-4"><i class="fa-solid fa-code-compare text-warning me-2"></i> Country Comparison</h5>
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-semibold">Country A (Base)</label>
                <select id="country-a-select" class="form-select bg-dark text-light border-secondary shadow-sm">
                    <option value="">Loading...</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted small fw-semibold">Country B (Target)</label>
                <select id="country-b-select" class="form-select bg-dark text-light border-secondary shadow-sm">
                    <option value="">Loading...</option>
                </select>
            </div>

            <button class="btn btn-primary w-100 mb-4 fw-semibold shadow" onclick="compareCountries()"><i class="fa-solid fa-bolt me-1"></i> Analyze Risk Delta</button>

            <div id="comparison-results" class="d-none mt-4">
                <h6 class="fw-bold mb-3 border-bottom border-secondary pb-2">Risk Breakdown</h6>
                <canvas id="comparisonChart" height="250"></canvas>
                
                <div class="mt-4 pt-3 border-top border-secondary">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span id="ca-name" class="fw-bold text-info">Country A</span>
                        <span class="badge bg-dark border border-secondary text-light">Total Score</span>
                        <span id="cb-name" class="fw-bold text-warning">Country B</span>
                    </div>
                    <div class="progress mt-2" style="height: 12px; background-color: #334155;">
                        <div id="ca-risk-bar" class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
                        <div id="cb-risk-bar" class="progress-bar bg-warning" role="progressbar" style="width: 0%; right: 0; position: absolute;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global Variables
    let map;
    let chartInstance = null;
    let countriesData = [];
    let riskData = [];
    let portsData = [];

    // Initialize Map
    function initMap() {
        // Dark theme map using CartoDB Dark Matter
        map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://carto.com/">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);
    }

    // Fetch API Data
    async function fetchDashboardData() {
        try {
            // Fetch concurrently for speed
            const [countriesRes, riskRes, portsRes] = await Promise.all([
                fetch('/api/countries'),
                fetch('/api/risk'),
                fetch('/api/ports')
            ]);

            countriesData = await countriesRes.json();
            riskData = await riskRes.json();
            portsData = await portsRes.json();

            updateMetrics();
            populateSelects();
            renderMapMarkers();

        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function updateMetrics() {
        document.getElementById('countries-val').innerText = countriesData.length;
        document.getElementById('ports-val').innerText = portsData.length;
        
        if (riskData.length > 0) {
            const avg = riskData.reduce((acc, curr) => acc + parseFloat(curr.total_score), 0) / riskData.length;
            document.getElementById('global-risk-val').innerText = avg.toFixed(1) + '%';
        }
    }

    function populateSelects() {
        const selectA = document.getElementById('country-a-select');
        const selectB = document.getElementById('country-b-select');
        
        let options = '<option value="">Select Country...</option>';
        countriesData.forEach(c => {
            options += `<option value="${c.id}">${c.name}</option>`;
        });

        selectA.innerHTML = options;
        selectB.innerHTML = options;
    }

    function renderMapMarkers() {
        // Mock data logic for empty DB, normally we use riskData
        // Plot ports
        portsData.forEach(port => {
            L.circleMarker([port.latitude, port.longitude], {
                radius: 4,
                fillColor: "#38bdf8",
                color: "#fff",
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map).bindPopup(`<b>${port.name}</b><br>Status: ${port.status}`);
        });

        // Plot Country Risks
        riskData.forEach(risk => {
            if(risk.country && risk.country.latitude) {
                let color = risk.total_score > 60 ? '#ef4444' : (risk.total_score > 30 ? '#f59e0b' : '#10b981');
                L.circleMarker([risk.country.latitude, risk.country.longitude], {
                    radius: 8 + (risk.total_score / 10),
                    fillColor: color,
                    color: color,
                    weight: 1,
                    opacity: 0.5,
                    fillOpacity: 0.4
                }).addTo(map).bindPopup(`<b>${risk.country.name}</b><br>Risk: ${risk.total_score}%`);
            }
        });
    }

    // Comparison Logic
    function compareCountries() {
        const idA = document.getElementById('country-a-select').value;
        const idB = document.getElementById('country-b-select').value;

        if (!idA || !idB) return alert('Please select both countries.');
        if (idA === idB) return alert('Please select different countries.');

        const riskA = riskData.find(r => r.country_id == idA) || {weather_risk:0, economic_risk:0, sentiment_risk:0, total_score:0, country: countriesData.find(c=>c.id==idA)};
        const riskB = riskData.find(r => r.country_id == idB) || {weather_risk:0, economic_risk:0, sentiment_risk:0, total_score:0, country: countriesData.find(c=>c.id==idB)};

        document.getElementById('comparison-results').classList.remove('d-none');
        document.getElementById('ca-name').innerText = riskA.country ? riskA.country.name : 'Unknown';
        document.getElementById('cb-name').innerText = riskB.country ? riskB.country.name : 'Unknown';

        // Update total score bar (split 100% width proportionally)
        let totalSum = parseFloat(riskA.total_score) + parseFloat(riskB.total_score);
        if(totalSum === 0) totalSum = 1; // prevent div zero
        
        document.getElementById('ca-risk-bar').style.width = (parseFloat(riskA.total_score) / totalSum * 100) + '%';
        document.getElementById('cb-risk-bar').style.width = (parseFloat(riskB.total_score) / totalSum * 100) + '%';

        renderChart(riskA, riskB);
    }

    function renderChart(riskA, riskB) {
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        
        if (chartInstance) {
            chartInstance.destroy();
        }

        Chart.defaults.color = '#94a3b8';
        chartInstance = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Weather', 'Economy', 'Sentiment'],
                datasets: [
                    {
                        label: riskA.country ? riskA.country.name : 'Country A',
                        data: [riskA.weather_risk, riskA.economic_risk, riskA.sentiment_risk],
                        backgroundColor: 'rgba(56, 189, 248, 0.2)',
                        borderColor: '#38bdf8',
                        pointBackgroundColor: '#38bdf8',
                    },
                    {
                        label: riskB.country ? riskB.country.name : 'Country B',
                        data: [riskB.weather_risk, riskB.economic_risk, riskB.sentiment_risk],
                        backgroundColor: 'rgba(245, 158, 11, 0.2)',
                        borderColor: '#f59e0b',
                        pointBackgroundColor: '#f59e0b',
                    }
                ]
            },
            options: {
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        pointLabels: { font: { size: 12, family: 'Inter' } },
                        ticks: { display: false, max: 100, min: 0 }
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Init App
    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        fetchDashboardData();
    });
</script>
@endpush
