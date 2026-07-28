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

<body class="flex h-screen overflow-hidden text-slate-800">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col transition-all">
        <div class="h-20 flex items-center px-6 border-b border-slate-200">
            <img src="{{ asset('images/setiketbg.webp') }}" alt="SeTiket Logo"
                style="height: 80px; width: auto; display: block;" class="mr-3">
            <span class="font-bold text-xl tracking-tight">Admin Panel</span>
        </div>

        <nav class="flex-1 py-6 flex flex-col gap-2">
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.participants') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.participants') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                Participants
            </a>
            <a href="{{ route('admin.payments') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Payments
            </a>
            <a href="{{ route('admin.scanner') }}"
                class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.scanner') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                    </path>
                </svg>
                QR Scanner
            </a>
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.events') }}"
                    class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.events') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Manajemen Event
                </a>
                <a href="{{ route('admin.admins') }}"
                    class="sidebar-item px-6 py-3 flex items-center gap-3 text-slate-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.admins') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Manajemen Admin
                </a>
            @endif
        </nav>

        <div class="p-6 border-t border-slate-200">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Site
            </a>
        </div>
        <div class="p-6 border-t border-slate-200">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10">
            <div class="flex flex-col">
                <h1 class="text-2xl font-bold">@yield('header_title', 'Dashboard')</h1>
                @if(auth()->user()->role === 'admin' && auth()->user()->event)
                    <span class="text-xs text-slate-500 font-medium">Event: {{ auth()->user()->event->title }}</span>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end">
                    <span class="font-semibold text-slate-800 text-sm">{{ auth()->user()->name }}</span>
                    <span
                        class="text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </div>
                <div
                    class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Content scrollable area -->
        <div class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </div>
    </main>

</body>

</html>