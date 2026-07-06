@extends('layouts.public')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center bg-dark p-3 rounded border border-secondary shadow-sm">
            <h4 class="mb-0 fw-bold"><i class="fa-solid fa-shield-halved text-warning me-2"></i> Admin Command Center</h4>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Logged in as <strong class="text-info">{{ auth()->user()->name }}</strong></span>
                <form action="/logout" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0"><i class="fa-solid fa-users text-info me-2"></i> System Users</h5>
                <button class="btn btn-sm btn-info" disabled>Add User</button>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" disabled>Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0"><i class="fa-solid fa-anchor text-info me-2"></i> Port Management</h5>
                <button class="btn btn-sm btn-info" disabled>Add Port</button>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Country</th>
                            <th>Coordinates</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ports as $port)
                        <tr>
                            <td>{{ $port->name }}</td>
                            <td>{{ $port->country->name ?? '-' }}</td>
                            <td class="small text-muted">{{ $port->latitude }}, {{ $port->longitude }}</td>
                            <td><span class="badge bg-success">{{ $port->status }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No ports configured yet. Run the API fetch command.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
