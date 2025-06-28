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
            <nav class="navbar-auth">
                @auth
                    <a href="#" class="profile-info">
                        <div class="profile-picture" style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset('backend/images/default-avatar.png') }}');"></div>
                        <span class="profile-name">
                            {{-- Tambahkan logika untuk menampilkan nama berdasarkan role --}}
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

    @stack('scripts')
</body>
</html>