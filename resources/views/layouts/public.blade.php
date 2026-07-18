<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- TomSelect CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Tailwind Slate 900 */
            color: #e2e8f0;
        }
        .navbar {
            background-color: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #334155;
        }
        .card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 1rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        #map {
            height: 550px;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1; /* Keep map below dropdowns */
        }
        
        .pulse {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(56, 189, 248, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
        }
        
        .bg-glow-1 {
            position: fixed; top: -10%; left: -10%; width: 40vw; height: 40vw;
            background: radial-gradient(circle, rgba(56,189,248,0.15) 0%, rgba(15,23,42,0) 70%);
            border-radius: 50%; filter: blur(60px); z-index: -1;
        }
        .bg-glow-2 {
            position: fixed; bottom: -10%; right: -10%; width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, rgba(15,23,42,0) 70%);
            border-radius: 50%; filter: blur(60px); z-index: -1;
        }
    </style>
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>
    
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <div class="sidebar border-end border-secondary p-3 d-flex flex-column" style="width: 260px; background-color: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px);">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 mb-5 mt-2 text-decoration-none" href="/">
                <i class="fa-solid fa-globe text-info fs-3"></i>
                <div class="d-flex flex-column text-white">
                    <span class="lh-1 fs-5">OmniChain</span>
                    <span class="text-info fw-normal opacity-75" style="font-size: 0.65rem; letter-spacing: 1px;">RISK INTELLIGENCE</span>
                </div>
            </a>
            
            <ul class="nav nav-pills flex-column mb-auto gap-2">
                <li class="nav-item">
                    <a href="/" class="nav-link text-white {{ request()->is('/') ? 'active bg-primary' : 'opacity-75 hover-opacity-100' }}">
                        <i class="fa-solid fa-chart-line me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/intelligence" class="nav-link text-white {{ request()->is('intelligence') ? 'active bg-primary' : 'opacity-75 hover-opacity-100' }}">
                        <i class="fa-solid fa-newspaper me-2"></i> Intelligence News
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/countries" class="nav-link text-white {{ request()->is('countries') || request()->is('country*') ? 'active bg-primary' : 'opacity-75 hover-opacity-100' }}">
                        <i class="fa-solid fa-flag text-warning me-2"></i> Country Profiles
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/countries/settings" class="nav-link text-white {{ request()->is('countries/settings') ? 'active bg-primary' : 'opacity-75 hover-opacity-100' }} ps-4 small">
                        <i class="fa-solid fa-gear me-2"></i> Active Countries Settings
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto; background-color: #0f172a;">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- TomSelect JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    @stack('scripts')
</body>
</html>
