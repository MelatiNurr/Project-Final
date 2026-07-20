<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OmniChain Risk Intelligence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
        background-color: #fdf8f5;
        font-family: 'Inter', sans-serif;
    }
    
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #f3e8e0 0%, #e8d5c4 100%);
        z-index: 1;
    }

    .login-container {
        position: relative;
        z-index: 2;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 24px;
        padding: 3rem;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 25px 50px -12px rgba(107, 85, 75, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -12px rgba(107, 85, 75, 0.2);
    }

    .brand-logo {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #d4a373 0%, #a98467 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 20px rgba(169, 132, 103, 0.25);
    }

    .brand-logo i {
        font-size: 28px;
        color: white;
    }

    .login-title {
        color: #4a3b32;
        font-weight: 700;
        font-size: 1.75rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .login-subtitle {
        color: #8c7a6b;
        text-align: center;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .form-control-glass {
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(169, 132, 103, 0.3);
        color: #4a3b32;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .form-control-glass::placeholder {
        color: #b5a496;
    }

    .form-control-glass:focus {
        background: #ffffff;
        border-color: #d4a373;
        box-shadow: 0 0 0 4px rgba(212, 163, 115, 0.2);
        color: #4a3b32;
    }

    .input-group-custom {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #b5a496;
        z-index: 4;
        transition: color 0.3s ease;
    }

    .form-control-glass:focus + .input-icon {
        color: #a98467;
    }

    .btn-login {
        background: linear-gradient(135deg, #d4a373 0%, #a98467 100%);
        color: white;
        border: none;
        padding: 0.75rem;
        border-radius: 12px;
        font-weight: 600;
        width: 100%;
        margin-top: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(169, 132, 103, 0.3);
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #c7925e 0%, #997457 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(169, 132, 103, 0.4);
    }

    .back-link {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-block;
        margin-top: 1.5rem;
        transition: color 0.3s ease;
    }

    .back-link:hover {
        color: #f8fafc;
    }

    .alert-glass {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border-radius: 12px;
        padding: 0.75rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
</head>
<body>
<div class="overlay"></div>

<div class="login-container">
    <div class="glass-card">
        <div class="brand-logo">
            <i class="fa-solid fa-earth-americas"></i>
        </div>
        <h2 class="login-title">Welcome Back</h2>
        <p class="login-subtitle">Sign in to the Intelligence Platform</p>
        
        @if($errors->any())
            <div class="alert-glass">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="/">
            @csrf
            
            <div class="input-group-custom">
                <input type="text" name="username" class="form-control form-control-glass" placeholder="Username" required autofocus value="{{ old('username') }}">
                <i class="fa-solid fa-user input-icon"></i>
            </div>
            
            <div class="input-group-custom">
                <input type="password" name="password" class="form-control form-control-glass" placeholder="Password" required>
                <i class="fa-solid fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn btn-login">
                Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>
</body>
</html>
