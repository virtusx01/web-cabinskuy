@extends('backend.user_layout')

{{-- Set judul halaman --}}
@section('title', 'Register - Cabinskuy')

{{-- Sisipkan CSS khusus untuk halaman ini --}}
@push('styles')
<style>
    body {
        background-color: #f8f9fa; /* Match the layout background or adjust as needed */
    }
    .main-content {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-grow: 1;
        width: 100%;
        min-height: calc(100vh - 120px); /* Adjust based on your header/footer height */
        padding: 30px 15px; /* Reduced padding */
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        box-sizing: border-box;
    }

    .form-container {
        background-color: #fff;
        padding: 30px 40px; /* Reduced padding */
        border-radius: 12px; /* Slightly smaller border-radius */
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); /* Adjusted shadow */
        width: 100%;
        max-width: 400px; /* Reduced max-width */
        position: relative;
        overflow: hidden;
        margin: 0 auto;
    }

    .form-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px; /* Slightly thinner line */
        background: linear-gradient(90deg, #229954, #27ae60);
    }

    .form-header {
        display: flex;
        align-items: center;
        margin-bottom: 25px; /* Reduced margin */
        padding-bottom: 15px; /* Reduced padding */
        border-bottom: 1px solid #f0f0f0;
    }

    .logo-container {
        margin-right: 15px; /* Reduced margin */
        text-align: center;
        flex-shrink: 0;
    }

    .logo-placeholder {
        width: 50px; /* Reduced size */
        height: 50px; /* Reduced size */
        border-radius: 10px; /* Adjusted border-radius */
        background-image: url('/backend/images/icon-cabinskuy.jpg');
        background-size: 50px 50px;
        background-repeat: no-repeat;
        background-position: center;
        box-shadow: 0 3px 10px rgba(34, 153, 84, 0.1); /* Adjusted shadow */
    }

    .logo-text {
        font-size: 0.65em; /* Slightly smaller font */
        color: #229954;
        margin-top: 6px; /* Adjusted margin */
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .title-container h1 {
        font-size: 1.4em; /* Reduced font size */
        color: #2c3e50;
        margin: 0 0 4px 0; /* Adjusted margin */
        font-weight: 700;
        line-height: 1.2;
    }

    .title-container p {
        font-size: 0.85em; /* Reduced font size */
        color: #7f8c8d;
        margin: 0;
        line-height: 1.4;
    }

    .form-group {
        margin-bottom: 20px; /* Reduced margin */
    }
    
    .label-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px; /* Reduced margin */
    }

    .label-group label {
        font-weight: 600;
        font-size: 0.85em; /* Reduced font size */
        color: #34495e;
        letter-spacing: 0.3px;
    }

    .form-control {
        width: 100%;
        padding: 11px 14px; /* Reduced padding */
        border: 1px solid #e1e8ed; /* Thinner border */
        border-radius: 8px; /* Slightly smaller border-radius */
        box-sizing: border-box;
        font-size: 0.9em; /* Reduced font size */
        transition: all 0.3s ease;
        background-color: #fafbfc;
    }

    .form-control::placeholder {
        color: #95a5a6;
        font-weight: 400;
    }

    .form-control:focus {
        border-color: #229954;
        outline: none;
        box-shadow: 0 0 0 3px rgba(34, 153, 84, 0.1); /* Adjusted shadow */
        background-color: #fff;
        transform: translateY(-1px);
    }

    .input-error-message {
        color: #e74c3c;
        font-size: 0.75em; /* Reduced font size */
        margin-top: 6px; /* Adjusted margin */
        display: block;
        font-weight: 500;
    }

    .is-invalid {
        border-color: #e74c3c !important;
        background-color: #fdf2f2 !important;
    }
    
    .is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1); /* Adjusted shadow */
    }

    .btn-submit {
        background: linear-gradient(135deg, #229954, #27ae60);
        color: white;
        padding: 13px 18px; /* Reduced padding */
        border: none;
        border-radius: 8px; /* Adjusted border-radius */
        cursor: pointer;
        font-size: 0.95em; /* Reduced font size */
        font-weight: 600;
        width: 100%;
        height: 50px; /* Reduced height */
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(34, 153, 84, 0.15); /* Adjusted shadow */
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #1e7e34, #229954);
        transform: translateY(-1px); /* Slightly reduced transform */
        box-shadow: 0 4px 15px rgba(34, 153, 84, 0.2); /* Adjusted shadow */
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .btn-google-login {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 50px; /* Reduced height */
        padding: 13px 18px; /* Reduced padding */
        border-radius: 8px; /* Adjusted border-radius */
        cursor: pointer;
        font-size: 0.95em; /* Reduced font size */
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        background-color: #ffffff;
        color: #34495e;
        border: 1px solid #e1e8ed; /* Thinner border */
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.04); /* Adjusted shadow */
        box-sizing: border-box;
    }
    
    .btn-google-login img {
        margin-right: 10px; /* Reduced margin */
        height: 18px; /* Reduced size */
        width: 18px; /* Reduced size */
        transition: transform 0.3s ease;
        flex-shrink: 0;
    }
    
    .btn-google-login span {
        white-space: nowrap;
    }
    
    .btn-google-login:hover {
        background-color: #f8f9fa;
        border-color: #229954;
        transform: translateY(-1px); /* Slightly reduced transform */
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08); /* Adjusted shadow */
    }

    .btn-google-login:hover img {
        transform: scale(1.05); /* Slightly reduced scale */
    }

    .btn-google-login:active {
        transform: translateY(0);
    }
    
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #95a5a6;
        margin: 25px 0; /* Reduced margin */
        font-size: 0.8em; /* Reduced font size */
        font-weight: 500;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e1e8ed;
    }

    .divider:not(:empty)::before { margin-right: 0.8em; } /* Adjusted margin */
    .divider:not(:empty)::after { margin-left: 0.8em; } /* Adjusted margin */

    .footer-link {
        text-align: center;
        margin-top: 30px; /* Reduced margin */
        font-size: 0.85em; /* Reduced font size */
        color: #7f8c8d;
        padding-top: 15px; /* Reduced padding */
        border-top: 1px solid #f0f0f0;
    }
    
    .footer-link a {
        color: #229954;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .footer-link a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px; /* Thinner line */
        bottom: -1px; /* Adjusted position */
        left: 50%;
        background-color: #229954;
        transition: all 0.3s ease;
    }

    .footer-link a:hover {
        color: #1e7e34;
    }

    .footer-link a:hover::after {
        width: 100%;
        left: 0;
    }

    .alert-session-error {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
        padding: 12px 16px; /* Reduced padding */
        border: 1px solid #f5c6cb;
        border-radius: 8px; /* Adjusted border-radius */
        margin-bottom: 20px; /* Reduced margin */
        font-size: 0.85em; /* Reduced font size */
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 6px rgba(220, 53, 69, 0.08); /* Adjusted shadow */
    }

    .alert-session-error .close {
        background: none;
        border: none;
        font-size: 1.2rem; /* Reduced font size */
        cursor: pointer;
        color: #721c24;
        padding: 0 0 0 12px; /* Adjusted padding */
        line-height: 1;
        transition: transform 0.2s ease;
    }

    .alert-session-error .close:hover {
        transform: scale(1.05); /* Slightly reduced scale */
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 12px 16px; /* Reduced padding */
        border-radius: 8px; /* Adjusted border-radius */
        margin-bottom: 20px; /* Reduced margin */
        box-shadow: 0 1px 6px rgba(21, 87, 36, 0.08); /* Adjusted shadow */
        animation: slideInDown 0.5s ease;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-15px); /* Slightly reduced transform */
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-content {
            padding: 15px 10px; /* Further reduced padding */
        }

        .form-container {
            padding: 25px 20px; /* Further reduced padding */
            max-width: 100%;
            margin: 0;
        }

        .form-header {
            flex-direction: column;
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-container {
            margin-right: 0;
            margin-bottom: 12px;
        }

        .title-container h1 {
            font-size: 1.3em;
        }

        .btn-submit,
        .btn-google-login {
            height: 48px; /* Further reduced height */
            padding: 12px 15px; /* Further reduced padding */
            font-size: 0.9em;
        }
    }

    @media (max-width: 480px) {
        .main-content {
            padding: 10px 8px;
        }

        .form-container {
            padding: 20px 15px;
        }

        .btn-submit,
        .btn-google-login {
            height: 45px;
            font-size: 0.85em;
        }
    }

    /* Loading Animation */
    .btn-submit.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-submit.loading::after {
        content: '';
        position: absolute;
        width: 18px; /* Reduced size */
        height: 18px; /* Reduced size */
        top: 50%;
        left: 50%;
        margin-left: -9px; /* Adjusted margin */
        margin-top: -9px; /* Adjusted margin */
        border: 2px solid transparent;
        border-top: 2px solid #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

{{-- Konten utama halaman --}}
@section('content')
<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <div class="logo-container">
                <div class="logo-placeholder"></div>
                <div class="logo-text">CABINSKUY</div>
            </div>
            <div class="title-container">
                <h1>Create your Cabinskuy Account</h1>
                <p>Join us and start your amazing staycation journey!</p>
            </div>
        </div>

        @if(session()->has('error'))
        <div class="alert-session-error" role="alert">
            <strong>{{ session('error')}}</strong>
            <button type="button" class="close" onclick="this.parentElement.style.display='none'" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        {{-- Notifikasi sukses registrasi --}}
        @if (session('registration_success_trigger') && session('success_message'))
            <div class="alert-success">
                {{ session('success_message') }}
            </div>
        @endif

        <form action="{{ route('backend.register') }}" method="post" id="registerForm">
            @csrf
            <div class="form-group">
                <div class="label-group">
                    <label for="name">Full Name</label>
                </div>
                <input type="text" name="name" id="name" value="{{old('name')}}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your full name" required autofocus>
                @error('name')
                <span class="input-error-message" role="alert">
                    <strong>{{$message}}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="label-group">
                    <label for="email">Email Address</label>
                </div>
                <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" required>
                @error('email')
                <span class="input-error-message" role="alert">
                    <strong>{{$message}}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="label-group">
                    <label for="password">Password</label>
                </div>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Create a strong password" required>
                @error('password')
                <span class="input-error-message" role="alert">
                    <strong>{{$message}}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="label-group">
                    <label for="password_confirmation">Confirm Password</label>
                </div>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm your password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-submit" id="submitBtn">Create Account</button>
            </div>

            <div class="divider">or sign up with</div>
            
            <a href="{{ route('auth.google') }}" class="btn-google-login">
                <img src="https://img.icons8.com/color/48/google-logo.png" alt="Google logo">
                <span>Sign Up with Google</span>
            </a>
        </form>

        <div class="footer-link">
            Already have an account? <a href="{{ route('backend.login') }}">Log in</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Form submission loading state
        document.getElementById('registerForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Creating Account...';
        });

        // Real-time password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        confirmPassword.addEventListener('input', function() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-session-error, .alert-success');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>

    {{-- Jika ada notifikasi sukses, arahkan ke halaman login setelah beberapa detik --}}
    @if (session('registration_success_trigger'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    window.location.href = "{{ route('backend.login') }}";
                }, 3000); // 3 detik
            });
        </script>
    @endif
@endpush