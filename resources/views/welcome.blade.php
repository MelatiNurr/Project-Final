@extends('layouts.public')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-transparent bg-clip-text" style="color: #4a3b32;"><i class="fa-solid fa-satellite-dish me-2" style="color: #a98467;"></i> Global Operations Platform</h4>
    <button id="syncMetricsBtn" class="btn btn-outline-coksu shadow-sm fw-bold" onclick="syncMetrics()">
        <i class="fa-solid fa-rotate me-1"></i> Sync Metrics
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="glass-card h-100 p-4">
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
        <div class="glass-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Countries Tracked</h6>
                    <p class="small text-secondary mb-0">Active watchlists</p>
                </div>
                <i class="fa-solid fa-flag fs-4" style="color: #a98467;"></i>
            </div>
            <div class="mt-3">
                <span class="stat-value" id="countries-val">0</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card h-100 p-4">
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
        <div class="glass-card h-100 p-4">
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
    <div class="col-lg-12">
        <div class="glass-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                <h5 class="card-title fw-bold mb-0" style="color: #4a3b32;"><i class="fa-solid fa-map-location-dot me-2" style="color: #a98467;"></i> Supply Chain Risk Map</h5>
                <span class="badge bg-secondary">Live</span>
            </div>
            <div id="map"></div>

            <!-- Detail Panel -->
            <div id="country-detail-panel" class="d-none mt-4 border-top pt-3" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" id="cd-name" style="color: #4a3b32;">Country Name</h5>
                    <div>
                        <span class="badge" style="background-color: #a98467;">Selected Region</span>
                        <a href="#" id="cd-profile-link" class="btn btn-sm btn-outline-coksu"><i class="fa-solid fa-arrow-right me-1"></i> View Full Profile</a>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                            <span class="d-block small text-muted mb-1"><i class="fa-solid fa-cloud text-info me-1"></i> Temperature</span>
                            <span class="fw-bold fs-5 text-dark" id="cd-temp">-</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                            <span class="d-block small text-muted mb-1"><i class="fa-solid fa-wind text-info me-1"></i> Wind Speed</span>
                            <span class="fw-bold fs-5 text-dark" id="cd-wind">-</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                            <span class="d-block small text-muted mb-1"><i class="fa-solid fa-money-bill-wave text-success me-1"></i> GDP & Inflation</span>
                            <span class="fw-bold fs-6 text-dark" id="cd-econ">-</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border h-100" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                            <span class="d-block small text-muted mb-1"><i class="fa-solid fa-shield-halved text-danger me-1"></i> Overall Risk</span>
                            <span class="fw-bold fs-5 text-danger" id="cd-risk">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="glass-card p-4 h-100">
            <h5 class="card-title fw-bold mb-4" style="color: #4a3b32;"><i class="fa-solid fa-code-compare me-2" style="color: #a98467;"></i> Country Comparison</h5>
            
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-semibold">Country A (Base)</label>
                    <select id="country-a-select" class="form-select bg-white text-dark shadow-sm" style="border-color: rgba(169, 132, 103, 0.3);">
                        <option value="">Loading...</option>
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <button class="btn bg-coksu w-100 fw-bold shadow" onclick="compareCountries()"><i class="fa-solid fa-bolt me-1"></i> Compare</button>
                </div>
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-semibold">Country B (Target)</label>
                    <select id="country-b-select" class="form-select bg-white text-dark shadow-sm" style="border-color: rgba(169, 132, 103, 0.3);">
                        <option value="">Loading...</option>
                    </select>
                </div>
            </div>

            <div id="comparison-results" class="d-none mt-4 border-top pt-4" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                <div class="row g-4">
                    <!-- Left: Data Table -->
                    <div class="col-md-7">
                        <!-- AI Recommendation Box -->
                        <div id="recommendation-box" class="alert alert-success d-none d-flex align-items-center mb-4 shadow-sm" role="alert" style="background-color: rgba(16, 185, 129, 0.1); border-color: #10b981; color: #10b981;">
                            <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">AI Recommendation</h6>
                                <span id="recommendation-text" class="small"></span>
                            </div>
                        </div>

                        <div class="table-responsive rounded border" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                            <table class="table table-hover align-middle text-center mb-0 bg-transparent">
                                <thead>
                                    <tr style="background-color: rgba(169, 132, 103, 0.1);">
                                        <th class="text-start w-25 text-muted small fw-semibold p-3" style="color: #4a3b32 !important;">METRIC</th>
                                        <th id="ca-name" class="w-25 fw-bold p-3" style="color: #4a3b32;">Country A</th>
                                        <th id="cb-name" class="text-warning w-25 fw-bold p-3" style="color: #a98467 !important;">Country B</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start text-muted small fw-semibold p-3"><i class="fa-solid fa-shield-halved me-2"></i> Total Risk</td>
                                        <td class="p-3"><span id="ca-risk-val" class="fs-5 fw-bold text-dark">-</span></td>
                                        <td class="p-3"><span id="cb-risk-val" class="fs-5 fw-bold text-warning">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start text-muted small fw-semibold p-3"><i class="fa-solid fa-money-bill-transfer me-2"></i> Currency (vs USD)</td>
                                        <td id="ca-currency" class="small p-3 text-dark">-</td>
                                        <td id="cb-currency" class="small p-3 text-dark">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start text-muted small fw-semibold p-3"><i class="fa-solid fa-chart-pie me-2"></i> GDP & Inflation</td>
                                        <td id="ca-econ" class="small p-3 text-dark">-</td>
                                        <td id="cb-econ" class="small p-3 text-dark">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start text-muted small fw-semibold p-3"><i class="fa-solid fa-cloud-sun-rain me-2"></i> Weather (Capitals)</td>
                                        <td id="ca-temp" class="small p-3 text-dark">-</td>
                                        <td id="cb-temp" class="small p-3 text-dark">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start text-muted small fw-semibold p-3"><i class="fa-solid fa-anchor me-2"></i> Tracked Ports</td>
                                        <td class="p-3"><span id="ca-ports" class="badge bg-secondary">-</span></td>
                                        <td class="p-3"><span id="cb-ports" class="badge bg-secondary">-</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Right: Chart -->
                    <div class="col-md-5 d-flex flex-column justify-content-center">
                        <h6 class="fw-bold mb-3 text-muted text-center text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Risk Factor Breakdown</h6>
                        <div style="height: 250px; position: relative;">
                            <canvas id="comparisonChart"></canvas>
                        </div>
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
        // Light theme map using CartoDB Light Matter
        map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
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

        new TomSelect('#country-a-select', { create: false, sortField: { field: "text", direction: "asc" }});
        new TomSelect('#country-b-select', { create: false, sortField: { field: "text", direction: "asc" }});
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

        // Plot Countries
        countriesData.forEach(country => {
            if(country.latitude) {
                let risk = riskData.find(r => r.country_id == country.id);
                let color = '#64748b'; // default neutral gray
                let radius = 6;
                let riskText = 'No Data (Run Sync)';

                if (risk) {
                    color = risk.total_score > 60 ? '#ef4444' : (risk.total_score > 30 ? '#f59e0b' : '#10b981');
                    radius = 8 + (risk.total_score / 10);
                    riskText = risk.total_score + '%';
                }

                L.circleMarker([country.latitude, country.longitude], {
                    radius: radius,
                    fillColor: color,
                    color: color,
                    weight: 1,
                    opacity: risk ? 0.5 : 0.3,
                    fillOpacity: risk ? 0.4 : 0.2
                }).addTo(map)
                  .bindPopup(`<b>${country.name}</b><br>Risk: ${riskText}${risk ? ' (Click for details)' : ''}`)
                  .on('click', () => {
                      if(risk) showCountryDetail(risk);
                  });
            }
        });
    }

    function showCountryDetail(risk) {
        document.getElementById('country-detail-panel').classList.remove('d-none');
        document.getElementById('cd-name').innerText = risk.country.name + ' Operations Profile';
        document.getElementById('cd-temp').innerText = risk.country.temperature ? risk.country.temperature + ' °C' : 'N/A';
        document.getElementById('cd-wind').innerText = risk.country.wind_speed ? risk.country.wind_speed + ' km/h' : 'N/A';
        document.getElementById('cd-econ').innerText = risk.country.gdp ? `$${(risk.country.gdp/1e9).toFixed(1)}B | ${risk.country.inflation}%` : 'N/A';
        document.getElementById('cd-risk').innerText = risk.total_score + '%';
        document.getElementById('cd-profile-link').href = '/country/' + risk.country.id;
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

        // Update Risk Value
        document.getElementById('ca-risk-val').innerText = parseFloat(riskA.total_score).toFixed(1) + '%';
        document.getElementById('cb-risk-val').innerText = parseFloat(riskB.total_score).toFixed(1) + '%';

        // Update Ports Count
        let caPorts = portsData.filter(p => p.country_id == idA).length;
        let cbPorts = portsData.filter(p => p.country_id == idB).length;
        document.getElementById('ca-ports').innerText = caPorts;
        document.getElementById('cb-ports').innerText = cbPorts;

        // Update Currency
        document.getElementById('ca-currency').innerText = (riskA.country && riskA.country.currency) ? `1 USD = ${parseFloat(riskA.country.exchange_rate).toFixed(2)} ${riskA.country.currency}` : '-';
        document.getElementById('cb-currency').innerText = (riskB.country && riskB.country.currency) ? `1 USD = ${parseFloat(riskB.country.exchange_rate).toFixed(2)} ${riskB.country.currency}` : '-';
        
        // Update Raw Data (Handle 0 values properly)
        document.getElementById('ca-temp').innerText = (riskA.country && riskA.country.temperature !== null) ? `${riskA.country.temperature}°C, Wind: ${riskA.country.wind_speed}km/h` : 'No data';
        document.getElementById('cb-temp').innerText = (riskB.country && riskB.country.temperature !== null) ? `${riskB.country.temperature}°C, Wind: ${riskB.country.wind_speed}km/h` : 'No data';
        document.getElementById('ca-econ').innerText = (riskA.country && riskA.country.gdp !== null) ? `GDP: $${(riskA.country.gdp / 1e9).toFixed(1)}B, Inf: ${riskA.country.inflation}%` : 'No data';
        document.getElementById('cb-econ').innerText = (riskB.country && riskB.country.gdp !== null) ? `GDP: $${(riskB.country.gdp / 1e9).toFixed(1)}B, Inf: ${riskB.country.inflation}%` : 'No data';

        // Recommendation Logic
        const recBox = document.getElementById('recommendation-box');
        const recText = document.getElementById('recommendation-text');
        recBox.classList.remove('d-none');
        
        let scoreA = parseFloat(riskA.total_score);
        let scoreB = parseFloat(riskB.total_score);
        let nameA = riskA.country ? riskA.country.name : 'Country A';
        let nameB = riskB.country ? riskB.country.name : 'Country B';
        
        let wA = parseFloat(riskA.weather_risk); let wB = parseFloat(riskB.weather_risk);
        let eA = parseFloat(riskA.economic_risk); let eB = parseFloat(riskB.economic_risk);
        let sA = parseFloat(riskA.sentiment_risk); let sB = parseFloat(riskB.sentiment_risk);
        
        // Build detailed reasoning
        let bestCountry = scoreA < scoreB ? nameA : (scoreB < scoreA ? nameB : null);
        let bestScore = scoreA < scoreB ? scoreA : scoreB;
        let altName = scoreA < scoreB ? nameB : nameA;
        let altScore = scoreA < scoreB ? scoreB : scoreA;
        
        if (bestCountry) {
            let reasons = [];
            
            // Check weather
            if ((bestCountry === nameA && wA < wB) || (bestCountry === nameB && wB < wA)) {
                reasons.push('kondisi cuaca yang lebih baik');
            }
            // Check economic
            if ((bestCountry === nameA && eA < eB) || (bestCountry === nameB && eB < eA)) {
                reasons.push('stabilitas ekonomi yang lebih kuat');
            }
            // Check sentiment
            if ((bestCountry === nameA && sA < sB) || (bestCountry === nameB && sB < sA)) {
                reasons.push('sentimen berita yang lebih positif');
            }
            
            let reasonText = "";
            if (reasons.length > 0) {
                reasonText = " Hal ini didorong oleh " + reasons.join(' dan ') + " dibandingkan dengan " + altName + ".";
            }
            
            // Highlight trade-offs (where the alternative country is actually better)
            let tradeoff = "";
            let tradeReasons = [];
            if ((bestCountry === nameA && wA > wB) || (bestCountry === nameB && wB > wA)) tradeReasons.push('cuaca');
            if ((bestCountry === nameA && eA > eB) || (bestCountry === nameB && eB > eA)) tradeReasons.push('ekonomi');
            if ((bestCountry === nameA && sA > sB) || (bestCountry === nameB && sB > sA)) tradeReasons.push('sentimen berita');
            
            if (tradeReasons.length > 0) {
                tradeoff = ` <em>Namun perlu diperhatikan bahwa ${altName} sebenarnya memiliki keunggulan pada aspek ${tradeReasons.join(' dan ')}.</em>`;
            }

            recText.innerHTML = `<strong>${bestCountry}</strong> adalah pilihan yang direkomendasikan karena memiliki risiko keseluruhan yang lebih rendah (${bestScore}% vs ${altScore}%).${reasonText}${tradeoff}`;
            
            // Set style for clear recommendation
            recBox.style.backgroundColor = 'rgba(16, 185, 129, 0.1)';
            recBox.style.borderColor = '#10b981';
            recBox.style.color = '#10b981';
            recBox.innerHTML = `
                <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Rekomendasi: ${bestCountry}</h6>
                    <span id="recommendation-text" class="small">${recText.innerHTML}</span>
                </div>
            `;
        } else {
            recText.innerHTML = `Kedua negara memiliki skor risiko yang identik yaitu ${scoreA}%. Silakan evaluasi secara manual rincian metrik di bawah ini untuk membuat keputusan akhir.`;
            recBox.style.backgroundColor = 'rgba(245, 158, 11, 0.1)';
            recBox.style.borderColor = '#f59e0b';
            recBox.style.color = '#f59e0b';
            recBox.innerHTML = `
                <i class="fa-solid fa-circle-exclamation fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Rekomendasi: Seri</h6>
                    <span id="recommendation-text" class="small">${recText.innerHTML}</span>
                </div>
            `;
        }

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
                        backgroundColor: 'rgba(169, 132, 103, 0.2)',
                        borderColor: '#a98467',
                        pointBackgroundColor: '#a98467',
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
                        angleLines: { color: 'rgba(0, 0, 0, 0.1)' },
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
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

    async function syncMetrics() {
        const btn = document.getElementById('syncMetricsBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Getting countries...';
        btn.disabled = true;

        try {
            // First, get list of active countries
            const cRes = await fetch('/api/countries');
            const countries = await cRes.json();
            
            if (countries.length === 0) {
                alert("No active countries to sync.");
                btn.innerHTML = '<i class="fa-solid fa-rotate me-1"></i> Sync Metrics';
                btn.disabled = false;
                return;
            }

            // Sync per country sequentially
            for (let i = 0; i < countries.length; i++) {
                const c = countries[i];
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> Syncing ${c.name} (${i+1}/${countries.length})`;
                await fetch('/api/sync-metrics?country_id=' + c.id, { method: 'POST' });
            }
            
            btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Done!';
            alert("Weather and Economic data synced successfully.");
            location.reload(); 
        } catch (e) {
            console.error(e);
            alert('Failed to sync metrics');
            btn.innerHTML = '<i class="fa-solid fa-rotate me-1"></i> Sync Metrics';
            btn.disabled = false;
        }
    }

    // Init App
    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        fetchDashboardData();
    });
</script>
@endpush
