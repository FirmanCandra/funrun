<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SeTiket</title>
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

<body class="flex h-dvh overflow-hidden text-slate-800">

    <!-- Latar gelap saat laci terbuka di layar kecil -->
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-72 max-w-[85vw] -translate-x-full transition-transform duration-200 ease-out overflow-y-auto bg-white border-r border-slate-200 flex flex-col lg:static lg:z-auto lg:w-64 lg:max-w-none lg:translate-x-0">
        <div class="h-20 flex items-center justify-between px-4 sm:px-6 border-b border-slate-200 shrink-0">
            <div class="flex items-center min-w-0">
                <img src="{{ asset('images/setiketbg.webp') }}" alt="SeTiket Logo"
                    class="block h-14 sm:h-20 w-auto mr-3">
                <span class="font-bold text-lg sm:text-xl tracking-tight truncate">Admin Panel</span>
            </div>
            <!-- Tutup laci (hanya layar kecil) -->
            <button type="button" id="sidebarClose"
                class="lg:hidden w-9 h-9 shrink-0 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors"
                aria-label="Tutup menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 py-6 flex flex-col gap-2">
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.participants') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.participants') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                Participants
            </a>
            <a href="{{ route('admin.orders') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pesanan
                @php($pendingBadge = \App\Http\Controllers\AdminController::pendingOrdersCount(auth()->user()))
                @if($pendingBadge > 0)
                    <span
                        class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full min-w-[1.5rem] text-center"
                        title="Pesanan menunggu verifikasi">{{ $pendingBadge }}</span>
                @endif
            </a>
            <a href="{{ route('admin.payment-accounts') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.payment-accounts') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                    </path>
                </svg>
                Rekening Pembayaran
            </a>
            <a href="{{ route('admin.form-fields') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.form-fields') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Formulir Pendaftaran
            </a>
            <a href="{{ route('admin.event-image') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.event-image') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                Gambar Event
            </a>
            <a href="{{ route('admin.scanner') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.scanner') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                    </path>
                </svg>
                QR Scanner
            </a>
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.events') }}"
                    class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.events') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Manajemen Event
                </a>
                <a href="{{ route('admin.admins') }}"
                    class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.admins') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Manajemen Admin
                </a>
            @endif
        </nav>

        <div class="p-6 border-t border-slate-200 shrink-0">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Site
            </a>
        </div>
        <div class="p-6 border-t border-slate-200 shrink-0">
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profil Saya
            </a>
        </div>
        <div class="p-6 border-t border-slate-200 shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <main class="flex-1 min-w-0 flex flex-col h-dvh overflow-hidden bg-slate-50">
        <!-- Header -->
        <header
            class="h-20 shrink-0 bg-white border-b border-slate-200 flex items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 z-10">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Buka laci (hanya layar kecil) -->
                <button type="button" id="sidebarToggle"
                    class="lg:hidden w-10 h-10 shrink-0 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                    aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex flex-col min-w-0">
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold truncate">@yield('header_title', 'Dashboard')</h1>
                    @if(auth()->user()->role === 'admin' && auth()->user()->event)
                        <span class="text-xs text-slate-500 font-medium truncate">Event:
                            {{ auth()->user()->event->title }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="font-semibold text-slate-800 text-sm">{{ auth()->user()->name }}</span>
                    <span
                        class="text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </div>
                <div
                    class="w-10 h-10 shrink-0 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold">
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
