<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - OmniChain Risk Intelligence</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #fdf8f5;
            font-family: 'Inter', sans-serif;
            color: #4a3b32;
            min-height: 100vh;
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

        .admin-sidebar {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(169, 132, 103, 0.3);
            width: 260px;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(169, 132, 103, 0.2);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(107, 85, 75, 0.1);
        }

        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(107, 85, 75, 0.15);
        }

        .text-coksu { color: #a98467; }
        .bg-coksu {
            background: linear-gradient(135deg, #d4a373 0%, #a98467 100%);
            color: white; border: none;
        }
        .bg-coksu:hover {
            background: linear-gradient(135deg, #c7925e 0%, #997457 100%);
            color: white;
        }
        .btn-outline-coksu { color: #a98467; border-color: #a98467; }
        .btn-outline-coksu:hover { background-color: #a98467; color: white; }

        .nav-tabs .nav-link {
            color: #8c7a6b; border: none; border-bottom: 2px solid transparent; font-weight: 600;
        }
        .nav-tabs .nav-link:hover { border-color: transparent; color: #4a3b32; }
        .nav-tabs .nav-link.active {
            color: #a98467; background: transparent; border-color: transparent; border-bottom: 2px solid #a98467;
        }

        .table-custom th { border-bottom-color: rgba(169, 132, 103, 0.3); color: #8c7a6b; font-weight: 600; }
        .table-custom td { border-bottom-color: rgba(169, 132, 103, 0.1); color: #4a3b32; }
        
        .modal-content {
            background: rgba(253, 248, 245, 0.95); backdrop-filter: blur(15px);
            border: 1px solid rgba(169, 132, 103, 0.3); border-radius: 16px;
        }
        .modal-header, .modal-footer { border-color: rgba(169, 132, 103, 0.2); }
        .form-control-glass {
            background: rgba(255, 255, 255, 0.7); border: 1px solid rgba(169, 132, 103, 0.3);
            color: #4a3b32; border-radius: 8px;
        }
        .form-control-glass:focus {
            background: #ffffff; border-color: #d4a373; box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.2);
        }
    </style>
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="d-flex" style="min-height: 100vh;">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar p-3 d-flex flex-column">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 mb-5 mt-2 text-decoration-none" href="#">
                <div style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;" class="bg-coksu">
                    <i class="fa-solid fa-crown text-white"></i>
                </div>
                <div class="d-flex flex-column text-dark">
                    <span class="lh-1 fs-5 text-coksu">Admin Portal</span>
                    <span class="fw-normal text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">OMNICHAIN</span>
                </div>
            </a>
            
            <ul class="nav flex-column mb-auto gap-2" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link w-100 text-start active d-flex align-items-center" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" style="border-radius: 8px;">
                        <i class="fa-solid fa-chart-pie me-3" style="width: 20px;"></i> Dashboard
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link w-100 text-start d-flex align-items-center" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" style="border-radius: 8px;">
                        <i class="fa-solid fa-users me-3" style="width: 20px;"></i> Manage Users
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link w-100 text-start d-flex align-items-center" id="countries-tab" data-bs-toggle="tab" data-bs-target="#countries" type="button" role="tab" style="border-radius: 8px;">
                        <i class="fa-solid fa-flag me-3" style="width: 20px;"></i> Monitored Countries
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link w-100 text-start d-flex align-items-center" id="data-tab" data-bs-toggle="tab" data-bs-target="#data" type="button" role="tab" style="border-radius: 8px;">
                        <i class="fa-solid fa-database me-3" style="width: 20px;"></i> Data & Sync
                    </button>
                </li>
            </ul>
            
            <div class="mt-auto border-top pt-3" style="border-color: rgba(169, 132, 103, 0.3) !important;">
                <form method="POST" action="/logout" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out</button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0 text-coksu">System Administration</h4>
                <span class="badge bg-white text-coksu border shadow-sm px-3 py-2" style="border-color: rgba(169, 132, 103, 0.3) !important;"><i class="fa-solid fa-user-shield me-2"></i> {{ auth()->user()->name }}</span>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="background: rgba(16, 185, 129, 0.1); color: #065f46; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px;">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="background: rgba(239, 68, 68, 0.1); color: #991b1b; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Tabs Content -->
            <div class="tab-content" id="adminTabsContent">
                
                <!-- OVERVIEW TAB -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="glass-card stat-card p-4 text-center h-100" style="border-top: 4px solid #a98467;">
                                <h1 class="text-coksu fw-bold mb-1">{{ $users->count() }}</h1>
                                <span class="text-muted small text-uppercase fw-semibold">Total Users</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="glass-card stat-card p-4 text-center h-100" style="border-top: 4px solid #10b981;">
                                <h1 class="text-success fw-bold mb-1">{{ $countries->where('is_active', true)->count() }}</h1>
                                <span class="text-muted small text-uppercase fw-semibold">Active Monitored</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="glass-card stat-card p-4 text-center h-100" style="border-top: 4px solid #f59e0b;">
                                <h1 class="text-warning fw-bold mb-1">{{ $ports->count() }}</h1>
                                <span class="text-muted small text-uppercase fw-semibold">Ports Logged</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="glass-card stat-card p-4 text-center h-100" style="border-top: 4px solid #0ea5e9;">
                                <h1 class="text-info fw-bold mb-1">{{ $articles->count() }}</h1>
                                <span class="text-muted small text-uppercase fw-semibold">Intel Articles</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- USERS TAB -->
                <div class="tab-pane fade" id="users" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0 text-coksu"><i class="fa-solid fa-users text-coksu me-2"></i> System Users</h5>
                            <button class="btn btn-sm bg-coksu shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-plus me-1"></i> Add User</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-custom text-center align-middle bg-transparent">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="text-muted">#{{ $user->id }}</td>
                                        <td class="fw-semibold">{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>
                                            <span class="badge rounded-pill {{ $user->role === 'admin' ? 'bg-danger' : 'bg-secondary' }}" style="font-weight: 500;">{{ ucfirst($user->role) }}</span>
                                        </td>
                                        <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                            @else
                                            <span class="text-muted small fst-italic">You</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- COUNTRIES TAB -->
                <div class="tab-pane fade" id="countries" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <h5 class="fw-bold mb-0 text-coksu"><i class="fa-solid fa-flag text-coksu me-2"></i> Tracked Countries</h5>
                                <input type="text" id="countrySearch" class="form-control form-control-glass form-control-sm" placeholder="Search countries..." style="width: 200px;">
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success">{{ $countries->where('is_active', true)->count() }} Active</span>
                                <form action="{{ route('admin.countries.bulk') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="activate">
                                    <button type="submit" class="btn btn-sm btn-outline-success shadow-sm" onclick="return confirm('Activate ALL countries? This will fetch a massive amount of data on the next sync.')">Activate All</button>
                                </form>
                                <form action="{{ route('admin.countries.bulk') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="deactivate">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary shadow-sm" onclick="return confirm('Deactivate ALL countries?')">Deactivate All</button>
                                </form>
                                <button class="btn btn-sm btn-success shadow-sm ms-2" data-bs-toggle="modal" data-bs-target="#addCountryModal"><i class="fa-solid fa-plus me-1"></i> Custom Country</button>
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                            <table class="table table-hover table-custom text-center align-middle bg-transparent">
                                <thead class="sticky-top bg-white" style="z-index: 10;">
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Region</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countries as $country)
                                    <tr class="country-row">
                                        <td><span class="badge bg-light text-dark border country-code">{{ $country->code }}</span></td>
                                        <td class="fw-bold text-start country-name">{{ $country->name }}</td>
                                        <td class="country-region">{{ $country->region }}</td>
                                        <td>
                                            <form action="{{ route('admin.countries.toggle') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="country_id" value="{{ $country->id }}">
                                                <button type="submit" class="badge rounded-pill border-0 px-3 {{ $country->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-weight: 500;">
                                                    {{ $country->is_active ? 'Active' : 'Disabled' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- DATA MANAGEMENT TAB -->
                <div class="tab-pane fade" id="data" role="tabpanel">
                    <div class="glass-card p-4 mb-4 border-primary" style="background: rgba(14, 165, 233, 0.05);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold text-primary mb-1"><i class="fa-solid fa-cloud-arrow-down me-2"></i> Global Data Sync</h5>
                                <p class="text-muted small mb-0">Pull the latest Intelligence News and Port Data for all ACTIVE countries.</p>
                            </div>
                            <button class="btn btn-primary fw-bold shadow px-4 py-2" id="btn-sync-all" onclick="syncAllData()">
                                <i class="fa-solid fa-rotate me-2"></i> Sync News & Ports
                            </button>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="glass-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0 text-coksu"><i class="fa-solid fa-anchor me-2"></i> Monitored Ports</h5>
                                    <button class="btn btn-sm btn-outline-coksu" data-bs-toggle="modal" data-bs-target="#addPortModal"><i class="fa-solid fa-plus"></i></button>
                                </div>
                                
                                @if($ports->isEmpty())
                                    <div class="text-center text-muted p-3">
                                        <i class="fa-solid fa-folder-open fs-1 mb-2 opacity-50"></i>
                                        <p class="small">No ports available. Click Sync to fetch.</p>
                                    </div>
                                @else
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-custom text-center align-middle bg-transparent">
                                        <thead class="sticky-top bg-white">
                                            <tr>
                                                <th>Port Name</th>
                                                <th>Country</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ports as $port)
                                            <tr>
                                                <td class="fw-medium text-start">{{ $port->name }}</td>
                                                <td class="text-muted small">{{ $port->country->name ?? 'Unknown' }}</td>
                                                <td>
                                                    <form action="{{ route('admin.ports.destroy', $port) }}" method="POST" onsubmit="return confirm('Delete port?');">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm text-danger border-0 py-0"><i class="fa-solid fa-xmark"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="glass-card p-4 h-100">
                                <h5 class="fw-bold mb-4 text-coksu"><i class="fa-solid fa-newspaper me-2"></i> Intelligence Articles</h5>
                                
                                @if($articles->isEmpty())
                                    <div class="text-center text-muted p-3">
                                        <i class="fa-solid fa-newspaper fs-1 mb-2 opacity-50"></i>
                                        <p class="small">No articles available. Click Sync to fetch.</p>
                                    </div>
                                @else
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-custom text-start align-middle bg-transparent">
                                        <thead class="sticky-top bg-white">
                                            <tr>
                                                <th>Title</th>
                                                <th class="text-center">Score</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($articles as $article)
                                            <tr>
                                                <td class="small" title="{{ $article->title }}">
                                                    <div class="text-truncate" style="max-width: 200px;">
                                                        <span class="fw-medium">{{ $article->title }}</span>
                                                    </div>
                                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $article->country->code ?? 'N/A' }} | {{ $article->source }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($article->sentiment_score > 0)
                                                        <span class="text-success fw-bold">{{ $article->sentiment_score }}</span>
                                                    @elseif($article->sentiment_score < 0)
                                                        <span class="text-danger fw-bold">{{ $article->sentiment_score }}</span>
                                                    @else
                                                        <span class="text-muted fw-bold">{{ $article->sentiment_score }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Delete article?');">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm text-danger border-0 py-0"><i class="fa-solid fa-xmark"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-coksu">Create New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-glass" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control form-control-glass" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control form-control-glass" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" class="form-select form-control-glass">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn bg-coksu">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Country Modal -->
    <div class="modal fade" id="addCountryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.countries.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-success">Add Monitored Country</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Country Name</label>
                            <input type="text" name="name" class="form-control form-control-glass" placeholder="e.g. Japan" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">ISO Code (2-letter)</label>
                                <input type="text" name="code" class="form-control form-control-glass" placeholder="e.g. JP" maxlength="2" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Region</label>
                                <input type="text" name="region" class="form-control form-control-glass" placeholder="e.g. Asia" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Capital Latitude</label>
                                <input type="number" step="any" name="latitude" class="form-control form-control-glass" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Capital Longitude</label>
                                <input type="number" step="any" name="longitude" class="form-control form-control-glass" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Country</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Port Modal -->
    <div class="modal fade" id="addPortModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.ports.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-coksu">Add Custom Port</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Country</label>
                            <select name="country_id" class="form-select form-control-glass" required>
                                @foreach($countries->where('is_active', true) as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Port Name</label>
                            <input type="text" name="name" class="form-control form-control-glass" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Latitude</label>
                                <input type="number" step="any" name="latitude" class="form-control form-control-glass" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Longitude</label>
                                <input type="number" step="any" name="longitude" class="form-control form-control-glass" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn bg-coksu">Add Port</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search Filter for Countries
        document.getElementById('countrySearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.country-row');
            
            rows.forEach(row => {
                let name = row.querySelector('.country-name').textContent.toLowerCase();
                let code = row.querySelector('.country-code').textContent.toLowerCase();
                let region = row.querySelector('.country-region').textContent.toLowerCase();
                
                if (name.includes(filter) || code.includes(filter) || region.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Remember active tab
        document.addEventListener('DOMContentLoaded', function() {
            var triggerTabList = [].slice.call(document.querySelectorAll('#adminTabs button'))
            triggerTabList.forEach(function(triggerEl) {
                triggerEl.addEventListener('shown.bs.tab', function(event) {
                    sessionStorage.setItem('activeAdminTab', event.target.id);
                });
            });

            var activeTabId = sessionStorage.getItem('activeAdminTab');
            if (activeTabId) {
                var activeTab = new bootstrap.Tab(document.getElementById(activeTabId));
                activeTab.show();
            }
        });

        async function syncAllData() {
            const btn = document.getElementById('btn-sync-all');
            
            if (!confirm('This process will fetch data sequentially for all ACTIVE countries and may take a few minutes. Continue?')) {
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Initializing...';

            try {
                // Fetch active countries
                const res = await fetch('/api/countries');
                const countries = await res.json();

                if (countries.length === 0) {
                    alert('No active countries to sync.');
                    btn.innerHTML = '<i class="fa-solid fa-rotate me-2"></i> Sync News & Ports';
                    btn.disabled = false;
                    return;
                }

                // Loop sequentially
                for (let i = 0; i < countries.length; i++) {
                    const c = countries[i];
                    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Syncing ${c.name} (${i + 1}/${countries.length})`;
                    
                    // Fire both metrics (which includes Ports) and news for this country
                    await Promise.all([
                        fetch(`/api/sync-metrics?country_id=${c.id}`, { method: 'POST' }),
                        fetch(`/api/sync-news?country_id=${c.id}`, { method: 'POST' })
                    ]);
                    
                    // Small delay to respect API limits
                    await new Promise(r => setTimeout(r, 1000));
                }

                btn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Sync Complete!';
                setTimeout(() => {
                    alert('Sync completed successfully!');
                    location.reload();
                }, 500);

            } catch (error) {
                console.error(error);
                alert('An error occurred during sync. Check console for details.');
                btn.innerHTML = '<i class="fa-solid fa-rotate me-2"></i> Sync News & Ports';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
