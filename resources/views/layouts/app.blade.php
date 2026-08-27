<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SeTiket — Platform pemesanan tiket event online. Temukan fun run, festival, seminar, dan pameran dengan mudah.">
    <title>@yield('title', 'SeTiket — Temukan & Pesan Tiket Event')</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/setiket.webp') }}">

    {{-- Fonts: Poppins (Space Dynamic) + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Animated CSS (Space Dynamic WOW.js) --}}
    <link rel="stylesheet" href="{{ asset('vendor/space-dynamic/css/animated.css') }}">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="{{ asset('vendor/space-dynamic/css/fontawesome.css') }}">

    {{-- Bootstrap (Space Dynamic base) --}}
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">

    {{-- App CSS (Tailwind + Space Dynamic tokens) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>

    {{-- ===== PRELOADER ===== --}}
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <script>
        (function() {
            function dismissPreloader() {
                var p = document.getElementById('js-preloader');
                if (p && !p.classList.contains('loaded')) {
                    p.classList.add('loaded');
                }
            }
            window.addEventListener('load', dismissPreloader);
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(dismissPreloader, 500);
            });
            setTimeout(dismissPreloader, 800);
        })();
    </script>

    {{-- ===== HEADER / NAVBAR ===== --}}
    <header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">

                        {{-- Logo --}}
                        <a href="{{ route('home') }}" class="logo">
                            <img src="{{ asset('images/setiketbg.webp') }}" alt="SeTiket Logo" class="navbar-logo-img">
                        </a>

                        {{-- Menu --}}
                        <ul class="nav">
                            <li class="scroll-to-section">
                                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') && !request()->segment(2) ? 'active' : '' }}">Beranda</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="{{ route('home') }}#events">Event</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="{{ route('home') }}#featured">Pilihan</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="https://wa.me/6289681201941" target="_blank" rel="noopener">Bantuan</a>
                            </li>

                            @auth
                                @if(auth()->user()->isUser())
                                    <li class="scroll-to-section">
                                        <a href="{{ route('tickets') }}" style="color:#2563eb;font-weight:600;">Tiket Saya</a>
                                    </li>
                                @else
                                    <li class="scroll-to-section">
                                        <a href="{{ route('admin.dashboard') }}" style="color:#2563eb;font-weight:600;">Panel Admin</a>
                                    </li>
                                @endif

                                {{-- Profile dropdown (DESKTOP) --}}
                                <li class="nav-profile desktop-only-nav" style="position:relative;padding-left:16px!important;">
                                    <a href="#" id="profileTrigger" onclick="toggleProfile(event)"
                                        style="display:flex;align-items:center;gap:8px;height:40px;line-height:1!important;color:#2a2a2a;">
                                        <span class="profile-avatar" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg, #2563eb, #7c3aed);color:#fff;font-weight:700;font-size:14px;">
                                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                        <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                        </svg>
                                    </a>
                                    <div id="profileDropdown" class="profile-dropdown">
                                        <div class="profile-header">
                                            <p style="font-weight:600;color:#111827;font-size:14px;margin:0;line-height:1.2;">{{ auth()->user()->name }}</p>
                                            <p style="color:#6b7280;font-size:12px;margin:5px 0 0;line-height:1.2;">{{ auth()->user()->email }}</p>
                                        </div>
                                        @if(auth()->user()->isUser())
                                            <a href="{{ route('dashboard') }}">Pesanan Saya</a>
                                            <a href="{{ route('tickets') }}">Tiket Saya</a>
                                        @endif
                                        <a href="{{ route('profile.edit') }}">Profil</a>
                                        <div style="height:1px;background:#f0f0f0;margin:4px 0;"></div>
                                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="logout-btn">Keluar</button>
                                        </form>
                                    </div>
                                </li>
                            @else
                                <li class="scroll-to-section desktop-only-nav">
                                    <a href="{{ route('login') }}">Masuk</a>
                                </li>
                                <li class="nav-cta desktop-only-nav">
                                    <div class="main-red-button">
                                        <a href="{{ route('register') }}">Daftar Sekarang</a>
                                    </div>
                                </li>
                            @endauth

                            {{-- Mobile auth (HANYA MOBILE) --}}
                            <li class="nav-auth-mobile mobile-only-nav !p-0 !border-none" style="display:none;">
                                @auth
                                    <div class="flex items-center gap-3 px-5 py-3.5 bg-slate-50 border-b border-slate-200">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-purple-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-900 text-sm m-0 leading-tight">{{ auth()->user()->name }}</p>
                                            <p class="text-slate-500 text-xs m-0 leading-tight truncate">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                    @if(auth()->user()->isUser())
                                        <a href="{{ route('dashboard') }}" class="block px-5 py-3 text-slate-700 text-sm font-medium border-b border-slate-100 hover:bg-slate-50 hover:text-orange-600 !h-auto !leading-normal !flex-none">Pesanan Saya</a>
                                        <a href="{{ route('tickets') }}" class="block px-5 py-3 text-slate-700 text-sm font-medium border-b border-slate-100 hover:bg-slate-50 hover:text-orange-600 !h-auto !leading-normal !flex-none">Tiket Saya</a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}" class="block px-5 py-3 text-slate-700 text-sm font-medium border-b border-slate-100 hover:bg-slate-50 hover:text-orange-600 !h-auto !leading-normal !flex-none">Profil</a>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-5 py-3 text-red-500 text-sm font-semibold border-t border-slate-200 hover:bg-red-50 bg-transparent m-0 !h-auto !leading-normal">Keluar</button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="block px-5 py-3 text-blue-600 font-medium border-b border-slate-100 !h-auto !leading-normal">Masuk</a>
                                    <a href="{{ route('register') }}" class="block px-5 py-3 text-orange-600 font-medium !h-auto !leading-normal">Daftar Sekarang</a>
                                @endauth
                            </li>
                        </ul>

                        <a class="menu-trigger" id="menuTrigger">
                            <span>Menu</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== KONTEN UTAMA ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="footer-sd wow fadeIn" data-wow-duration="1s" data-wow-delay="0.25s">
        <div class="container">
            <div class="row">

                {{-- Brand --}}
                <div class="col-lg-4 col-md-6 mb-4 sm:mb-5">
                    <div class="footer-brand">
                        <img src="{{ asset('images/setiket.webp') }}" alt="SeTiket" onerror="this.style.display='none'">
                        <span class="brand-name">SeTiket</span>
                    </div>
                    <p class="footer-desc">
                        Temukan event seru dan kebutuhan event favorit Anda
                    </p>
                </div>

                {{-- Jelajahi --}}
                <div class="col-lg-2 col-md-3 col-6 mb-4 sm:mb-5">
                    <h5>Jelajahi</h5>
                    <ul>
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('home') }}#events">Semua Event</a></li>
                        <li><a href="{{ route('home') }}#featured">Event Pilihan</a></li>
                    </ul>
                </div>

                {{-- Akun --}}
                <div class="col-lg-2 col-md-3 col-6 mb-4 sm:mb-5">
                    <h5>Akun</h5>
                    <ul>
                        @auth
                            @if(auth()->user()->isUser())
                                <li><a href="{{ route('dashboard') }}">Pesanan Saya</a></li>
                                <li><a href="{{ route('tickets') }}">Tiket Saya</a></li>
                            @endif
                            <li><a href="{{ route('profile.edit') }}">Profil</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Masuk</a></li>
                            <li><a href="{{ route('register') }}">Daftar</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- Bantuan --}}
                <div class="col-lg-2 col-md-6 mb-4 sm:mb-5">
                    <h5>Bantuan</h5>
                    <ul>
                        <li><a href="https://wa.me/6289681201941" target="_blank" rel="noopener">Hubungi Kami</a></li>
                        <li><a href="https://wa.me/6289681201941" target="_blank" rel="noopener">Pusat Bantuan</a></li>
                    </ul>
                </div>

            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} SeTiket. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/space-dynamic/js/animation.js') }}"></script>
    <script src="{{ asset('vendor/space-dynamic/js/templatemo-custom.js') }}"></script>

    <style>
        /* Responsive Nav Visibility */
        @media (max-width: 767px) {
            .desktop-only-nav { display: none !important; }
            .mobile-only-nav { display: block !important; }
            .header-area .main-nav .nav li.mobile-only-nav { display: block !important; }
        }
        @media (min-width: 768px) {
            .mobile-only-nav { display: none !important; }
            .desktop-only-nav { display: block !important; }
            /* Specifically for list items in desktop header */
            .header-area .main-nav .nav li.desktop-only-nav { display: block !important; }
        }

        /* Profile Dropdown (Desktop) */
        .profile-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 5px);
            width: 220px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #f0f0f0;
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .profile-dropdown.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .profile-dropdown .profile-header {
            padding: 12px 20px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 4px;
            background: #f9fafb;
        }
        .profile-dropdown a, .profile-dropdown .logout-btn {
            display: block !important;
            padding: 8px 20px !important;
            color: #4b5563 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            line-height: 1.5 !important;
            height: auto !important;
            border: none !important;
            text-align: left;
            width: 100%;
            background: transparent;
            cursor: pointer;
            text-decoration: none !important;
            transition: all 0.2s;
        }
        .profile-dropdown a:hover {
            background: #f3f4f6 !important;
            color: #ea580c !important;
        }
        .profile-dropdown .logout-btn {
            color: #ef4444 !important;
        }
        .profile-dropdown .logout-btn:hover {
            background: #fef2f2 !important;
        }
    </style>

    <script>
        // ---- Profil dropdown ----
        function toggleProfile(e) {
            e.preventDefault();
            e.stopPropagation();
            var dd = document.getElementById('profileDropdown');
            if (dd) dd.classList.toggle('open');
        }
        document.addEventListener('click', function() {
            var dd = document.getElementById('profileDropdown');
            if (dd) dd.classList.remove('open');
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var dd = document.getElementById('profileDropdown');
                if (dd) dd.classList.remove('open');
            }
        });

        // ---- Initialize WOW.js Scroll Animation ----
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof WOW !== 'undefined') {
                new WOW().init();
            }
            var trigger = document.getElementById('menuTrigger');
            var nav = document.querySelector('.header-area .main-nav .nav');
            if (trigger && nav) {
                trigger.addEventListener('click', function() {
                    this.classList.toggle('active');
                    nav.classList.toggle('open');
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
