@extends('backend.user_layout')

{{-- Set judul halaman --}}
@section('title', 'Atur Ulang Kata Sandi - Cabinskuy')

{{-- Sisipkan CSS khusus dari halaman login --}}
@push('styles')
<style>
    /* SEMUA CSS DARI HALAMAN LOGIN ANDA DITEMPEL DI SINI */
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

    input.is-invalid {
        border-color: #e74c3c !important;
        background-color: #fdf2f2 !important;
    }
    
    input.is-invalid:focus {
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
</style>
@endpush

@section('content')
    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <div class="logo-container">
                    <div class="logo-placeholder"></div>
                    <div class="logo-text">CABINSKUY</div>
                </div>
                <div class="title-container">
                    <h1>Atur Ulang Kata Sandi</h1>
                    <p>Masukkan kata sandi baru Anda di bawah ini.</p>
                </div>
            </div>

            {{-- Menggunakan route `password.update` yang biasanya terhubung ke `resetPassword` --}}
            <form action="{{ route('password.update') }}" method="post">
                @csrf
                {{-- Token dan email wajib ada untuk proses reset --}}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <div class="label-group">
                        <label for="email">Alamat Email</label>
                    </div>
                    <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email Anda" required>
                    @error('email')
                    <span class="input-error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="label-group">
                        <label for="password">Kata Sandi Baru</label>
                    </div>
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi baru" required>
                    @error('password')
                    <span class="input-error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="label-group">
                        <label for="password-confirm">Konfirmasi Kata Sandi Baru</label>
                    </div>
                    <input type="password" name="password_confirmation" id="password-confirm" class="form-control"
                        placeholder="Ketik ulang kata sandi baru" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-submit" id="submitBtn">Reset Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>
@endsection