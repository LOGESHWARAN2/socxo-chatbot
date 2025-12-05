@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #FAF5F2;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
            max-width: 450px;
            margin: 0 auto;
        }

        .logo-text {
            color: #1a3c5e;
            font-weight: 900;
            font-size: 40px;
            letter-spacing: -2px;
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-text span {
            color: #E85D24;
        }

        .form-control {
            border: 1px solid #E85D24;
            border-radius: 8px;
            padding: 12px 15px;
            padding-left: 45px;
        }

        .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #000;
            font-size: 1.2rem;
        }

        .btn-primary {
            background-color: #E85D24;
            border-color: #E85D24;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            font-size: 18px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #d14d19;
            border-color: #d14d19;
        }

        .footer-text {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin-top: 20px;
            line-height: 1.5;
        }

        .copyright {
            font-size: 12px;
            color: #333;
            text-align: center;
            margin-top: 20px;
        }

        .login-header {
            text-align: center;
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 30px;
            color: #333;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }
    </style>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <img src="{{ asset('img/socxo.png') }}" alt="Socxo Logo" style="height: 60px;">
        </div>

        <div class="login-card">
            <div class="login-header">
                Sign up or Login with
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-wrapper">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="Email Address">
                    @error('email')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="input-wrapper">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" placeholder="Password">
                    @error('password')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mb-3">
                    Continue
                </button>

                <div class="d-flex justify-content-between mb-3">
                    <a class="text-decoration-none" href="{{ route('register') }}"
                        style="color: #E85D24; font-size: 14px;">Register</a>
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none" href="{{ route('password.request') }}"
                            style="color: #666; font-size: 14px;">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <div class="footer-text">
                    By Signing Up/Signing in, you agree to our Terms and<br>Conditions and Privacy Policy.
                </div>

                <div class="copyright">
                    &copy; 2025 Socxo. All rights reserved.
                </div>
            </form>
        </div>
    </div>

    <!-- Add Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection
