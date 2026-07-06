@extends('layouts.public')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card p-4">
            <h4 class="card-title fw-bold text-center mb-4"><i class="fa-solid fa-lock text-warning"></i> Admin Portal</h4>
            
            <form method="POST" action="/login">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted">Email address</label>
                    <input type="email" name="email" class="form-control bg-dark text-light border-secondary" required autofocus value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-muted">Password</label>
                    <input type="password" name="password" class="form-control bg-dark text-light border-secondary" required>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold">Secure Login</button>
            </form>
            <div class="text-center mt-3">
                <a href="/" class="text-secondary small text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Back to public dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
