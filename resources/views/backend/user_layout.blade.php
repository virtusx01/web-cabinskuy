<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul halaman akan dinamis, dengan fallback default --}}
    <title>{{ $title ?? 'Cabinskuy' }}</title>

    {{-- Font Awesome untuk ikon sosial (opsional, tapi disarankan untuk footer) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* General Body & Container */
        :root {
            --primary-green: #229954;
            --dark-green: #1c7d43;
            --light-green: #d6ffd8;
            --darkest-green: #223324;
            --text-color-dark: #333;
            --text-color-light: #e9f5e9;
            --link-color: #333;
            --link-hover-color: var(--primary-green);
            --header-height: 70px; /* Define header height as a variable */
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding-top: var(--header-height); /* Use variable for padding */
            box-sizing: border-box;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex-grow: 1; /* Memastikan main content mengisi ruang yang tersedia */
        }

        .container {
            width: 100%;
            max-width: 1100px; /* Good for large screens */
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem; /* Use rem for consistent padding */
            padding-right: 1rem;
        }

        /* Navbar Styles */
        .navbar {
            background-color: var(--light-green);
            padding: 0.75rem 0; /* Adjust padding with rem */
            width: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            height: var(--header-height); /* Set a fixed height for the navbar */
            align-items: center; /* Vertically center content in navbar */
        }
        .navbar-container {
            width: 100%;
            max-width: 1100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem; /* Use rem for padding */
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 0.5rem; /* Use gap for spacing between icon and text */
        }
        .navbar-logo-icon-container {
            width: 2.5rem; /* Use rem */
            height: 2.5rem; /* Use rem */
            border-radius: 6px;
            background-image: url('/backend/images/icon-cabinskuy.jpg');
            background-size: cover; /* Use cover to ensure image fills, or 100% 100% for exact fit */
            background-repeat: no-repeat;
            background-position: center;
        }
        .navbar-logo-text {
            font-size: 1.25rem; /* Use rem for font size */
            font-weight: bold;
            color: var(--primary-green);
        }

        /* Navbar Menu (links and auth) */
        .navbar-menu {
            display: flex;
            flex-grow: 1; /* Allows it to take available space */
            justify-content: flex-end; /* Pushes content to the right */
            align-items: center;
            gap: 1.5rem; /* Space between links and auth */
        }

        .navbar-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 1.5rem; /* Use gap for spacing between links */
        }
        .navbar-links a {
            text-decoration: none;
            color: var(--link-color);
            font-size: 0.9rem; /* Use rem */
            font-weight: 500;
            padding: 0.3rem 0; /* Adjust padding with rem */
            transition: color 0.3s;
        }
        .navbar-links a:hover,
        .navbar-links a.active {
            color: var(--primary-green);
        }
        .navbar-auth {
            display: flex;
            align-items: center;
            margin-left: 9rem;
            gap: 1rem;
        }
        .navbar-auth a {
            text-decoration: none;
            color: var(--link-color);
            font-size: 0.9rem; /* Use rem */
            font-weight: 500;
            padding: 0.5rem 0;
            transition: color 0.3s;
        }
        .navbar-auth a:hover {
            color: var(--primary-green);
        }
        .navbar-auth .profile-info {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--link-color);
            padding: 0.3rem 0.75rem; /* Adjust padding with rem */
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        .navbar-auth .profile-info:hover {
            background-color: rgba(34, 153, 84, 0.1);
            color: var(--primary-green);
        }
        .navbar-auth .profile-picture {
            width: 2.25rem; /* Use rem */
            height: 2.25rem; /* Use rem */
            border-radius: 50%;
            margin-right: 0.5rem; /* Use rem */
            background-color: #ccc;
            background-size: cover;
            background-position: center;
            border: 1px solid var(--primary-green);
        }
        .navbar-auth .profile-name {
            font-weight: 500;
            font-size: 0.9rem; /* Use rem */
        }
        .navbar-auth .btn-register {
            background-color: var(--primary-green);
            color: white !important;
            padding: 0.5rem 1.125rem; /* Adjust padding with rem */
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.3s;
            margin-left: 1rem; /* Adjust margin with rem */
        }
        .navbar-auth .btn-register:hover {
            background-color: var(--dark-green);
            color: white;
        }
        .navbar-auth .btn-logout {
            background: none;
            border: none; /* Changed from 0px solid */
            color: #ff0000;
            padding: 0.5rem 1rem; /* Adjust padding with rem */
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem; /* Use rem */
            cursor: pointer;
            margin-left: 1rem; /* Adjust margin with rem */
            transition: all 0.3s ease;
        }

        .navbar-auth .btn-logout:hover {
            color: rgb(255, 0, 0);
            box-shadow: 0 2px 8px rgba(34, 153, 84, 0.2);
        }

        /* Language Switcher */
        .navbar-lang-switcher {
            position: fixed;
            top: 1.25rem; /* Use rem */
            right: 1.25rem; /* Use rem */
            z-index: 1000;
            background: rgba(255, 255, 255, 0); /* Still transparent */
            backdrop-filter: blur(10px);
            border-radius: 25px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0.25rem; /* Small padding around buttons */
        }

        .lang-btn {
            background: none;
            border: none;
            padding: 0.3rem 0.6rem; /* Adjust padding with rem */
            margin: 0 0.125rem; /* Adjust margin with rem */
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.75rem; /* Use rem */
            font-weight: 500;
            transition: all 0.3s ease;
            color: #666;
        }

        .lang-btn.active {
            background: linear-gradient(135deg, var(--primary-green), #27a65f);
            color: white;
            transform: scale(1.05);
        }

        .lang-btn:hover:not(.active) {
            background: #f0f0f0;
            transform: scale(1.02);
        }

        /* Hamburger Icon */
        .navbar-burger {
            display: none; /* Hidden by default on larger screens */
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
            height: 3rem; /* Aligned with navbar height for clickability */
            width: 3rem;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 0.3rem; /* Space between lines */
        }

        .navbar-burger span {
            display: block;
            width: 1.5rem; /* Length of the lines */
            height: 2px;
            background-color: var(--text-color-dark);
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }

        /* Animation for hamburger to 'x' */
        .navbar-burger.is-active span:nth-child(1) {
            transform: translateY(0.5rem) rotate(45deg);
        }
        .navbar-burger.is-active span:nth-child(2) {
            opacity: 0;
        }
        .navbar-burger.is-active span:nth-child(3) {
            transform: translateY(-0.5rem) rotate(-45deg);
        }


        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar {
                height: auto; /* Allow navbar height to adjust on mobile */
                padding-bottom: 0.75rem; /* Add some padding at the bottom */
                align-items: flex-start; /* Align items to top for mobile menu */
            }
            .navbar-container {
                flex-wrap: wrap; /* Allow items to wrap */
                justify-content: space-between; /* Space out logo and burger */
                align-items: center; /* Vertically center logo and burger */
            }

            .navbar-menu {
                display: none; /* Hidden by default on small screens */
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
                margin-top: 0.75rem; /* Adjust margin with rem */
                gap: 0.75rem; /* Adjust gap for vertical links */
                /* Added for overall alignment within the menu on mobile */
                box-sizing: border-box; /* Ensure padding/border don't overflow */
                padding: 0 1rem; /* Add padding to align content with navbar-container */
            }

            .navbar-menu.is-active {
                display: flex; /* Show when active */
                /* Adjust flex properties to position lang-switcher to the right */
                flex-direction: column; /* Keep vertical stacking */
                align-items: flex-start; /* Keep main content left-aligned */
            }

            .navbar-burger {
                display: flex; /* Show hamburger on small screens */
            }

            .navbar-links {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
                gap: 0.75rem; /* Adjust gap for vertical links */
                margin-top: 0.5rem; /* Space from menu top */
            }
            .navbar-links li {
                width: 100%; /* Make links full width for better touch */
            }
            .navbar-links a {
                padding: 0.5rem 0; /* Larger touch area */
                display: block; /* Make links block level */
            }
            .navbar-auth {
                flex-direction: column; /* Stack profile info and logout */
                align-items: flex-start; /* Left-align items */
                width: 100%;
                margin-top: 0.75rem; /* Adjust margin with rem */
                gap: 0.75rem; /* Adjust gap for vertical auth elements */
                /* Remove any justify-content that would push to center/right */
            }
            .navbar-auth a,
            .navbar-auth form {
                margin-left: 0;
                border-left: none;
                padding-left: 0;
                /* width: 100%; */ /* Remove this if you want them to shrink to content width */
                /* If you want them to take full width and still be left-aligned, keep it. */
                /* Based on the image, they occupy less than 100% width, so removing this for profile-info and btn-logout is better */
            }
            /* Specific adjustment for profile-info and logout button within navbar-auth */
            .navbar-auth .profile-info {
                width: auto; /* Allow content to dictate width */
            }
            .navbar-auth .btn-logout {
                width: auto; /* Allow content to dictate width */
                margin-left: 0; /* Ensure no left margin */
            }


            .navbar-lang-switcher {
                /* Changed positioning to be part of the flow and align right within navbar-menu */
                position: static; /* Important: Make it part of the normal document flow */
                width: 100%; /* Take full width of parent to use flex for alignment */
                display: flex;
                justify-content: flex-end; /* Push content to the right */
                align-items: center;
                padding: 0; /* No padding needed here, handled by internal buttons */
                margin-top: 1rem; /* Space from elements above it */
                margin-left: 0; /* Ensure no centering margin */
            }

            .lang-btn {
                /* Keep existing styles */
            }
        }

        /* Further adjustment for very small screens if needed, mostly handled by 768px now */
        @media (max-width: 576px) {
            .footer-column {
                flex-basis: 100%;
                text-align: center; /* Center content for stacked columns */
            }
            .footer-column h4::after {
                left: 50%;
                transform: translateX(-50%); /* Center the underline */
            }
            .footer-socials {
                justify-content: center; /* Center social icons */
            }
            .footer-links ul {
                text-align: center; /* Center list items */
            }
            .footer-links a:hover {
                padding-left: 0; /* Remove padding animation if centered */
            }
        }


        .footer {
            background-color: var(--darkest-green);
            color: var(--text-color-light);
            padding: 3rem 0 0; /* Use rem for padding */
            text-align: left;
            margin-top: auto; /* Push footer to the bottom */
        }
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1.5rem; /* Use rem for gap */
            padding-bottom: 2rem; /* Use rem for padding */
        }
        .footer-column {
            flex: 1; /* Allow columns to grow and shrink */
            min-width: 180px; /* A bit smaller min-width for more flexibility */
            margin-bottom: 1rem; /* Use rem */
            flex-basis: 22%; /* Distribute columns more evenly on wider screens (approx 4 columns) */
            /* This flex-basis value might need adjustment based on desired column count per row */
            /* For 3 columns, flex-basis: 30%; for 2 columns, flex-basis: 48%; */
        }
        /* Override for about column if it needs more space */
        .footer-about {
            flex-basis: 30%; /* Give about column more space, but keep it flexible */
            min-width: 250px; /* Ensure it's not too small */
        }
        @media (max-width: 992px) { /* Adjust for medium screens, e.g., two columns */
            .footer-column {
                flex-basis: 48%;
            }
            .footer-about {
                flex-basis: 100%; /* About column takes full width on smaller screens */
            }
        }

        .footer-column h4 {
            color: #fff;
            font-size: 1.05rem; /* Use rem */
            margin-bottom: 1.25rem; /* Use rem */
            position: relative;
        }
        .footer-column h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -0.5rem; /* Use rem */
            width: 2.5rem; /* Use rem */
            height: 2px;
            background-color: var(--primary-green);
        }
        .footer-logo {
            font-size: 1.5rem; /* Use rem */
            font-weight: bold;
            color: #ffffff;
            margin: 0 0 1rem 0; /* Use rem */
        }
        .footer-about p {
            font-size: 0.85rem; /* Use rem */
            line-height: 1.6;
            color: #ced4da;
            margin-bottom: 1rem; /* Use rem */
        }
        .footer-socials {
            display: flex; /* Ensure it's a flex container for alignment */
            /* Default: align-items: flex-start; for left align */
            /* When centered, justify-content: center; */
        }
        .footer-socials a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem; /* Use rem */
            height: 2.25rem; /* Use rem */
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 50%;
            text-decoration: none;
            margin-right: 0.625rem; /* Use rem */
            transition: background-color 0.3s, transform 0.3s;
        }
        .footer-socials a:hover {
            background-color: var(--primary-green);
            transform: scale(1.1);
        }
        .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links ul li {
            margin-bottom: 0.75rem; /* Use rem */
        }
        .footer-links a {
            color: #ced4da;
            text-decoration: none;
            transition: color 0.3s, padding-left 0.3s;
            font-size: 0.85rem; /* Use rem */
        }
        .footer-links a:hover {
            color: #fff;
            padding-left: 5px;
        }
        .footer-contact p {
            font-size: 0.85rem; /* Use rem */
            line-height: 1.7;
            color: #ced4da;
            margin: 0;
        }
        .footer-contact p i {
            margin-right: 0.5rem; /* Add spacing for icons */
        }
        .footer-bottom {
            border-top: 1px solid #445546;
            text-align: center;
            padding: 1.25rem 0; /* Use rem */
        }
        .footer-bottom p {
            margin: 0;
            font-size: 0.85rem; /* Use rem */
            color: #ced4da;
        }

        /* Loading Animation - Removed from applyTranslations, but kept if other animations use it */
        .loading {
            /* opacity: 0.5; */ /* Commented out for instant change */
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        /* Animation Classes - Kept for other elements if used */
        .fade-in {
            /* animation: fadeIn 0.6s ease-out; */ /* Commented out for instant change */
            opacity: 1 !important; /* Ensure it's fully visible instantly */
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body>

    <header class="navbar">
        <div class="navbar-container">
            <a href="{{ url('/') }}" class="navbar-logo">
                <div class="navbar-logo-icon-container"></div>
                <span class="navbar-logo-text" data-translate="navbar_logo">CABINSKUY</span>
            </a>

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
                        {{-- CONDITIONAL ROUTING FOR PROFILE LINK --}}
                        @if(Auth::user()->isCustomer())
                            <a href="{{ route('profile.user.edit') }}" class="profile-info {{ request()->routeIs('profile.user.*') ? 'active' : '' }}">
                                <div class="profile-picture"
                                     style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset('backend/images/default-avatar.png') }}');">
                                </div>
                                <span class="profile-name">{{ Auth::user()->name }}</span>
                            </a>
                        @else
                            {{-- For admin/superadmin, keep current showProfile or redirect to admin dashboard --}}
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
                {{-- Language Switcher - Moved inside navbar-menu for mobile --}}
                <div class="navbar-lang-switcher">
                    <button class="lang-btn" data-lang="en">EN</button>
                    <button class="lang-btn" data-lang="id">ID</button>
                </div>
            </div>
        </div>
    </header>

    <main>
        {{-- Konten dari halaman spesifik akan dirender di sini --}}
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-column footer-about"> {{-- Removed fixed flex-basis here, will handle via media queries for more dynamic behavior --}}
                    <h3 class="footer-logo" data-translate="footer_logo">CABINSKUY</h3>
                    <p data-translate="footer_about_desc">Temukan ketenangan dan kemewahan di tengah alam. Cabinskuy menyediakan pengalaman menginap di kabin yang tak terlupakan dengan fasilitas terbaik.</p>
                    <div class="footer-socials">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-column footer-links">
                    <h4 data-translate="footer_nav_title">Navigasi</h4>
                    <ul>
                        <li><a href="{{ url('/') }}" data-translate="nav_home_footer">Home</a></li>
                        <li><a href="{{ url('/kabin') }}" data-translate="nav_cabin_footer">Kabin</a></li>
                        {{-- Removed About Us, Blog, and Contact from footer --}}
                        <li><a href="{{ route('frontend.booking.index') }}" data-translate="nav_my_bookings_footer">My Bookings</a></li> {{-- Added My Bookings to footer --}}
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
                    <p><i class="fas fa-phone"></i> <span data-translate="footer_phone">(022) 123-4567</span></p>
                    <p><i class="fas fa-envelope"></i> <span data-translate="footer_email">halo@cabinskuy.com</span></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p data-translate="footer_copyright">&copy; {{ date('Y') }} Cabinskuy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Translation data
        const translations = {
            en: {
                // Navbar
                navbar_logo: "CABINSKUY",
                nav_home: "Home",
                // nav_about: "About", // Removed
                nav_cabin: "Cabin",
                // nav_blog: "Blog", // Removed
                nav_my_bookings: "My Bookings",
                login_btn: "Log in",
                register_btn: "Register",
                logout_btn: "Logout",

                // Footer (re-check keys for uniqueness and clarity)
                footer_logo: "CABINSKUY",
                footer_about_desc: "Discover tranquility and luxury amidst nature. Cabinskuy offers unforgettable cabin stays with the best facilities.",
                footer_nav_title: "Navigation",
                nav_home_footer: "Home", // Specific key for footer home if text differs
                nav_cabin_footer: "Cabin", // Specific key for footer cabin if text differs
                nav_my_bookings_footer: "My Bookings", // Added for footer
                // nav_blog_footer: "Blog", // Removed
                // footer_nav_about_us: "About Us", // Removed
                // footer_nav_contact: "Contact", // Removed
                footer_help_title: "Help",
                footer_faq: "FAQ",
                footer_terms: "Terms & Conditions",
                footer_privacy: "Privacy Policy",
                footer_contact_title: "Contact Us",
                footer_address: "Jl. Alam Asri No. 123,<br>Bandung, Indonesia",
                footer_phone: "(022) 123-4567",
                footer_email: "halo@cabinskuy.com",
                footer_copyright: "© {{ date('Y') }} Cabinskuy. All rights reserved.",

                // Homepage Content (from your provided example for reference)
                hero_title: "Live Out The Adventure With Cabinskuy",
                hero_description: "Discover unique cabin stays nestled in nature. Your perfect getaway for tranquility and adventure awaits. Book your escape today!",
                search_title: "Good Morning! Where Do You Want To Stay?",
                location_label: "Location:",
                all_locations: "All Locations",
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
                step4_desc: "Arrive at your cabin, check-in seamlessly, and start your unforgettable getaway."
            },
            id: {
                // Navbar
                navbar_logo: "CABINSKUY",
                nav_home: "Beranda",
                // nav_about: "Tentang", // Removed
                nav_cabin: "Kabin",
                // nav_blog: "Blog", // Removed
                nav_my_bookings: "Pemesanan Saya",
                login_btn: "Masuk",
                register_btn: "Daftar",
                logout_btn: "Keluar",

                // Footer
                footer_logo: "CABINSKUY",
                footer_about_desc: "Temukan ketenangan dan kemewahan di tengah alam. Cabinskuy menyediakan pengalaman menginap di kabin yang tak terlupakan dengan fasilitas terbaik.",
                footer_nav_title: "Navigasi",
                nav_home_footer: "Beranda", // Specific key for footer home if text differs
                nav_cabin_footer: "Kabin", // Specific key for footer cabin if text differs
                nav_my_bookings_footer: "Pemesanan Saya", // Added for footer
                // nav_blog_footer: "Blog", // Removed
                // footer_nav_about_us: "Tentang Kami", // Removed
                // footer_nav_contact: "Kontak", // Removed
                footer_help_title: "Bantuan",
                footer_faq: "FAQ",
                footer_terms: "Syarat & Ketentuan",
                footer_privacy: "Kebijakan Privasi",
                footer_contact_title: "Hubungi Kami",
                footer_address: "Jl. Alam Asri No. 123,<br>Bandung, Indonesia",
                footer_phone: "(022) 123-4567",
                footer_email: "halo@cabinskuy.com",
                footer_copyright: "© {{ date('Y') }} Cabinskuy. Hak Cipta Dilindungi.",

                // Homepage Content (from your provided example for reference)
                hero_title: "Wujudkan Petualangan Bersama Cabinskuy",
                hero_description: "Temukan penginapan kabin unik yang tersembunyi di alam. Liburan sempurna Anda untuk ketenangan dan petualangan menanti. Pesan pelarian Anda hari ini!",
                search_title: "Selamat Pagi! Dimana Anda Ingin Menginap?",
                location_label: "Lokasi:",
                all_locations: "Semua Lokasi",
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
                step4_desc: "Tiba di kabin Anda, check-in dengan lancar, dan mulai liburan yang tak terlupakan."
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
                return 'US';
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
        }

        // Update guest select options (specific function as it's dynamic)
        function updateGuestOptions(language) {
            const guestSelect = document.getElementById('guests');
            if (guestSelect) {
                const guestText = translations[language].guest || 'Guest';
                const guestsText = translations[language].guests || 'Guests';
                const currentValue = guestSelect.value;

                guestSelect.innerHTML = `
                    <option value="1">1 ${guestText}</option>
                    <option value="2">2 ${guestsText}</option>
                    <option value="3">3 ${guestsText}</option>
                    <option value="4">4 ${guestsText}</option>
                `;
                guestSelect.value = currentValue;
            }
        }

        // Set default language on page load
        async function setDefaultLanguageOnLoad() {
            let initialLang = localStorage.getItem('preferred_language'); // Check local storage first

            if (!initialLang) {
                const countryCode = await detectUserCountry();
                initialLang = (countryCode === 'ID') ? 'id' : 'en';
                localStorage.setItem('preferred_language', initialLang); // Store for future visits
            }
            console.log('Initial language determined:', initialLang);
            applyTranslations(initialLang);

            // Update active state of language buttons after applying initial translations
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.lang === initialLang) {
                    btn.classList.add('active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
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
                });
            }


            // Tab functionality (from your provided code)
            const tabs = document.querySelectorAll('.cabin-types-tabs .tab-link');
            const tabContents = document.querySelectorAll('.cabin-types-section .tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(item => item.classList.remove('active'));
                    tabContents.forEach(content => {
                        content.classList.remove('active');
                        content.style.display = 'none'; // Hide explicitly before showing
                    });

                    this.classList.add('active');
                    const targetContentId = this.getAttribute('data-tab');
                    const targetContent = document.getElementById(targetContentId);

                    setTimeout(() => {
                        targetContent.style.display = 'block'; // Show explicitly for animation
                        targetContent.classList.add('active', 'slide-up');
                        setTimeout(() => {
                            targetContent.classList.remove('slide-up');
                        }, 600);
                    }, 100);
                });
            });

            // Date input min/max logic (from your provided code)
            const today = new Date().toISOString().split('T')[0];
            const checkinInput = document.getElementById('checkin');
            const checkoutInput = document.getElementById('checkout');

            if(checkinInput) {
                checkinInput.setAttribute('min', today);
                // checkinInput.value = today; // Uncomment if you want to set today as default checkin
            }
            if(checkoutInput) {
                checkoutInput.setAttribute('min', today);
                // const tomorrow = new Date(); // Uncomment if you want to set tomorrow as default checkout
                // tomorrow.setDate(tomorrow.getDate() + 1);
                // checkoutInput.value = tomorrow.toISOString().split('T')[0];
            }

            if(checkinInput && checkoutInput) {
                checkinInput.addEventListener('change', function() {
                    const checkinDate = new Date(this.value);
                    const minCheckoutDate = new Date(checkinDate);
                    minCheckoutDate.setDate(minCheckoutDate.getDate() + 1);

                    checkoutInput.setAttribute('min', minCheckoutDate.toISOString().split('T')[0]);
                    if (new Date(checkoutInput.value) <= checkinDate) {
                        checkoutInput.value = minCheckoutDate.toISOString().split('T')[0];
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
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.cabin-card, .booking-step').forEach(el => {
                observer.observe(el);
            });

            // Ripple effect (from your provided code)
            document.querySelectorAll('.btn-search, .btn-see-more').forEach(button => {
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
                    `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    @stack('scripts')
</body>
</html>