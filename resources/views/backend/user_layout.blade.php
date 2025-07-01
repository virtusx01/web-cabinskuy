<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul halaman akan dinamis, dengan fallback default --}}
    <title>{{ $title ?? 'Cabinskuy' }}</title>

    {{-- Font Awesome untuk ikon sosial --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    
    <style>
        /* Modern CSS Variables */
        :root {
            --primary-green: #2D5D3B;
            --primary-green-light: #4A8B5C;
            --primary-green-dark: #1F3E28;
            --accent-green: #6FCF7F;
            --light-green: #d6ffd8;
            --cream: #FDF8F3;
            --warm-white: #FEFCFA;
            --text-dark: #1A1A1A;
            --text-medium: #4A4A4A;
            --text-light: #7A7A7A;
            --text-white: #FFFFFF;
            --border-light: #E5E7EB;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.12);
            --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.16);
            --radius-sm: 6px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --header-height: 80px;
            --container-max: 1200px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --gradient-primary: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            --gradient-accent: linear-gradient(135deg, var(--accent-green) 0%, var(--primary-green-light) 100%);
            --blur-glass: blur(20px);
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: var(--warm-white);
            color: var(--text-dark);
            line-height: 1.6;
            font-weight: 400;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding-top: var(--header-height);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Modern Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: var(--header-height);
            background-color: var(--light-green);
            border-bottom: 1px solid var(--border-light);
            transition: var(--transition);
        }

        .navbar-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            max-width: var(--container-max);
            margin: 0 auto;
        }

        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: var(--transition);
        }

        .navbar-logo:hover {
            transform: translateY(-1px);
        }

        .navbar-logo img {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            object-fit: cover;
            box-shadow: var(--shadow-sm);
        }

        .navbar-logo-text {
            font-size: 1.25rem; /* Use rem for font size */
            font-weight: bold;
            color: var(--primary-green-light);
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 48px;
            flex: 1;
            justify-content: flex-end;
        }

        .navbar-links {
            display: flex;
            list-style: none;
            gap: 32px;
            margin: 0;
            padding: 0;
            
        }

        .navbar-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 15px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            position: relative;
            transition: var(--transition);
            letter-spacing: -0.2px;
        }

        .navbar-links a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 16px;
            right: 16px;
            height: 2px;
            background: var(--gradient-primary);
            border-radius: 1px;
            transform: scaleX(0);
            transition: var(--transition);
        }

        .navbar-links a:hover,
        .navbar-links a.active {
            color: var(--primary-green);
            
        }

        .navbar-links a:hover::before,
        .navbar-links a.active::before {
            transform: scaleX(1);
        }

        .navbar-auth {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-left: 24px;
            border-left: 1px solid var(--border-light);
        }

        .navbar-auth a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 15px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .navbar-auth a:hover {
            color: var(--primary-green);
            background: var(--light-green);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: var(--radius-lg);
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .profile-info:hover {
            background: var(--light-green);
            border-color: var(--accent-green);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
            color: var(--accent-green);
        }

        .profile-picture {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-green);
            background-size: cover;
            background-position: center;
            border: 2px solid var(--accent-green);
            flex-shrink: 0;
        }

        .profile-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-register {
            background: var(--gradient-primary);
            color: var(--text-white) !important;
            padding: 12px 24px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            border: none;
            cursor: pointer;
            letter-spacing: -0.2px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: var(--primary-green-dark) !important;
        }

        .btn-logout {
            background: none;
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-logout:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        /* Language Switcher */
        .navbar-lang-switcher {
            display: flex !important;
            padding: 4px;
        }


        .lang-btn {
            border: none;
            background: transparent;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.5px;
            transition: var(--transition);
            color: var(--text-medium);
            min-width: 40px;
            margin-inline: 5px;
            justify-content: end;
        }


        .lang-btn.active {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-sm);
            transform: translateY(-1px);
        }

        .lang-btn:hover:not(.active) {
            background: var(--warm-white);
            color: var(--primary-green);
        }

        /* Hamburger Menu */
        .navbar-burger {
            display: none;
            flex-direction: column;
            gap: 4px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .navbar-burger:hover {
            background: var(--light-green);
        }

        .navbar-burger span {
            width: 24px;
            height: 2px;
            background: var(--text-dark);
            border-radius: 1px;
            transition: var(--transition);
        }

        .navbar-burger.is-active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .navbar-burger.is-active span:nth-child(2) {
            opacity: 0;
        }

        .navbar-burger.is-active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
            color: var(--text-white);
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.02)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.02)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.01)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .footer-content-wrapper {
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 80px 24px 40px;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 48px;
            position: relative;
            z-index: 1;
        }

        .footer-column h4 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin-bottom: 24px;
            position: relative;
        }

        .footer-column h4::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 32px;
            height: 2px;
            background: var(--accent-green);
            border-radius: 1px;
        }

        .footer-logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .footer-about p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .footer-socials {
            display: flex;
            gap: 12px;
        }

        .footer-socials a {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }

        .footer-socials a:hover {
            background: var(--accent-green);
            border-color: var(--accent-green);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(111, 207, 127, 0.3);
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            transition: var(--transition);
            position: relative;
            padding-left: 0;
        }

        .footer-links a:hover {
            color: var(--accent-green);
            padding-left: 8px;
        }

        .footer-contact p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .footer-contact i {
            color: var(--accent-green);
            margin-top: 2px;
            flex-shrink: 0;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 24px 24px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            max-width: var(--container-max);
            margin: 0 auto;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: var(--transition);
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            border-radius: var(--radius-xl);
            padding: 40px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: var(--shadow-xl);
            transform: translateY(20px);
            transition: var(--transition);
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--primary-green);
            margin-bottom: 16px;
            font-weight: 600;
        }

        .modal-content p {
            color: var(--text-medium);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .modal-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .modal-button {
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            min-width: 100px;
        }

        .modal-button.cancel {
            background: var(--border-light);
            color: var(--text-dark);
        }

        .modal-button.cancel:hover {
            background: #d1d5db;
            transform: translateY(-1px);
        }

        .modal-button.ok {
            background: var(--gradient-primary);
            color: white;
        }

        .modal-button.ok:hover {
            background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .footer-content-wrapper {
                grid-template-columns: 1fr 1fr;
                gap: 32px;
                padding: 60px 24px 32px;
            }
            
            .footer-about {
                grid-column: 1 / -1;
                text-align: center;
            }
            
            .footer-socials {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            :root {
                --header-height: 72px;
            }

            .navbar-container {
                padding: 0 16px;
            }

            .navbar-menu {
                position: absolute;
                top: var(--header-height);
                left: 0;
                right: 0;
                background: var(--light-green);
                backdrop-filter: var(--blur-glass);
                flex-direction: column;
                align-items: center; 
                gap: 0;
                padding: 24px;
                border-bottom: 1px solid var(--border-light);
                box-shadow: var(--shadow-lg);
                transform: translateY(-100%);
                opacity: 0;
                pointer-events: none;
                transition: var(--transition);
            }

            .navbar-menu.is-active {
                transform: translateY(0);
                opacity: 1;
                pointer-events: all;
                align-items: center; 
            }

            .navbar-burger {
                display: flex;
            }

            .navbar-links {
                flex-direction: column;
                width: 100%;
                gap: 30px;
                margin-bottom: 24px;
                padding-bottom: 24px;
                border-bottom: 1px solid var(--border-light);
                text-align: center;
            }

            .navbar-links a {
                width: 100%;
                padding: 12px 16px;
                border-radius: var(--radius-md);
            }

            .navbar-auth {
                flex-direction: column;
                width: 100%;
                gap: 12px;
                padding-left: 0;
                border-left: none;
                border-top: 1px solid var(--border-light);
                padding-top: 24px;
                margin-bottom: 24px;
                align-items: center; 
            }

            .navbar-auth .btn-register,
            .navbar-auth .btn-logout {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .profile-info {
                width: 100%;
                justify-content: center;
            }

            .navbar-lang-switcher {
                width: 100%;
                justify-content: center;
            }

            .footer-content-wrapper {
                grid-template-columns: 1fr;
                gap: 32px;
                padding: 48px 16px 24px;
                text-align: center;
            }

            .footer-column h4::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .footer-column.footer-contact p{
                text-align: center !important;
                justify-content: center;
            }

            .modal-content {
                padding: 32px 24px;
                margin: 16px;
            }

            .modal-buttons {
                flex-direction: column;
            }

            .modal-button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }

            .navbar-container {
                padding: 0 12px;
            }

            .navbar-logo-text {
                font-size: 20px;
            }

            .navbar-logo img {
                width: 40px;
                height: 40px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }

        /* Utility Classes */
        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Focus States for Accessibility */
        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 2px solid var(--accent-green);
            outline-offset: 2px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <header class="navbar">
        <div class="navbar-container">
            <a href="{{ url('/') }}" class="navbar-logo">
                <img src="{{ asset('backend/images/icon-cabinskuy.jpg') }}" alt="Cabinskuy Logo">
                <span class="navbar-logo-text" data-translate="navbar_logo">CABINSKUY</span>
            </a>

            {{-- Hamburger button: display: none by default (desktop), display: flex in media query (mobile) --}}
            <button class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div id="navbarMenu" class="navbar-menu">
                
                <nav>
                    <ul class="navbar-links">
                        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}" data-translate="nav_home">Home</a></li>
                        <li><a href="{{ url('/kabin') }}" class="{{ request()->is('kabin*') ? 'active' : '' }}" data-translate="nav_cabin">Kabin</a></li>
                        @auth
                            <li><a href="{{ route('frontend.booking.index') }}" class="{{ request()->routeIs('frontend.booking.index') ? 'active' : '' }}" data-translate="nav_my_bookings">My Bookings</a></li>
                        @endauth
                    </ul>
                </nav>
                <div class="navbar-auth">
                    @auth
                        @if(Auth::user()->isCustomer())
                            <a href="{{ route('profile.user.edit') }}" class="profile-info {{ request()->routeIs('profile.user.*') ? 'active' : '' }}">
                                <div class="profile-picture"
                                    style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset('backend/images/default-avatar.png') }}');">
                                </div>
                                <span class="profile-name">{{ Auth::user()->name }}</span>
                            </a>
                        @else
                            <a href="{{ route('profile.user.show') }}" class="profile-info {{ request()->routeIs('profile.user.*') ? 'active' : '' }}">
                                <div class="profile-picture"
                                    style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset('backend/images/default-avatar.png') }}');">
                                </div>
                                <span class="profile-name">{{ Auth::user()->name }}</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('backend.logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-logout" data-translate="logout_btn">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('backend.login') }}" data-translate="login_btn">Log in</a>
                        <a href="{{ route('backend.register') }}" class="btn-register" data-translate="register_btn">Register</a>
                    @endauth
                </div>
                <div class="navbar-lang-switcher">
                    <button class="lang-btn" data-lang="en">EN</button>
                    <button class="lang-btn" data-lang="id">ID</button>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        {{-- This new wrapper will contain your footer columns and apply max-width/padding --}}
        <div class="footer-content-wrapper">
            <div class="footer-column footer-about">
                <h3 class="footer-logo" data-translate="footer_logo">CABINSKUY</h3>
                <p data-translate="footer_about_desc">Temukan ketenangan dan kemewahan di tengah alam. Cabinskuy menyediakan pengalaman menginap di kabin yang tak terlupakan dengan fasilitas terbaik.</p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-column footer-links">
                <h4 data-translate="footer_nav_title">Navigasi</h4>
                <ul>
                    <li><a href="{{ url('/') }}" data-translate="nav_home_footer">Home</a></li>
                    <li><a href="{{ url('/kabin') }}" data-translate="nav_cabin_footer">Kabin</a></li>
                    <li><a href="#" id="footerMyBookingsLink" data-translate="nav_my_bookings_footer">My Bookings</a></li>
                </ul>
            </div>
            <div class="footer-column footer-links">
                <h4 data-translate="footer_help_title">Bantuan</h4>
                <ul>
                    <li><a href="#" data-translate="footer_faq">FAQ</a></li>
                    <li><a href="#" data-translate="footer_terms">Syarat & Ketentuan</a></li>
                    <li><a href="#" data-translate="footer_privacy">Kebijakan Privasi</a></li>
                </ul>
            </div>
            <div class="footer-column footer-contact">
                <h4 data-translate="footer_contact_title">Hubungi Kami</h4>
                <p>
                    <i class="fas fa-map-marker-alt"></i> <span data-translate="footer_address">Jl. Alam Asri No. 123,<br>Bandung, Indonesia</span>
                </p>
                <p>
                    <a href="https://wa.me/6281574422949" target="_blank" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 12px;">
                        <i class="fab fa-whatsapp"></i> <span data-translate="footer_phone">+6281574422949</span>
                    </a>
                </p>
                <p><i class="fas fa-envelope"></i> <span data-translate="footer_email">halo@cabinskuy.com</span></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p data-translate="footer_copyright">&copy; {{ date('Y') }} Cabinskuy. All rights reserved.</p>
        </div>
    </footer>

    {{-- Floating Login Window (Modal) --}}
    <div id="loginModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalMessage">
        <div class="modal-content">
            <h3 id="modalTitle">Anda Belum Login</h3>
            <p id="modalMessage">Untuk melihat pemesanan Anda, silakan login terlebih dahulu.</p>
            <div class="modal-buttons">
                <button class="modal-button cancel" id="cancelLogin" type="button">Batal</button>
                <button class="modal-button ok" id="confirmLogin" type="button">Oke</button>
            </div>
        </div>
    </div>

    <script>
        // Translation data
        const translations = {
            en: {
                // Navbar
                navbar_logo: "CABINSKUY",
                nav_home: "Home",
                nav_cabin: "Cabin",
                nav_my_bookings: "My Bookings",
                login_btn: "Log in",
                register_btn: "Register",
                logout_btn: "Logout",

                // Footer (re-check keys for uniqueness and clarity)
                footer_logo: "CABINSKUY",
                footer_about_desc: "Discover tranquility and luxury amidst nature. Cabinskuy offers unforgettable cabin stays with the best facilities.",
                footer_nav_title: "Navigation",
                nav_home_footer: "Home",
                nav_cabin_footer: "Cabin",
                nav_my_bookings_footer: "My Bookings",
                footer_help_title: "Help",
                footer_faq: "FAQ",
                footer_terms: "Terms & Conditions",
                footer_privacy: "Privacy Policy",
                footer_contact_title: "Contact Us",
                footer_address: "Jl. Alam Asri No. 123,<br>Bandung, Indonesia",
                footer_phone: "+6281574422949", // Updated phone number
                footer_email: "halo@cabinskuy.com",
                footer_copyright: "© 2025 Cabinskuy. All rights reserved.", // Updated year dynamically

                // Homepage Content (from your provided example for reference)
                hero_title: "Live Out The Adventure With Cabinskuy",
                hero_description: "Discover unique cabin stays nestled in nature. Your perfect getaway for tranquility and adventure awaits. Book your escape today!",
                search_title: "Good Morning! Where Do You Want To Stay?",
                location_label: "Location:",
                all_locations: "All Locations",
                province_label: "Province:",
                all_provinces: "All Provinces",
                regency_label: "Regency/City:",
                all_regencies: "All Regencies/Cities",
                checkin_label: "Check In",
                checkout_label: "Check Out",
                guests_label: "Guests",
                guest: "Guest",
                guests: "Guests",
                search_btn: "Search",
                standard: "Standard",
                deluxe: "Deluxe",
                executive: "Executive",
                standard_title: "Standard Cabin",
                standard_desc: "Our Standard Cabin offers a cozy and comfortable retreat, perfect for solo travelers or couples. Enjoy essential amenities amidst serene natural surroundings.",
                standard_feature1: "Comfortable queen-sized bed",
                standard_feature2: "Private bathroom with hot shower",
                standard_feature3: "Small kitchenette area",
                standard_feature4: "Wi-Fi access",
                offers: "Offers:",
                standard_offers: "Free breakfast for two, 10% off on weekday stays.",
                deluxe_title: "Deluxe Cabin",
                deluxe_desc: "Experience enhanced comfort and more space in our Deluxe Cabin. Ideal for families or those seeking extra amenities and a touch of luxury.",
                deluxe_feature1: "Spacious king-sized bed & sofa bed",
                deluxe_feature2: "En-suite bathroom with premium toiletries",
                deluxe_feature3: "Fully equipped kitchenette",
                deluxe_feature4: "Private balcony with a view",
                deluxe_feature5: "Smart TV and high-speed Wi-Fi",
                deluxe_offers: "Welcome drink & fruit basket, 15% off for stays over 3 nights.",
                executive_title: "Executive Cabin",
                executive_desc: "Indulge in the ultimate cabin experience with our Executive Cabin. Featuring top-tier amenities, expansive space, and breathtaking views for an unforgettable stay.",
                executive_feature1: "Luxurious super king-sized bed",
                executive_feature2: "Jacuzzi in a large en-suite bathroom",
                executive_feature3: "Gourmet kitchen with modern appliances",
                executive_feature4: "Expansive private deck with outdoor seating",
                executive_feature5: "Personalized concierge service",
                executive_offers: "Complimentary minibar, private bonfire setup, 20% off spa services.",
                explore_cabins: "Explore All Cabins",
                find_near_title: "Find CABINSKUY Near You",
                bandung_desc: "Escape to the cool highlands of Bandung. Our cabins offer a perfect blend of modern comfort and natural beauty, ideal for a refreshing getaway.",
                bogor_desc: "Discover tranquility just a short drive from the city. Our Bogor cabins are surrounded by lush greenery, offering a peaceful retreat.",
                bali_desc: "Immerse yourself in the magical island atmosphere of Bali. Our unique cabins provide an exotic and serene base for your island adventures.",
                see_more_locations: "See More Locations",
                how_to_book: "How to Book Your Getaway",
                step1_title: "Select Your Favorite Cabin",
                step1_desc: "Browse our diverse collection and choose the cabin that suits your style and needs.",
                step2_title: "Book and Pay",
                step2_desc: "Secure your dates with our easy and secure online payment system.",
                step3_title: "Verify Your ID",
                step3_desc: "Complete a simple verification process for a smooth check-in experience.",
                step4_title: "Check-in and Enjoy!",
                step4_desc: "Arrive at your cabin, check-in seamlessly, and start your unforgettable getaway.",
                modal_title: "You are not logged in",
                modal_message: "To view your bookings, please log in first.",
                modal_cancel: "Cancel",
                modal_ok: "Ok"
            },
            id: {
                // Navbar
                navbar_logo: "CABINSKUY",
                nav_home: "Beranda",
                nav_cabin: "Kabin",
                nav_my_bookings: "Pemesanan Saya",
                login_btn: "Masuk",
                register_btn: "Daftar",
                logout_btn: "Keluar",

                // Footer
                footer_logo: "CABINSKUY",
                footer_about_desc: "Temukan ketenangan dan kemewahan di tengah alam. Cabinskuy menyediakan pengalaman menginap di kabin yang tak terlupakan dengan fasilitas terbaik.",
                footer_nav_title: "Navigasi",
                nav_home_footer: "Beranda",
                nav_cabin_footer: "Kabin",
                nav_my_bookings_footer: "Pemesanan Saya",
                footer_help_title: "Bantuan",
                footer_faq: "FAQ",
                footer_terms: "Syarat & Ketentuan",
                footer_privacy: "Kebijakan Privasi",
                footer_contact_title: "Hubungi Kami",
                footer_address: "Jl. Alam Asri No. 123,<br>Bandung, Indonesia",
                footer_phone: "+6281574422949", // Updated phone number
                footer_email: "halo@cabinskuy.com",
                footer_copyright: "© 2025 Cabinskuy. Hak Cipta Dilindungi.", // Updated year dynamically

                // Homepage Content (from your provided example for reference)
                hero_title: "Wujudkan Petualangan Bersama Cabinskuy",
                hero_description: "Temukan penginapan kabin unik yang tersembunyi di alam. Liburan sempurna Anda untuk ketenangan dan petualangan menanti. Pesan pelarian Anda hari ini!",
                search_title: "Selamat Pagi! Dimana Anda Ingin Menginap?",
                location_label: "Lokasi:",
                all_locations: "Semua Lokasi",
                province_label: "Provinsi:",
                all_provinces: "Semua Provinsi",
                regency_label: "Kabupaten/Kota:",
                all_regencies: "Semua Kabupaten/Kota",
                checkin_label: "Check In",
                checkout_label: "Check Out",
                guests_label: "Tamu",
                guest: "Tamu", // Singular
                guests: "Tamu", // Plural (assuming 'Tamu' is singular and plural in ID)
                search_btn: "Cari",
                standard: "Standar",
                deluxe: "Deluxe",
                executive: "Eksekutif",
                standard_title: "Kabin Standar",
                standard_desc: "Kabin Standar kami menawarkan tempat peristirahatan yang nyaman dan hangat, sempurna untuk solo traveler atau pasangan. Nikmati fasilitas penting di tengah lingkungan alam yang tenang.",
                standard_feature1: "Tempat tidur queen yang nyaman",
                standard_feature2: "Kamar mandi pribadi dengan shower air panas",
                standard_feature3: "Area dapur kecil",
                standard_feature4: "Akses Wi-Fi",
                offers: "Penawaran:",
                standard_offers: "Sarapan gratis untuk dua orang, diskon 10% untuk menginap di hari kerja.",
                deluxe_title: "Kabin Deluxe",
                deluxe_desc: "Rasakan kenyamanan yang lebih baik dan ruang yang lebih luas di Kabin Deluxe kami. Ideal untuk keluarga atau mereka yang mencari fasilitas ekstra dan sentuhan kemewahan.",
                deluxe_feature1: "Tempat tidur king yang luas & sofa bed",
                deluxe_feature2: "Kamar mandi en-suite dengan perlengkapan mandi premium",
                deluxe_feature3: "Dapur kecil yang lengkap",
                deluxe_feature4: "Balkon pribadi dengan pemandangan",
                deluxe_feature5: "Smart TV dan Wi-Fi berkecepatan tinggi",
                deluxe_offers: "Minuman selamat datang & keranjang buah, diskon 15% untuk menginap lebih dari 3 malam.",
                executive_title: "Kabin Eksekutif",
                executive_desc: "Manjakan diri dalam pengalaman kabin terbaik dengan Kabin Eksekutif kami. Menampilkan fasilitas kelas atas, ruang yang luas, dan pemandangan menakjubkan untuk pengalaman yang tak terlupakan.",
                executive_feature1: "Tempat tidur super king yang mewah",
                executive_feature2: "Jacuzzi di kamar mandi en-suite yang besar",
                executive_feature3: "Dapur gourmet dengan peralatan modern",
                executive_feature4: "Dek pribadi yang luas dengan tempat duduk outdoor",
                executive_feature5: "Layanan concierge yang dipersonalisasi",
                executive_offers: "Minibar gratis, setup api unggun pribadi, diskon 20% layanan spa.",
                explore_cabins: "Jelajahi Semua Kabin",
                find_near_title: "Temukan CABINSKUY Dekat Anda",
                bandung_desc: "Melarikan diri ke dataran tinggi Bandung yang sejuk. Kabin kami menawarkan perpaduan sempurna antara kenyamanan modern dan keindahan alam, ideal untuk liburan yang menyegarkan.",
                bogor_desc: "Temukan ketenangan hanya dalam perjalanan singkat dari kota. Kabin Bogor kami dikelilingi oleh kehijauan yang rimbun, menawarkan tempat peristirahatan yang damai.",
                bali_desc: "Benamkan diri Anda dalam suasana pulau magis Bali. Kabin unik kami menyediakan basis yang eksotis dan tenang untuk petualangan pulau Anda.",
                see_more_locations: "Lihat Lokasi Lainnya",
                how_to_book: "Cara Memesan Liburan Anda",
                step1_title: "Pilih Kabin Favorit Anda",
                step1_desc: "Jelajahi koleksi beragam kami dan pilih kabin yang sesuai dengan gaya dan kebutuhan Anda.",
                step2_title: "Pesan dan Bayar",
                step2_desc: "Amankan tanggal Anda dengan sistem pembayaran online yang mudah dan aman.",
                step3_title: "Verifikasi ID Anda",
                step3_desc: "Lengkapi proses verifikasi sederhana untuk pengalaman check-in yang lancar.",
                step4_title: "Check-in dan Nikmati!",
                step4_desc: "Tiba di kabin Anda, check-in dengan lancar, dan mulai liburan yang tak terlupakan.",
                modal_title: "Anda Belum Login",
                modal_message: "Untuk melihat pemesanan Anda, silakan login terlebih dahulu.",
                modal_cancel: "Batal",
                modal_ok: "Oke"
            }
        };

        // Get user's country based on IP (simplified detection)
        async function detectUserCountry() {
            try {
                const response = await fetch('https://ipapi.co/json/');
                const data = await response.json();
                console.log('IP Data:', data);
                return data.country_code;
            } catch (error) {
                console.warn('Could not detect country based on IP, defaulting to English:', error);
                return 'US'; // Default ke US jika deteksi gagal
            }
        }

        // Apply translations to elements with data-translate attribute
        function applyTranslations(language) {
            console.log(`Applying translations for: ${language}`);
            document.querySelectorAll('[data-translate]').forEach(element => {
                const key = element.getAttribute('data-translate');
                if (translations[language] && translations[language][key]) {
                    if (element.tagName === 'INPUT' && element.type === 'submit') {
                        element.value = translations[language][key];
                    } else if (element.tagName === 'OPTION') {
                        element.textContent = translations[language][key];
                    } else {
                        element.innerHTML = translations[language][key];
                    }
                }
            });

            // Update placeholder texts (if any, add data-translate-placeholder to elements)
            document.querySelectorAll('[data-translate-placeholder]').forEach(element => {
                const key = element.getAttribute('data-translate-placeholder');
                if (translations[language] && translations[language][key]) {
                    element.placeholder = translations[language][key];
                }
            });

            // Update guest options separately as they are dynamically generated
            updateGuestOptions(language);
            updateLocationOptions(language); // Perbarui opsi lokasi

            // Update modal content based on language
            const modalTitle = document.querySelector('#loginModal h3');
            const modalMessage = document.querySelector('#loginModal p');
            const modalCancelBtn = document.getElementById('cancelLogin');
            const modalOkBtn = document.getElementById('confirmLogin');

            if (modalTitle) modalTitle.textContent = translations[language].modal_title || "You are not logged in";
            if (modalMessage) modalMessage.textContent = translations[language].modal_message || "To view your bookings, please log in first.";
            if (modalCancelBtn) modalCancelBtn.textContent = translations[language].modal_cancel || "Cancel";
            if (modalOkBtn) modalOkBtn.textContent = translations[language].modal_ok || "Ok";
        }

        // Update guest select options (specific function as it's dynamic)
        function updateGuestOptions(language) {
            const guestSelect = document.getElementById('guests');
            if (guestSelect) {
                const guestText = translations[language].guest || 'Guest';
                const guestsText = translations[language].guests || 'Guests';
                const selectGuestText = translations[language].select_guest_count || 'Select Guest Count'; // New translation key
                const currentValue = guestSelect.value;

                // Clear existing options
                guestSelect.innerHTML = `<option value="">${selectGuestText}</option>`;
                for (let i = 1; i <= 10; i++) { // Maksimal 10 tamu, atau sesuai kebutuhan Anda
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = `${i} ${i === 1 ? guestText : guestsText}`;
                    guestSelect.appendChild(option);
                }
                guestSelect.value = currentValue; // Setel kembali nilai yang dipilih
            }
        }

        // Update location select options (new function, assuming dynamic generation or static translation)
        function updateLocationOptions(language) {
            const provinceSelect = document.getElementById('filter-province');
            if (provinceSelect) {
                const allProvincesText = translations[language].all_provinces || 'All Provinces';
                provinceSelect.querySelector('option[value=""]').textContent = allProvincesText;
            }

            const regencySelect = document.getElementById('filter-regency');
            if (regencySelect) {
                const allRegenciesText = translations[language].all_regencies || 'All Regencies/Cities';
                regencySelect.querySelector('option[value=""]').textContent = allRegenciesText;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Updated dynamic year for footer copyright
            const currentYear = new Date().getFullYear();
            translations.en.footer_copyright = `© ${currentYear} Cabinskuy. All rights reserved.`;
            translations.id.footer_copyright = `© ${currentYear} Cabinskuy. Hak Cipta Dilindungi.`;

            // Initiate default language detection and application
            setDefaultLanguageOnLoad();

            // Language button click events
            document.querySelectorAll('.lang-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const selectedLang = this.dataset.lang;

                    // Update active button classes
                    document.querySelectorAll('.lang-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Apply translations instantly
                    applyTranslations(selectedLang);

                    // Store preference in local storage
                    localStorage.setItem('preferred_language', selectedLang);
                });
            });

            // Hamburger menu toggle functionality
            const navbarBurger = document.querySelector('.navbar-burger');
            const navbarMenu = document.getElementById('navbarMenu');

            if (navbarBurger && navbarMenu) {
                navbarBurger.addEventListener('click', () => {
                    navbarBurger.classList.toggle('is-active');
                    navbarMenu.classList.toggle('is-active');
                    const isExpanded = navbarBurger.getAttribute('aria-expanded') === 'true';
                    navbarBurger.setAttribute('aria-expanded', !isExpanded);
                });

                // Close navbar menu when a link is clicked (for mobile UX)
                document.querySelectorAll('.navbar-links a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (navbarMenu.classList.contains('is-active')) {
                            navbarBurger.classList.remove('is-active');
                            navbarMenu.classList.remove('is-active');
                            navbarBurger.setAttribute('aria-expanded', 'false');
                        }
                    });
                });
            }

            // --- My Bookings Link Logic (Header & Footer) ---
            const footerMyBookingsLink = document.getElementById('footerMyBookingsLink');
            const loginModal = document.getElementById('loginModal');
            const cancelLoginBtn = document.getElementById('cancelLogin');
            const confirmLoginBtn = document.getElementById('confirmLogin');

            const isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
            const myBookingsRoute = "{{ route('frontend.booking.index') }}";
            const loginRoute = "{{ route('backend.login') }}";

            if (footerMyBookingsLink) {
                footerMyBookingsLink.addEventListener('click', function(e) {
                    if (!isAuthenticated) {
                        e.preventDefault();
                        loginModal.classList.add('show');
                    } else {
                        window.location.href = myBookingsRoute;
                    }
                });
            }

            if (cancelLoginBtn) {
                cancelLoginBtn.addEventListener('click', function() {
                    loginModal.classList.remove('show');
                });
            }

            if (confirmLoginBtn) {
                confirmLoginBtn.addEventListener('click', function() {
                    window.location.href = loginRoute;
                });
            }

            if (loginModal) {
                loginModal.addEventListener('click', function(e) {
                    if (e.target === loginModal) {
                        loginModal.classList.remove('show');
                    }
                });
            }
            document.addEventListener('keydown', function(event) {
                if (event.key === "Escape" && loginModal.classList.contains('show')) {
                    loginModal.classList.remove('show');
                }
            });
            // --- End My Bookings Link Logic ---

            // Tab functionality (from your provided code) - only if elements exist
            const tabsContainer = document.querySelector('.cabin-types-tabs');
            if (tabsContainer) {
                const tabs = document.querySelectorAll('.cabin-types-tabs .tab-link');
                const tabContents = document.querySelectorAll('.cabin-types-section .tab-content');

                tabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        tabs.forEach(item => item.classList.remove('active'));
                        tabContents.forEach(content => {
                            content.classList.remove('active');
                            content.style.display = 'none';
                        });

                        this.classList.add('active');
                        const targetContentId = this.getAttribute('data-tab');
                        const targetContent = document.getElementById(targetContentId);

                        setTimeout(() => {
                            if (targetContent) {
                                targetContent.style.display = 'block';
                                targetContent.classList.add('active', 'slide-up');
                                setTimeout(() => {
                                    targetContent.classList.remove('slide-up');
                                }, 600);
                            }
                        }, 50);
                    });
                });
                if (tabs.length > 0) {
                    tabs[0].click();
                }
            }


            // Date input min/max logic (from your provided code)
            const today = new Date().toISOString().split('T')[0];
            const checkinInput = document.getElementById('check_in_date');
            const checkoutInput = document.getElementById('check_out_date');

            if(checkinInput) {
                checkinInput.setAttribute('min', today);
            }
            if(checkoutInput) {
                checkoutInput.setAttribute('min', today);
            }

            if(checkinInput && checkoutInput) {
                checkinInput.addEventListener('change', function() {
                    const checkinDate = new Date(this.value);
                    const minCheckoutDate = new Date(checkinDate);
                    minCheckoutDate.setDate(minCheckoutDate.getDate() + 1);

                    const minCheckoutDateString = minCheckoutDate.toISOString().split('T')[0];
                    checkoutInput.setAttribute('min', minCheckoutDateString);

                    if (checkoutInput.value < minCheckoutDateString) {
                        checkoutInput.value = minCheckoutDateString;
                    }
                });
            }

            // Scroll animations (from your provided code)
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('slide-up');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.cabin-card, .booking-step').forEach(el => {
                observer.observe(el);
            });

            // Ripple effect (from your provided code)
            document.querySelectorAll('.btn-search, .btn-see-more, .modal-button.ok, .modal-button.cancel').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s ease-out;
                        pointer-events: none;
                        z-index: 1;
                    `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.querySelectorAll('.ripple-effect').forEach(oldRipple => oldRipple.remove());
                    ripple.classList.add('ripple-effect');
                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Set default language on page load
            async function setDefaultLanguageOnLoad() {
                let initialLang = localStorage.getItem('preferred_language');

                if (!initialLang) {
                    const countryCode = await detectUserCountry();
                    initialLang = (countryCode === 'ID') ? 'id' : 'en';
                    localStorage.setItem('preferred_language', initialLang);
                }
                console.log('Initial language determined:', initialLang);
                applyTranslations(initialLang);

                document.querySelectorAll('.lang-btn').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.dataset.lang === initialLang) {
                        btn.classList.add('active');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>