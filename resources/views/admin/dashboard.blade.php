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
        
        @if(session('success'))
            <div class="alert alert-success mt-3 mb-0 border-success" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mt-3 mb-0 border-danger" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Article Management -->
    <div class="col-md-6">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0"><i class="fa-solid fa-newspaper text-info me-2"></i> Article Management</h5>
                <span class="badge bg-secondary">{{ count($articles) }} Articles</span>
            </div>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-dark table-hover align-middle">
                    <thead class="sticky-top bg-dark">
                        <tr>
                            <th>Title</th>
                            <th>Country</th>
                            <th>Sent.</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                        <tr>
                            <td class="small">{{ Str::limit($article->title, 40) }}</td>
                            <td>{{ $article->country->name ?? '-' }}</td>
                            <td>
                                @if($article->sentiment_score > 0)
                                    <span class="text-success"><i class="fa-solid fa-arrow-up"></i></span>
                                @elseif($article->sentiment_score < 0)
                                    <span class="text-danger"><i class="fa-solid fa-arrow-down"></i></span>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No articles found. Run sync.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Port Management -->
    <div class="col-md-6">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0"><i class="fa-solid fa-anchor text-info me-2"></i> Port Management</h5>
                <button class="btn btn-sm btn-info fw-bold" data-bs-toggle="modal" data-bs-target="#addPortModal"><i class="fa-solid fa-plus"></i> Add Port</button>
            </div>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-dark table-hover align-middle">
                    <thead class="sticky-top bg-dark">
                        <tr>
                            <th>Name</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ports as $port)
                        <tr>
                            <td>{{ $port->name }}</td>
                            <td>{{ $port->country->name ?? '-' }}</td>
                            <td><span class="badge bg-success">{{ $port->status }}</span></td>
                            <td>
                                <form action="{{ route('admin.ports.destroy', $port->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this port?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No ports configured yet. Run sync.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Port Modal -->
<div class="modal fade" id="addPortModal" tabindex="-1" aria-labelledby="addPortModalLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content bg-dark border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title" id="addPortModalLabel">Add Manual Port</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.ports.store') }}" method="POST">
          @csrf
          <div class="modal-body">
              <div class="mb-3">
                  <label class="form-label text-muted">Country</label>
                  <select name="country_id" class="form-select bg-dark text-light border-secondary" required>
                      <option value="">Select Country...</option>
                      @foreach($countries as $c)
                          <option value="{{ $c->id }}">{{ $c->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label text-muted">Port Name</label>
                  <input type="text" name="name" class="form-control bg-dark text-light border-secondary" required>
              </div>
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label class="form-label text-muted">Latitude</label>
                      <input type="number" step="any" name="latitude" class="form-control bg-dark text-light border-secondary" required>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="form-label text-muted">Longitude</label>
                      <input type="number" step="any" name="longitude" class="form-control bg-dark text-light border-secondary" required>
                  </div>
              </div>
          </div>
          <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-info">Save Port</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
