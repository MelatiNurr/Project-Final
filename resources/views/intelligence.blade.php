@extends('layouts.public')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0" style="color: #4a3b32;"><i class="fa-solid fa-newspaper me-2" style="color: #a98467;"></i> Global Intelligence Feed</h4>
</div>

<div class="glass-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: #4a3b32;">Intelligence Filters</h5>
    </div>
    <div class="d-flex gap-2 flex-nowrap overflow-x-auto pb-2" id="country-filters" style="scrollbar-width: thin; scrollbar-color: #a98467 transparent;">
        <button class="btn btn-outline-coksu active text-nowrap" onclick="loadNews(null)">Global (All)</button>
        <!-- Country buttons injected here -->
    </div>
</div>

<div class="row g-4" id="news-grid">
    <div class="col-12 text-center text-muted py-5" id="news-loading">
        <i class="fa-solid fa-spinner fa-spin fs-2 mb-3"></i>
        <p>Intercepting latest intelligence...</p>
    </div>
    <!-- News cards injected here -->
</div>

@endsection

@push('scripts')
<script>
    let currentCountryFilter = null;
    let allCountries = [];

    async function loadCountries() {
        try {
            const res = await fetch('/api/countries');
            allCountries = await res.json();
            const filterContainer = document.getElementById('country-filters');
            
            allCountries.forEach(c => {
                const btn = document.createElement('button');
                btn.className = 'btn btn-outline-secondary text-nowrap';
                btn.innerText = c.name;
                btn.onclick = (e) => {
                    // Update active state
                    document.querySelectorAll('#country-filters .btn').forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    loadNews(c.id);
                };
                filterContainer.appendChild(btn);
            });
        } catch (e) {
            console.error('Failed to load countries');
        }
    }

    async function loadNews(countryId) {
        currentCountryFilter = countryId;
        const grid = document.getElementById('news-grid');
        grid.innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="fa-solid fa-spinner fa-spin fs-2 mb-3"></i><p>Loading intelligence...</p></div>';
        
        try {
            let url = '/api/news';
            if (countryId) url += `?country_id=${countryId}`;
            
            const res = await fetch(url);
            const articles = await res.json();
            
            if (articles.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center py-5 text-muted"><i class="fa-solid fa-satellite-dish fs-1 mb-3 opacity-50"></i><h5>No Intelligence Found</h5><p>No recent news intercepted for this region.</p></div>';
                return;
            }

            grid.innerHTML = '';
            articles.forEach(article => {
                // Determine sentiment color
                let badgeClass = 'bg-secondary';
                if (article.sentiment_score > 0) badgeClass = 'bg-success';
                else if (article.sentiment_score < 0) badgeClass = 'bg-danger';

                const card = `
                    <div class="col-md-4">
                        <div class="card glass-card h-100 overflow-hidden d-flex flex-column" style="border-top: 3px solid ${article.sentiment_score < 0 ? '#ef4444' : (article.sentiment_score > 0 ? '#10b981' : '#a98467')};">
                            <div class="card-body p-4 flex-grow-1">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="badge bg-white border text-dark shadow-sm" style="border-color: rgba(169, 132, 103, 0.3) !important;"><i class="fa-solid fa-location-dot me-1" style="color: #a98467;"></i> ${article.country ? article.country.name : 'Unknown'}</span>
                                    <span class="badge ${badgeClass} shadow-sm">Sentiment: ${article.sentiment_score}</span>
                                </div>
                                <h6 class="fw-bold lh-base" style="color: #4a3b32;">${article.title}</h6>
                                <p class="small text-secondary mb-0 mt-3"><i class="fa-solid fa-newspaper me-1"></i> Source: ${article.source || 'Unknown'}</p>
                            </div>
                            <div class="card-footer mt-auto bg-transparent border-top d-flex justify-content-between align-items-center py-3" style="border-color: rgba(169, 132, 103, 0.2) !important; position: relative; z-index: 10;">
                                <span class="small text-muted fw-medium"><i class="fa-regular fa-clock me-1"></i> ${new Date(article.published_at).toLocaleDateString()}</span>
                                <a href="${article.url}" target="_blank" class="btn btn-sm btn-outline-coksu" style="position: relative; z-index: 20;">Read Source</a>
                            </div>
                        </div>
                    </div>
                `;
                grid.innerHTML += card;
            });

            // Add Profile Link at the bottom if a specific country is selected
            if (countryId) {
                const countryData = allCountries.find(c => c.id === countryId);
                if (countryData) {
                    grid.innerHTML += `
                        <div class="col-12 mt-4 text-center">
                            <hr class="mb-4" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                            <a href="/country/${countryData.id}" class="btn btn-outline-coksu px-4 py-2 fw-bold"><i class="fa-solid fa-flag me-2"></i> View ${countryData.name}'s Full Profile</a>
                        </div>
                    `;
                }
            }

        } catch (e) {
            grid.innerHTML = '<div class="col-12 text-center text-danger py-5"><i class="fa-solid fa-triangle-exclamation fs-1 mb-3"></i><p>Failed to load intelligence feed.</p></div>';
        }
    }



    document.addEventListener('DOMContentLoaded', () => {
        loadCountries().then(() => loadNews(null));
    });
</script>
@endpush
