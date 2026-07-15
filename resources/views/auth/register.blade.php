<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Inventory Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #0F766E 0%, #14B8A6 50%, #0F766E 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .auth-header {
            padding: 40px 40px 0;
            text-align: center;
        }
        .auth-header .brand-icon {
            width: 64px;
            height: 64px;
            background: #0F766E;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
            margin-bottom: 16px;
        }
        .auth-header h3 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .auth-header p {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 0;
        }
        .auth-body {
            padding: 32px 40px 40px;
        }
        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #111827;
            margin-bottom: 6px;
        }
        .form-control {
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            padding: 12px 14px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: #0F766E;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }
        .form-control.is-invalid {
            border-color: #EF4444;
        }
        .btn-auth {
            width: 100%;
            padding: 12px;
            background: #0F766E;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-auth:hover {
            background: #0D6B64;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15, 118, 110, 0.3);
        }
        .auth-footer {
            text-align: center;
            padding: 0 40px 40px;
            font-size: 14px;
            color: #6B7280;
        }
        .auth-footer a {
            color: #0F766E;
            font-weight: 500;
        }
        .auth-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            border: none;
            border-radius: 10px;
            font-size: 13px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #DC2626;
        }
        .invalid-feedback {
            font-size: 12px;
            color: #EF4444;
            margin-top: 4px;
        }
        .input-group {
            position: relative;
        }
        .input-group .form-control {
            padding-left: 44px;
        }
        .input-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 16px;
            z-index: 4;
        }
        @media (max-width: 480px) {
            .auth-header { padding: 30px 24px 0; }
            .auth-body { padding: 24px; }
            .auth-footer { padding: 0 24px 30px; }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-icon">
                <i class="fas fa-cubes"></i>
            </div>
            <h3>Create Account</h3>
            <p>Register to start managing your inventory</p>
        </div>
        <div class="auth-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 characters" required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat your password" required>
                    </div>
                </div>
                <button type="submit" class="btn-auth">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>
        </div>
        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</body>
</html>
