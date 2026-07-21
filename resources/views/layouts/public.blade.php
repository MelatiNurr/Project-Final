<!DOCTYPE html>
<html lang="en">
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
            background-color: #fdf8f5;
            color: #4a3b32;
        }
        .navbar {
            background-color: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #334155;
        }
        .card, .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(169, 132, 103, 0.2);
            box-shadow: 0 10px 25px -5px rgba(107, 85, 75, 0.1);
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
            background: linear-gradient(135deg, #d4a373, #a98467);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        #map {
            height: 550px;
            border-radius: 1rem;
            border: 1px solid rgba(169, 132, 103, 0.2);
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
            background: radial-gradient(circle, rgba(212, 163, 115, 0.2) 0%, rgba(253, 248, 245, 0) 70%);
            border-radius: 50%; filter: blur(60px); z-index: -1;
        }
        .bg-glow-2 {
            position: fixed; bottom: -10%; right: -10%; width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(169, 132, 103, 0.15) 0%, rgba(253, 248, 245, 0) 70%);
            border-radius: 50%; filter: blur(60px); z-index: -1;
        }
        
        /* Premium Sidebar Styles */
        .sidebar-modern {
            width: 280px;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(169, 132, 103, 0.3);
            box-shadow: 5px 0 25px rgba(107, 85, 75, 0.05);
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.25rem;
            color: #8c7a6b;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-link:hover {
            color: #4a3b32;
            background: rgba(212, 163, 115, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 12px;
            transition: color 0.3s ease;
        }
        
        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(212, 163, 115, 0.15), rgba(169, 132, 103, 0.15));
            color: #a98467;
            border: 1px solid rgba(169, 132, 103, 0.2);
            box-shadow: 0 4px 12px rgba(107, 85, 75, 0.05);
        }
        
        .sidebar-link.active i {
            color: #a98467;
        }
        
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 4px;
            background: #a98467;
            border-radius: 0 4px 4px 0;
        }

        /* Custom Coksu Theme Buttons */
        .btn-outline-coksu {
            color: #a98467;
            border-color: #a98467;
            transition: all 0.3s ease;
        }
        .btn-outline-coksu:hover {
            color: #fff;
            background-color: #a98467;
            border-color: #a98467;
        }
        .bg-coksu {
            background-color: #a98467 !important;
            color: #fff !important;
        }
        .bg-coksu:hover {
            background-color: #8c6a4e !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>
    
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <div class="sidebar-modern p-4 d-flex flex-column z-3">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-3 mb-5 text-decoration-none" href="/dashboard">
                <div class="p-2 rounded-3" style="background: linear-gradient(135deg, #d4a373, #a98467); box-shadow: 0 4px 15px rgba(169, 132, 103, 0.3);">
                    <i class="fa-solid fa-globe text-white fs-4"></i>
                </div>
                <div class="d-flex flex-column" style="color: #4a3b32;">
                    <span class="lh-1 fs-5 fw-bold" style="letter-spacing: 0.5px;">OmniChain</span>
                    <span class="fw-semibold mt-1" style="font-size: 0.65rem; letter-spacing: 1.5px; color: #a98467;">RISK INTELLIGENCE</span>
                </div>
            </a>
            
            <div class="text-uppercase fw-bold mb-3 ms-2" style="font-size: 0.7rem; letter-spacing: 1px; color: #8c7a6b;">Main Menu</div>
            <div class="d-flex flex-column mb-auto">
                <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Global Dashboard
                </a>
                
                <a href="/intelligence" class="sidebar-link {{ request()->is('intelligence') ? 'active' : '' }}">
                    <i class="fa-solid fa-newspaper"></i> Intelligence Feed
                </a>
                
                <a href="/visualization" class="sidebar-link {{ request()->is('visualization') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Data Visualization
                </a>
                
                <a href="/countries" class="sidebar-link {{ request()->is('countries') || request()->is('country*') ? 'active' : '' }}">
                    <i class="fa-solid fa-flag"></i> Country Profiles
                </a>
                
                <a href="/watchlist" class="sidebar-link {{ request()->is('watchlist') ? 'active' : '' }}">
                    <i class="fa-solid fa-star"></i> My Watchlist
                </a>
            </div>
            
            <div class="mt-auto pt-4 border-top" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="/admin" class="btn w-100 fw-bold mb-3 py-2 d-flex justify-content-center align-items-center gap-2" style="color: #4a3b32; background: rgba(169, 132, 103, 0.15); border: 1px solid rgba(169, 132, 103, 0.4); border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(169, 132, 103, 0.3)'" onmouseout="this.style.background='rgba(169, 132, 103, 0.15)'">
                        <i class="fa-solid fa-crown text-warning"></i> Admin Panel
                    </a>
                @endif
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn w-100 fw-bold py-2 text-danger d-flex justify-content-center align-items-center gap-2" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'">
                        <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto;">
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
