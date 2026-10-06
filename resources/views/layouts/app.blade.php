<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SeTiket">
    <meta name="application-name" content="SeTiket">
    <meta name="msapplication-TileColor" content="#0050ff">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#ffffff" id="metaThemeColor">
    <meta name="description" content="SeTiket — Platform pemesanan tiket event & konser terpercaya di Indonesia. Temukan fun run, festival musik, seminar, dan pameran dengan mudah.">
    <title>@yield('title', 'SeTiket | Platform Beli Tiket Konser & Event Terpercaya di Indonesia')</title>

    {{-- Favicon & App Icons (Support Semua Browser: Chrome, Safari, Edge, Firefox, iOS & Android WebApp) --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=2">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=2">

    {{-- Fonts: Plus Jakarta Sans & Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- App CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Init theme before render to prevent flash
        const isDarkTheme = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDarkTheme) {
            document.documentElement.classList.add('dark');
            const metaTheme = document.getElementById('metaThemeColor');
            if (metaTheme) metaTheme.setAttribute('content', '#0b0f19');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen flex flex-col bg-white dark:bg-[#0b0f19] text-gray-900 dark:text-gray-100 antialiased transition-colors duration-200">

    {{-- ===== NAVBAR LOKET STYLE ===== --}}
    <header class="sticky top-0 z-50 bg-white dark:bg-[#0f172a] border-b border-gray-100 dark:border-slate-800/80 shadow-[0_2px_12px_rgba(0,0,0,0.03)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.35)] transition-colors duration-200" style="position: -webkit-sticky; position: sticky; top: 0; z-index: 50;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[72px] gap-2 sm:gap-4">

                {{-- Left: Logo --}}
                <div class="flex items-center gap-3 sm:gap-6 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('images/setiketbg.webp') }}" alt="SeTiket Logo" class="h-9 sm:h-10 w-auto object-contain transition-transform group-hover:scale-105" onerror="this.src='{{ asset('images/setiket.webp') }}'">
                    </a>
                </div>

                {{-- Center: Search Bar (Loket Pill Search - Hidden on mobile per request) --}}
                <div class="hidden sm:block flex-1 max-w-xl mx-2 sm:mx-6">
                    <form action="{{ route('home') }}" method="GET" class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari event SOUNDCHECK, YE JAKARTA, MARATHON..."
                            class="w-full bg-[#f3f5f8] dark:bg-slate-800 hover:bg-[#ebedf2] dark:hover:bg-slate-700/70 focus:bg-white dark:focus:bg-slate-800 border border-transparent dark:border-slate-700 focus:border-[#0050ff] rounded-full py-2.5 pl-4 sm:pl-5 pr-11 text-xs sm:text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-3 focus:ring-[#0050ff]/15 transition-all">
                        <button type="submit" aria-label="Cari"
                            class="absolute right-1.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-[#0050ff] dark:hover:text-blue-400 hover:bg-white dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z"/>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Right: Theme Toggle + User Auth --}}
                <div class="flex items-center gap-2 sm:gap-3.5 shrink-0">
                    {{-- Theme Toggle Button (Dark / Light Mode) --}}
                    <button type="button" id="themeToggleBtn" onclick="toggleTheme()" aria-label="Ganti Mode Tema"
                        class="p-2 sm:p-2.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-amber-400 hover:text-amber-500 dark:hover:text-amber-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all focus:outline-none shadow-2xs">
                        {{-- Sun icon (visible in dark mode) --}}
                        <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14l-1.41 1.41"/>
                        </svg>
                        {{-- Moon icon (visible in light mode) --}}
                        <svg class="w-4 h-4 block dark:hidden text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
                        </svg>
                    </button>

                    @auth
                        @if(auth()->user()->isUser())
                            <a href="{{ route('tickets') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#0050ff] hover:text-[#0043d4] px-3 py-1.5 rounded-xl hover:bg-blue-50/70 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z" />
                                </svg>
                                <span>Tiket Saya</span>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#0050ff] hover:text-[#0043d4] px-3 py-1.5 rounded-xl hover:bg-blue-50/70 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                                </svg>
                                <span>Panel Admin</span>
                            </a>
                        @endif

                        {{-- Profile trigger --}}
                        <div class="relative" id="profileMenuContainer">
                            <button type="button" id="profileTrigger" onclick="toggleProfileMenu(event)"
                                class="flex items-center gap-2 p-1.5 rounded-full hover:bg-gray-100 transition-colors focus:outline-none">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#0050ff] to-blue-400 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:inline text-xs font-semibold text-gray-800 max-w-[100px] truncate">
                                    {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown Profile (Menggunakan SVG Icons) --}}
                            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-[#1e293b] rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 p-2 z-50">
                                <div class="px-3 py-2 border-b border-gray-100 dark:border-slate-700 mb-1">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                @if(auth()->user()->isUser())
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-700 hover:text-[#0050ff] dark:hover:text-blue-400 transition-colors">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.5 9.4-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>
                                        <span>Pesanan Saya</span>
                                    </a>
                                    <a href="{{ route('tickets') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-700 hover:text-[#0050ff] dark:hover:text-blue-400 transition-colors">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z"/></svg>
                                        <span>Tiket Saya</span>
                                    </a>
                                @else
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-700 hover:text-[#0050ff] dark:hover:text-blue-400 transition-colors">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span>Panel Pengelola</span>
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-700 hover:text-[#0050ff] dark:hover:text-blue-400 transition-colors">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <span>Pengaturan Akun</span>
                                </a>
                                <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Guest: Loket Masuk / Akun button (Hidden on mobile, placed inside hamburger menu) --}}
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-[#0050ff] dark:hover:text-blue-400 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <span>Masuk</span>
                        </a>

                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center px-4 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-bold text-white bg-[#0050ff] hover:bg-[#0043d4] rounded-full shadow-[0_4px_12px_rgba(0,80,255,0.25)] transition-all transform hover:-translate-y-0.5">
                            Daftar
                        </a>
                    @endauth

                    {{-- Mobile menu button --}}
                    <button type="button" id="mobileMenuBtn" onclick="toggleMobileMenu()"
                        class="md:hidden p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 focus:outline-none transition-colors border border-gray-200/70 dark:border-slate-700/70" aria-label="Menu Navigasi">
                        <svg class="w-5 h-5" id="mobileMenuIconClosed" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg class="w-5 h-5 hidden" id="mobileMenuIconOpen" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile dropdown menu (Desain Bersih & Elegan, Tanpa Emote) --}}
        <div id="mobileMenu" class="hidden md:hidden border-t border-gray-100 dark:border-slate-800/80 bg-white/95 dark:bg-[#0f172a]/95 backdrop-blur-xl px-4 py-4 space-y-4 shadow-xl">
            @auth
                {{-- User Profile Card --}}
                <div class="bg-gradient-to-r from-blue-50/80 to-indigo-50/40 dark:from-slate-800/80 dark:to-slate-800/40 border border-blue-100/70 dark:border-slate-700/80 rounded-2xl p-3.5 flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0050ff] to-blue-400 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-900/40 text-[10px] font-bold text-blue-700 dark:text-blue-300 uppercase shrink-0">
                        {{ auth()->user()->isUser() ? 'Member' : 'Admin' }}
                    </span>
                </div>

                {{-- User Navigation Links --}}
                <div class="space-y-1">
                    @if(auth()->user()->isUser())
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-800 hover:text-[#0050ff] transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.5 9.4-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>
                            <span>Pesanan Saya</span>
                        </a>
                        <a href="{{ route('tickets') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-800 hover:text-[#0050ff] transition-colors">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z"/></svg>
                            <span>Tiket Saya</span>
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-800 hover:text-[#0050ff] transition-colors">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span>Panel Admin</span>
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50/70 dark:hover:bg-slate-800 hover:text-[#0050ff] transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Pengaturan Akun</span>
                    </a>
                </div>
            @else
                {{-- Guest: Action Banner Card (Masuk & Daftar dalam Garis 3) --}}
                <div class="bg-gradient-to-br from-blue-50/90 via-indigo-50/50 to-slate-50 dark:from-slate-800/90 dark:via-slate-800/50 dark:to-slate-900 border border-blue-100/70 dark:border-slate-700/80 rounded-2xl p-4 shadow-xs">
                    <div class="flex items-center gap-2 mb-1.5">
                        <svg class="w-4 h-4 text-[#0050ff] dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                        </svg>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white">Akses Akun SeTiket</h4>
                    </div>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-3.5 leading-relaxed">
                        Masuk untuk mengecek pesanan tiket atau daftar akun baru dalam hitungan detik.
                    </p>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 text-gray-800 dark:text-white font-bold text-xs border border-gray-200 dark:border-slate-600 shadow-xs transition-all active:scale-95">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                            </svg>
                            <span>Masuk</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-[#0050ff] hover:bg-[#0043d4] text-white font-bold text-xs shadow-sm hover:shadow-md transition-all active:scale-95">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.375 21c-2.331 0-4.512-.652-6.375-1.765Z"/>
                            </svg>
                            <span>Daftar</span>
                        </a>
                    </div>
                </div>
            @endauth

            {{-- Navigation Quick Links --}}
            <div class="border-t border-gray-100 dark:border-slate-800 pt-3 space-y-1">
                <a href="{{ route('home') }}#events" onclick="toggleMobileMenu()" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#0050ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                        </svg>
                        <span>Event Trending</span>
                    </span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>

                <a href="{{ route('home') }}#upcoming-events" onclick="toggleMobileMenu()" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        <span>Event yang Akan Datang</span>
                    </span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>

                <a href="{{ route('home') }}#ended-events" onclick="toggleMobileMenu()" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871a2.25 2.25 0 0 0-2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H8.625A2.25 2.25 0 0 0 6.375 6.75v5.25a2.25 2.25 0 0 0-2.25 2.25h-.871c-.621 0-1.125.504-1.125 1.125V18.75m15 0h-15"/>
                        </svg>
                        <span>Arsip Event Selesai</span>
                    </span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>

                <a href="https://wa.me/6289681201941" target="_blank" rel="noopener" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-.974-.94 5.942 5.942 0 0 1 .805-2.288A7.785 7.785 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                        </svg>
                        <span>Pusat Bantuan WhatsApp</span>
                    </span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25"/></svg>
                </a>
            </div>

            {{-- Mode Tampilan (Tanpa Emote - Hanya SVG Open Source) --}}
            <div class="border-t border-gray-100 dark:border-slate-800 pt-3 flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                    <span>Mode Tampilan</span>
                </div>
                <button type="button" onclick="toggleTheme()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-xs font-bold text-gray-700 dark:text-amber-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    {{-- Mode Terang Aktif: Tombol untuk mengaktifkan Gelap dengan icon bulan SVG --}}
                    <span class="flex items-center gap-1.5 dark:hidden text-slate-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
                        </svg>
                        <span>Gelap</span>
                    </span>
                    {{-- Mode Gelap Aktif: Tombol untuk mengaktifkan Terang dengan icon matahari SVG --}}
                    <span class="hidden dark:flex items-center gap-1.5 text-amber-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14l-1.41 1.41"/>
                        </svg>
                        <span>Terang</span>
                    </span>
                </button>
            </div>

            @auth
                {{-- Tombol Logout Khusus Auth --}}
                <div class="border-t border-gray-100 dark:border-slate-800 pt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                </div>
            @endauth
        </div>

        {{-- ===== LOKET RUNNING TICKER RIBBON (DENGAN SVG ICONS) ===== --}}
        <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white overflow-hidden py-2 select-none border-b border-indigo-950/40">
            <div class="loket-marquee-track text-[11px] sm:text-xs font-semibold tracking-wide">
                <span class="inline-flex items-center gap-6 px-4">
                    <span class="inline-flex items-center gap-1.5 text-amber-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                        TIKET RESMI & TERVERIFIKASI
                    </span>
                    <span class="text-white/40">•</span>
                    <span>SOUNDCHECK VOL. 1 NIGHT PARTY</span>
                    <span class="text-white/40">•</span>
                    <span>YE JAKARTA 2026 WORLD TOUR</span>
                    <span class="text-white/40">•</span>
                    <span>BIGBANG 2026-2027 WORLD TOUR RE-BOOT</span>
                    <span class="text-white/40">•</span>
                    <span>JAKARTA NIGHT MARATHON 2026</span>
                    <span class="text-white/40">•</span>
                    <span>E-TICKET DENGAN QR CODE INSTAN</span>
                    <span class="text-white/40">•</span>
                    <span>CHECK-IN MUDAH & CEPAT</span>
                    <span class="text-white/40">•</span>
                    <span>VERIFIKASI PANITIA RESMI</span>
                    <span class="text-white/40">•</span>
                </span>
                <span class="inline-flex items-center gap-6 px-4" aria-hidden="true">
                    <span class="inline-flex items-center gap-1.5 text-amber-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                        TIKET RESMI & TERVERIFIKASI
                    </span>
                    <span class="text-white/40">•</span>
                    <span>SOUNDCHECK VOL. 1 NIGHT PARTY</span>
                    <span class="text-white/40">•</span>
                    <span>YE JAKARTA 2026 WORLD TOUR</span>
                    <span class="text-white/40">•</span>
                    <span>BIGBANG 2026-2027 WORLD TOUR RE-BOOT</span>
                    <span class="text-white/40">•</span>
                    <span>JAKARTA NIGHT MARATHON 2026</span>
                    <span class="text-white/40">•</span>
                    <span>E-TICKET DENGAN QR CODE INSTAN</span>
                    <span class="text-white/40">•</span>
                    <span>CHECK-IN MUDAH & CEPAT</span>
                    <span class="text-white/40">•</span>
                    <span>VERIFIKASI PANITIA RESMI</span>
                    <span class="text-white/40">•</span>
                </span>
            </div>
        </div>
    </header>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ===== FOOTER HARMONIS SETIKET ===== --}}
    <footer class="bg-gradient-to-b from-[#09132b] via-[#070e22] to-[#040816] text-gray-300 relative border-t border-blue-900/40">
        {{-- Accent Top Gradient Border (Se-frekuensi SeTiket Blue) --}}
        <div class="h-1 w-full bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-400"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-10 pb-12 border-b border-slate-800/80">

                {{-- Brand Info & Logo --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/setiketbg.webp') }}" alt="SeTiket Logo" class="h-10 w-auto object-contain transition-transform hover:scale-105" onerror="this.src='{{ asset('images/setiket.webp') }}'">
                    </div>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-sm">
                        Platform resmi pemesanan tiket hiburan, konser musik, olahraga lari, festival seni, dan workshop terbesar di Indonesia. Beli tiket mudah, cepat, dan aman dengan e-Ticket QR Code resmi.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="https://wa.me/6289681201941" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-slate-800/90 hover:bg-[#0050ff] border border-slate-700/60 flex items-center justify-center text-white transition-all hover:scale-110 shadow-sm" aria-label="WhatsApp">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.187-2.59-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.311.045-.698.058-2.029-.496-1.554-.648-2.531-2.228-2.608-2.33-.078-.104-.627-.836-.627-1.592 0-.756.395-1.127.536-1.282.14-.155.307-.194.409-.194.103 0 .205.002.296.006.096.004.225-.036.352.27.13.313.444 1.084.483 1.163.039.078.064.17.013.272-.051.103-.077.167-.154.256-.076.09-.161.2-.23.269-.077.077-.157.161-.067.315.09.155.4 0.658.857 1.066.589.524 1.085.688 1.24.765.154.077.243.064.333-.038.09-.103.385-.449.487-.603.103-.154.205-.128.346-.077.141.051.897.423 1.05.5.154.077.256.115.295.18.038.064.038.371-.106.776z"/></svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-slate-800/90 hover:bg-pink-600 border border-slate-700/60 flex items-center justify-center text-white transition-all hover:scale-110 shadow-sm" aria-label="Instagram">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Column 1: Rayakan Event --}}
                <div class="space-y-3">
                    <h5 class="text-xs font-bold text-white uppercase tracking-wider">Rayakan Event</h5>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-400">
                        <li><a href="{{ route('home') }}#events" class="hover:text-white transition-colors">Event Trending</a></li>
                        <li><a href="{{ route('home') }}#upcoming-events" class="hover:text-white transition-colors">Event Mendatang</a></li>
                        <li><a href="{{ route('home') }}#ended-events" class="hover:text-white transition-colors">Arsip Event Selesai</a></li>
                    </ul>
                </div>

                {{-- Column 2: Akun & Transaksi --}}
                <div class="space-y-3">
                    <h5 class="text-xs font-bold text-white uppercase tracking-wider">Akun & Tiket</h5>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-400">
                        @auth
                            @if(auth()->user()->isUser())
                                <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Pesanan Saya</a></li>
                                <li><a href="{{ route('tickets') }}" class="hover:text-white transition-colors">Tiket Aktif</a></li>
                            @else
                                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Dashboard Admin</a></li>
                            @endif
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition-colors">Pengaturan Akun</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk ke Akun</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar Akun Baru</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- Column 3: Bantuan & Hubungi --}}
                <div class="space-y-3">
                    <h5 class="text-xs font-bold text-white uppercase tracking-wider">Bantuan</h5>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-400">
                        <li><a href="https://wa.me/6289681201941" target="_blank" rel="noopener" class="hover:text-white transition-colors">Hubungi Layanan CS</a></li>
                        <li><a href="https://wa.me/6289681201941" target="_blank" rel="noopener" class="hover:text-white transition-colors">Pusat Bantuan WhatsApp</a></li>
                    </ul>
                </div>

            </div>

            {{-- Footer Bottom --}}
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} SeTiket. Seluruh hak cipta dilindungi undang-undang.</p>
                <div class="flex items-center gap-4 text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sistem Terenkripsi SSL 256-Bit
                    </span>
                    <span>•</span>
                    <span>Platform Tiket Resmi Indonesia</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Script Helpers --}}
    <script>
        function toggleTheme() {
            const metaTheme = document.getElementById('metaThemeColor');
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
                if (metaTheme) metaTheme.setAttribute('content', '#ffffff');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
                if (metaTheme) metaTheme.setAttribute('content', '#0b0f19');
            }
        }

        function toggleProfileMenu(e) {
            e.stopPropagation();
            const pd = document.getElementById('profileDropdown');
            if (pd) pd.classList.toggle('hidden');
        }

        function toggleMobileMenu() {
            const mm = document.getElementById('mobileMenu');
            const iconClosed = document.getElementById('mobileMenuIconClosed');
            const iconOpen = document.getElementById('mobileMenuIconOpen');
            if (mm) {
                const isHidden = mm.classList.toggle('hidden');
                if (iconClosed && iconOpen) {
                    if (isHidden) {
                        iconClosed.classList.remove('hidden');
                        iconOpen.classList.add('hidden');
                    } else {
                        iconClosed.classList.add('hidden');
                        iconOpen.classList.remove('hidden');
                    }
                }
            }
        }

        document.addEventListener('click', function(e) {
            const profDD = document.getElementById('profileDropdown');
            const profBtn = document.getElementById('profileTrigger');
            if (profDD && !profDD.classList.contains('hidden')) {
                if (!profDD.contains(e.target) && (!profBtn || !profBtn.contains(e.target))) {
                    profDD.classList.add('hidden');
                }
            }

            const mm = document.getElementById('mobileMenu');
            const mmBtn = document.getElementById('mobileMenuBtn');
            if (mm && !mm.classList.contains('hidden')) {
                if (!mm.contains(e.target) && (!mmBtn || !mmBtn.contains(e.target))) {
                    mm.classList.add('hidden');
                    const iconClosed = document.getElementById('mobileMenuIconClosed');
                    const iconOpen = document.getElementById('mobileMenuIconOpen');
                    if (iconClosed && iconOpen) {
                        iconClosed.classList.remove('hidden');
                        iconOpen.classList.add('hidden');
                    }
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const profDD = document.getElementById('profileDropdown');
                if (profDD) profDD.classList.add('hidden');
                const mm = document.getElementById('mobileMenu');
                if (mm && !mm.classList.contains('hidden')) {
                    mm.classList.add('hidden');
                    const iconClosed = document.getElementById('mobileMenuIconClosed');
                    const iconOpen = document.getElementById('mobileMenuIconOpen');
                    if (iconClosed && iconOpen) {
                        iconClosed.classList.remove('hidden');
                        iconOpen.classList.add('hidden');
                    }
                }
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
