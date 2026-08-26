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

                                {{-- Profile dropdown --}}
                                <li class="nav-profile" style="position:relative;padding-left:16px!important;">
                                    <a href="#" id="profileTrigger" onclick="toggleProfile(event)"
                                        style="display:flex;align-items:center;gap:8px;height:40px;line-height:1!important;color:#2a2a2a;">
                                        <span class="profile-avatar">
                                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                        <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                        </svg>
                                    </a>
                                    <div id="profileDropdown" class="profile-dropdown">
                                        <div class="profile-header">
                                            <p style="font-weight:600;color:#111827;font-size:14px;">{{ auth()->user()->name }}</p>
                                            <p style="color:#6b7280;font-size:12px;">{{ auth()->user()->email }}</p>
                                        </div>
                                        @if(auth()->user()->isUser())
                                            <a href="{{ route('dashboard') }}">Pesanan Saya</a>
                                            <a href="{{ route('tickets') }}">Tiket Saya</a>
                                        @endif
                                        <a href="{{ route('profile.edit') }}">Profil</a>
                                        <div class="profile-sep"></div>
                                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="logout-btn">Keluar</button>
                                        </form>
                                    </div>
                                </li>
                            @else
                                <li class="scroll-to-section">
                                    <a href="{{ route('login') }}">Masuk</a>
                                </li>
                                <li class="nav-cta">
                                    <div class="main-red-button">
                                        <a href="{{ route('register') }}">Daftar Sekarang</a>
                                    </div>
                                </li>
                            @endauth

                            {{-- Mobile auth (hanya di mobile nav) --}}
                            <li class="nav-auth-mobile" style="display:none;">
                                @auth
                                    <div class="mobile-user-info">
                                        <div class="mobile-user-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
                                        <div class="mobile-user-detail">
                                            <p class="mobile-user-name">{{ auth()->user()->name }}</p>
                                            <p class="mobile-user-email">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                    @if(auth()->user()->isUser())
                                        <a href="{{ route('dashboard') }}" class="mobile-nav-link">Pesanan Saya</a>
                                        <a href="{{ route('tickets') }}" class="mobile-nav-link">Tiket Saya</a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}" class="mobile-nav-link">Profil</a>
                                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="mobile-logout-btn">Keluar</button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" style="color:#2563eb;">Masuk</a>
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
