<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Cabinskuy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #229954;
            --primary-dark: #1e8449;
            --light-green-bg: #e9f5e9;
            --dark-bg: #223324;
            --text-dark: #333;
            --text-light: #f8f9fa;
            --border-color: #e0e0e0;
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: var(--text-dark);
        }
        
        main {
            flex-grow: 1;
            padding: 30px 0;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        h1, h2, h3 {
            font-weight: 600;
        }

        .navbar {
            background-color: #ffffff;
            padding: 15px 0;
            width: 100%;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            margin-left: 13px;
        }
        
        .navbar-logo-icon-container {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            margin-right: 10px;
            background-image: url('/backend/images/icon-cabinskuy.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            border: 2px solid var(--primary-color);
        }
        
        .navbar-logo-text {
            font-size: 1.4em;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .navbar-auth {
            display: flex;
            align-items: center;
            margin-right: 13px;
        }
        
        .navbar-auth a {
            text-decoration: none;
            color: var(--text-dark);
            font-size: 0.95em;
            font-weight: 500;
            margin-left: 15px;
            padding: 8px 12px;
            border-radius: 5px;
            transition: color 0.3s, background-color 0.3s;
        }
        
        .navbar-auth .profile-info {
            display: flex;
            align-items: center;
            padding-right: 0;
        }
        
        .navbar-auth .profile-picture {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            margin-right: 10px;
            background-color: #ccc;
            background-size: cover;
            background-position: center;
            border: 2px solid var(--primary-color);
        }
        
        .navbar-auth .profile-name {
            font-weight: 500;
        }
        
        .navbar-auth a.logout-link {
            color: red;
        }

        .navbar-auth a.logout-link:hover {
            background-color: #e74c3c;
            color: white;
        }

        /* Hamburger Menu */
        .hamburger-menu {
            display: none;
            background: none;
            border: none;
            font-size: 1.5em;
            color: var(--primary-color);
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-right: 13px;
        }

        .hamburger-menu:hover {
            background-color: var(--light-green-bg);
        }

        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            min-width: 280px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1001;
        }

        .mobile-menu.show {
            display: block;
        }

        .mobile-menu-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .mobile-menu-header .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            background-color: #ccc;
            background-size: cover;
            background-position: center;
            border: 2px solid white;
        }

        .mobile-menu-header .profile-info {
            flex: 1;
        }

        .mobile-menu-header .profile-name {
            font-weight: 600;
            font-size: 1em;
            margin-bottom: 2px;
        }

        .mobile-menu-header .profile-role {
            font-size: 0.85em;
            opacity: 0.9;
        }

        .mobile-menu-section {
            padding: 10px 0;
        }

        .mobile-menu-section-title {
            padding: 10px 20px 5px 20px;
            font-size: 0.85em;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .mobile-menu a {
            color: var(--text-dark);
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s, color 0.2s;
        }

        .mobile-menu a:last-child {
            border-bottom: none;
        }

        .mobile-menu a:hover {
            background-color: var(--light-green-bg);
            color: var(--primary-color);
        }

        .mobile-menu a i {
            margin-right: 12px;
            width: 16px;
            text-align: center;
        }

        .mobile-menu a.logout-link {
            color: #e74c3c;
            border-top: 1px solid #f0f0f0;
            margin-top: 5px;
        }

        .mobile-menu a.logout-link:hover {
            background-color: #ffeaea;
            color: #c0392b;
        }

        /* Desktop Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
            margin-left: 15px;
        }

        .dropdown-toggle {
            background-color: var(--primary-color);
            color: white !important;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }

        .dropdown-toggle:hover {
            background-color: var(--primary-dark);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
            top: calc(100% + 10px);
            left: 0;
        }

        .dropdown-menu a {
            color: var(--text-dark);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            margin: 0;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s, color 0.2s;
            white-space: nowrap;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .dropdown-menu a:hover {
            background-color: var(--light-green-bg);
            color: var(--primary-color);
        }

        .dropdown-menu.show {
            display: block;
        }

        .admin-header {
            background: white;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }
        .admin-header h1 {
            margin: 0 0 5px 0;
            color: var(--primary-dark);
        }
        .admin-header p {
            margin: 0;
            color: #666;
        }

        .footer {
            background-color: var(--dark-bg);
            color: var(--text-light);
        }
        
        .footer-column {
            flex: 1;
            min-width: 220px;
        }
        
        .footer-column h4 {
            color: #fff;
            font-size: 1.1em;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-bottom {
            border-top: 1px solid #445546;
            text-align: center;
            padding: 20px 0;
        }
        
        .footer-bottom p {
            margin: 0;
            font-size: 0.9em;
            color: #ced4da;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .navbar-container {
                position: relative;
            }

            .navbar-logo-text {
                font-size: 1.2em;
            }

            .navbar-auth {
                display: none;
            }

            .hamburger-menu {
                display: block;
            }

            .container {
                padding: 0 15px;
            }

            main {
                padding: 20px 0;
            }

            .admin-header {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .navbar-logo-text {
                font-size: 1.1em;
            }

            .navbar-logo-icon-container {
                width: 35px;
                height: 35px;
            }

            .mobile-menu {
                min-width: 250px;
                right: -10px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <header class="navbar">
        <div class="container navbar-container">
            <a href="{{ route('admin.beranda') }}" class="navbar-logo">
                <div class="navbar-logo-icon-container"></div>
                <span class="navbar-logo-text">CABINSKUY</span>
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="navbar-auth">
                @auth
                    {{-- Quick Actions Dropdown --}}
                    <div class="dropdown" id="quickActionsDropdown">
                        <a href="#" class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bolt"></i> Aksi Cepat
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('admin.cabins.create') }}">Tambah Kabin Baru</a>
                            <a href="{{ route('admin.cabins.index') }}">Kelola Kabin</a>
                            <a href="{{ route('admin.bookings.index') }}">Kelola Booking</a>
                            <a href="{{ route('admin.reports.booking') }}">Laporan</a>
                            @if (Auth::user()->isSuperAdmin())
                                <a href="{{ route('admin.employees.index') }}">Kelola Karyawan</a>
                            @endif
                        </div>
                    </div>

                    <a href="#" class="profile-info">
                        <div class="profile-picture" style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset('backend/images/default-avatar.png') }}');"></div>
                        <span class="profile-name">
                            @if (Auth::user()->isSuperAdmin())
                                Super Admin {{ Str::after(Auth::user()->name, 'Admin ') }}
                            @elseif (Auth::user()->isAdmin())
                                Admin {{ Str::after(Auth::user()->name, 'Admin ') }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </span>
                    </a>
                    <form method="POST" action="{{ route('backend.logout') }}" style="display: inline;">
                        @csrf
                        <a href="{{ route('backend.logout') }}"
                        class="logout-link"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </form>
                @endauth
            </nav>

            <!-- Mobile Hamburger Menu -->
            @auth
            <button class="hamburger-menu" id="mobileMenuToggle" aria-label="Toggle mobile menu">
                <i class="fas fa-bars"></i>
            </button>

            <div class="mobile-menu" id="mobileMenu">
                <div class="mobile-menu-header">
                    <div class="profile-picture" style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset('backend/images/default-avatar.png') }}');"></div>
                    <div class="profile-info">
                        <div class="profile-name">
                            @if (Auth::user()->isSuperAdmin())
                                {{ Str::after(Auth::user()->name, 'Admin ') }}
                            @elseif (Auth::user()->isAdmin())
                                {{ Str::after(Auth::user()->name, 'Admin ') }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </div>
                        <div class="profile-role">
                            @if (Auth::user()->isSuperAdmin())
                                Super Administrator
                            @elseif (Auth::user()->isAdmin())
                                Administrator
                            @else
                                User
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mobile-menu-section">
                    <div class="mobile-menu-section-title">Aksi Cepat</div>
                    <a href="{{ route('admin.cabins.create') }}">
                        <i class="fas fa-plus"></i> Tambah Kabin Baru
                    </a>
                    <a href="{{ route('admin.cabins.index') }}">
                        <i class="fas fa-home"></i> Kelola Kabin
                    </a>
                    <a href="{{ route('admin.bookings.index') }}">
                        <i class="fas fa-calendar-check"></i> Kelola Booking
                    </a>
                    <a href="{{ route('admin.reports.booking') }}">
                        <i class="fas fa-chart-bar"></i> Laporan Booking
                    </a>
                    @if (Auth::user()->isSuperAdmin())
                        <a href="{{ route('admin.employees.index') }}">
                            <i class="fas fa-users"></i> Kelola Karyawan
                        </a>
                    @endif
                </div>

                <form method="POST" action="{{ route('backend.logout') }}">
                    @csrf
                    <a href="{{ route('backend.logout') }}" class="logout-link"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </form>
            </div>
            @endauth
        </div>
    </header>

    <main>
        @yield('admin_content')
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Cabinskuy. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Desktop dropdown functionality
            const dropdown = document.getElementById('quickActionsDropdown');
            if (dropdown) {
                const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');

                dropdownToggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    dropdownMenu.classList.toggle('show');
                    
                    let isExpanded = dropdownToggle.getAttribute('aria-expanded') === 'true';
                    dropdownToggle.setAttribute('aria-expanded', !isExpanded);
                });

                document.addEventListener('click', function(event) {
                    if (!dropdown.contains(event.target)) {
                        dropdownMenu.classList.remove('show');
                        dropdownToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Mobile menu functionality
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMenu = document.getElementById('mobileMenu');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    mobileMenu.classList.toggle('show');
                    
                    // Toggle hamburger icon
                    const icon = mobileMenuToggle.querySelector('i');
                    if (mobileMenu.classList.contains('show')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.remove('show');
                        const icon = mobileMenuToggle.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });

                // Close mobile menu when clicking on a link
                const mobileMenuLinks = mobileMenu.querySelectorAll('a:not(.logout-link)');
                mobileMenuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.remove('show');
                        const icon = mobileMenuToggle.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    });
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>