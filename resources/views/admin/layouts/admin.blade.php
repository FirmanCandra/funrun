<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SeTiket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar-item.active {
            background-color: #eff6ff;
            color: #2563eb;
            border-right: 3px solid #2563eb;
        }
    </style>
</head>

{{--
    Tata letak admin: di layar lebar sidebar menetap di kiri, di layar kecil
    sidebar jadi laci yang digeser masuk lewat tombol menu — kalau tetap
    menetap, lebarnya menghabiskan hampir seluruh layar ponsel.
--}}

<body class="flex flex-row h-dvh w-screen overflow-hidden text-slate-800" style="display: flex !important; flex-direction: row !important; height: 100vh !important; width: 100vw !important;">

    <!-- Latar gelap saat laci terbuka di layar kecil -->
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-72 max-w-[85vw] -translate-x-full transition-transform duration-200 ease-out overflow-y-auto bg-white border-r border-slate-200/80 flex flex-col shrink-0 lg:static lg:z-auto lg:w-64 lg:max-w-none lg:translate-x-0" style="height: 100vh !important;">
        {{-- Brand Header (Logo & Title) --}}
        <div class="h-20 flex items-center justify-between px-5 border-b border-slate-100 bg-white shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0 group">
                <img src="{{ asset('images/setiketbg.webp') }}" alt="SeTiket Logo"
                    class="h-9 w-auto object-contain shrink-0 group-hover:scale-105 transition-transform">
                <div class="flex flex-col min-w-0">
                    <span class="font-extrabold text-base tracking-tight text-slate-900 leading-tight truncate">SeTiket</span>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-blue-600">Admin Panel</span>
                </div>
            </a>
            <!-- Tutup laci (hanya layar kecil) -->
            <button type="button" id="sidebarClose"
                class="lg:hidden w-8 h-8 shrink-0 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors"
                aria-label="Tutup menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Streamlined Sidebar Navigation --}}
        <nav class="flex-1 px-3.5 py-4 flex flex-col gap-1 overflow-y-auto">
            <div class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Menu Utama
            </div>

            {{-- 1. Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-item px-3.5 py-2.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                Dashboard
            </a>

            {{-- 2. Manajemen Event (Super Admin) / Kelola Event Saya (Admin Event) --}}
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.events') }}"
                    class="sidebar-item px-3.5 py-2.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-all {{ request()->routeIs('admin.events*') || request()->routeIs('admin.payment-accounts*') || request()->routeIs('admin.form-fields*') || request()->routeIs('admin.event-image*') ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Manajemen Event
                </a>
            @else
                <a href="{{ route('admin.event-image') }}"
                    class="sidebar-item px-3.5 py-2.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-all {{ request()->routeIs('admin.event-image*') || request()->routeIs('admin.payment-accounts*') || request()->routeIs('admin.form-fields*') ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Kelola Event Saya
                </a>
            @endif

            {{-- 3. Pesanan --}}
            <a href="{{ route('admin.orders') }}"
                class="sidebar-item px-3.5 py-2.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-all {{ request()->routeIs('admin.orders') ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="flex-1 truncate">Pesanan</span>
                @php($pendingBadge = \App\Http\Controllers\AdminController::pendingOrdersCount(auth()->user()))
                @if($pendingBadge > 0)
                    <span
                        class="bg-amber-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[1.4rem] text-center"
                        title="Pesanan menunggu verifikasi">{{ $pendingBadge }}</span>
                @endif
            </a>

            {{-- 4. Participants --}}
            <a href="{{ route('admin.participants') }}"
                class="sidebar-item px-3.5 py-2.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-all {{ request()->routeIs('admin.participants') ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                Participants
            </a>

            {{-- 5. QR Scanner --}}
            <a href="{{ route('admin.scanner') }}"
                class="sidebar-item px-3.5 py-2.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-all {{ request()->routeIs('admin.scanner') ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                    </path>
                </svg>
                QR Scanner
            </a>

            @if(auth()->user()->role === 'super_admin')
                <div class="px-3 pt-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Sistem
                </div>
                <a href="{{ route('admin.admins') }}"
                    class="sidebar-item px-3.5 py-2.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-all {{ request()->routeIs('admin.admins') ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Manajemen Admin
                </a>
            @endif
        </nav>

        {{-- Sidebar Footer Actions --}}
        <div class="p-3 border-t border-slate-100 bg-slate-50/50 shrink-0 flex flex-col gap-1">
            <a href="{{ route('home') }}"
                class="px-3 py-2 rounded-xl flex items-center gap-2.5 text-xs font-medium text-slate-600 hover:bg-white hover:text-blue-600 transition-all">
                <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Ke Website
            </a>
            <a href="{{ route('profile.edit') }}"
                class="px-3 py-2 rounded-xl flex items-center gap-2.5 text-xs font-medium text-slate-600 hover:bg-white hover:text-blue-600 transition-all">
                <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profil Saya
            </a>
            <form action="{{ route('logout') }}" method="POST" class="pt-1">
                @csrf
                <button type="submit"
                    class="w-full px-3 py-2 rounded-xl flex items-center justify-center gap-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0 flex flex-col h-dvh overflow-hidden bg-slate-50" style="flex: 1 1 0% !important; min-width: 0 !important; height: 100vh !important;">
        <!-- Header -->
        <header
            class="h-20 shrink-0 bg-white border-b border-slate-200/80 flex items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 z-10">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Buka laci (hanya layar kecil) -->
                <button type="button" id="sidebarToggle"
                    class="lg:hidden w-10 h-10 shrink-0 flex items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                    aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex flex-col min-w-0">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight truncate">@yield('header_title', 'Dashboard')</h1>
                    @if(auth()->user()->role === 'admin' && auth()->user()->event)
                        <span class="text-xs text-blue-600 font-semibold truncate flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Event: {{ auth()->user()->event->title }}
                        </span>
                    @else
                        <span class="text-[11px] text-slate-400 font-medium">Panel Administrasi SeTiket</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="font-bold text-slate-800 text-sm leading-tight">{{ auth()->user()->name }}</span>
                    <span class="text-[11px] font-semibold text-blue-600 capitalize bg-blue-50 px-2 py-0.5 rounded-full mt-0.5 border border-blue-100/50">
                        {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin Event' }}
                    </span>
                </div>
                <div
                    class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-xs flex items-center justify-center font-bold text-sm tracking-wide">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Content scrollable area -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

    <script>
        // Laci sidebar untuk layar kecil. Di lg ke atas sidebar sudah menetap,
        // jadi kelas geser dilepas agar tidak menyisa saat jendela diperbesar.
        (function () {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var tombolBuka = document.getElementById('sidebarToggle');
            var tombolTutup = document.getElementById('sidebarClose');

            function buka() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                tombolBuka.setAttribute('aria-expanded', 'true');
            }

            function tutup() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                tombolBuka.setAttribute('aria-expanded', 'false');
            }

            tombolBuka.addEventListener('click', buka);
            tombolTutup.addEventListener('click', tutup);
            overlay.addEventListener('click', tutup);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') tutup();
            });

            // Jendela dilebarkan sampai sidebar menetap: pastikan latar gelap ikut hilang.
            window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) {
                if (e.matches) tutup();
            });
        })();
    </script>

</body>

</html>
