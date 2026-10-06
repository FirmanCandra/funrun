@extends('admin.layouts.admin')

@section('header_title', 'Dashboard Overview')

@section('content')

{{-- Welcome Greeting Banner --}}
<div class="relative overflow-hidden bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg shadow-blue-500/10 mb-8">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="max-w-xl">
            <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold text-blue-100 mb-3 border border-white/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                SeTiket Operational Hub
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="text-sm sm:text-base text-blue-100/90 mt-2 leading-relaxed">
                @if(auth()->user()->role === 'admin' && auth()->user()->event)
                    Kelola operasional event <span class="font-bold underline decoration-white/40">{{ auth()->user()->event->title }}</span> secara terpusat, mulai dari validasi pembayaran hingga check-in peserta.
                @else
                    Pusat kendali seluruh event, verifikasi pesanan, penjualan tiket, dan manajemen operasional SeTiket Indonesia.
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.events') }}" 
                   class="bg-white text-blue-600 hover:bg-blue-50 font-bold px-5 py-3 rounded-2xl text-sm transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14" />
                    </svg>
                    Manajemen Event
                </a>
            @endif
            <a href="{{ route('admin.scanner') }}" 
               class="bg-blue-800/80 hover:bg-blue-800 text-white font-semibold px-5 py-3 rounded-2xl text-sm transition-all border border-white/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Buka Scanner
            </a>
        </div>
    </div>

    {{-- Subtle background decoration --}}
    <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute right-40 -top-20 w-60 h-60 bg-indigo-500/20 rounded-full blur-xl pointer-events-none"></div>
</div>

{{-- Antrian pesanan masuk jika ada --}}
@if($pendingOrders > 0)
<div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-l-4 border-amber-500 bg-white rounded-2xl p-5 sm:p-6 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 shadow-xs">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <p class="font-extrabold text-amber-900 text-base">{{ $pendingOrders }} Pesanan Menunggu Verifikasi</p>
            </div>
            <p class="text-xs sm:text-sm text-amber-800/80 mt-0.5">
                @if(auth()->user()->role === 'admin' && auth()->user()->event)
                    Pesanan tiket masuk untuk {{ auth()->user()->event->title }}. Silakan periksa bukti transfer & terbitkan tiket.
                @else
                    Terdapat pesanan masuk dari berbagai event yang butuh verifikasi bukti transfer.
                @endif
            </p>
        </div>
    </div>
    <a href="{{ route('admin.orders') }}"
        class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-xs whitespace-nowrap text-center">
        Verifikasi Sekarang →
    </a>
</div>
@endif

{{-- Stat KPI Metrics Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    {{-- Metric 1: Total Participants --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Peserta</span>
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Participants</p>
        <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalParticipants) }}</h3>
        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
            <span class="text-emerald-600 font-bold">Terdaftar</span> di sistem
        </p>
    </div>

    {{-- Metric 2: Total Revenue --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Revenue</span>
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Revenue</p>
        <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight truncate">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
            <span class="text-emerald-600 font-bold">Lunas</span> pembayaran terverifikasi
        </p>
    </div>

    {{-- Metric 3: Tickets Sold --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">Tiket</span>
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Tickets Sold</p>
        <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($ticketsSold) }}</h3>
        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
            <span class="text-indigo-600 font-bold">Valid</span> tiket siap digunakan
        </p>
    </div>

    {{-- Metric 4: Checked-In --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Check-In</span>
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Checked-In</p>
        <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($checkedIn) }}</h3>
        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
            @php($rate = $ticketsSold > 0 ? round(($checkedIn / $ticketsSold) * 100) : 0)
            <span class="text-amber-600 font-bold">{{ $rate }}%</span> kehadiran di venue
        </p>
    </div>
</div>

{{-- Quick Actions Hub --}}
<div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-xs mb-8">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">Aksi Cepat Admin</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pintasan navigasi untuk mengelola fitur-fitur operasional utama</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.events') }}" 
           class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-blue-200 hover:shadow-md transition-all group flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition-colors">Manajemen Event</h4>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">Atur seluruh event, poster, tiket, rekening, dan form pendaftaran.</p>
            </div>
        </a>
        @endif

        <a href="{{ route('admin.orders') }}" 
           class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-amber-200 hover:shadow-md transition-all group flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-amber-600 transition-colors">Verifikasi Pesanan</h4>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">Cek bukti transfer pembayaran dan terbitkan tiket baru.</p>
            </div>
        </a>

        <a href="{{ route('admin.scanner') }}" 
           class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-indigo-200 hover:shadow-md transition-all group flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">QR Scanner</h4>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">Scan QR code tiket peserta saat memasuki area event.</p>
            </div>
        </a>

        <a href="{{ route('admin.participants') }}" 
           class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-emerald-200 hover:shadow-md transition-all group flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-emerald-600 transition-colors">Data Peserta</h4>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">Lihat daftar peserta lengkap dan ekspor data ke format CSV.</p>
            </div>
        </a>
    </div>
</div>

{{-- Two-Column Overview Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Column 1: Event Aktif & Thumbnail Overview --}}
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-xs">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">Event Aktif & Thumbnail</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar event yang sedang dibuka pendaftarannya</p>
            </div>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.events') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                    Lihat Semua →
                </a>
            @endif
        </div>

        <div class="space-y-4">
            @forelse($recentEvents as $ev)
                <div class="p-3.5 rounded-2xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 hover:bg-white transition-all flex items-center gap-4 group">
                    {{-- Thumbnail Box --}}
                    <div class="w-20 h-14 sm:w-24 sm:h-16 rounded-xl overflow-hidden bg-slate-200 shrink-0 relative border border-slate-200">
                        @if(!empty($ev['thumbnail']))
                            <img src="{{ $ev['thumbnail'] }}" alt="{{ $ev['nama'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-xs">
                                {{ strtoupper(substr($ev['nama'], 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Event Details --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            @if(($ev['kategori'] ?? '') === 'upcoming')
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Upcoming</span>
                            @else
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Highlight</span>
                            @endif
                            <span class="text-xs text-slate-400 font-medium truncate">{{ $ev['tanggal'] }}</span>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm truncate group-hover:text-blue-600 transition-colors">
                            {{ $ev['nama'] }}
                        </h4>
                        <p class="text-xs text-slate-500 truncate mt-0.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $ev['lokasi'] }}
                        </p>
                    </div>

                    {{-- Action Button --}}
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.events') }}" 
                           class="shrink-0 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-xs">
                            Kelola
                        </a>
                    @else
                        <a href="{{ route('admin.event-image') }}" 
                           class="shrink-0 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-xs">
                            Kelola
                        </a>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">Belum ada event terdaftar.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Column 2: Pesanan Masuk Terbaru --}}
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-xs">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">Pesanan Masuk Terbaru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Transaksi pembelian tiket yang baru dilakukan peserta</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                Semua Pesanan →
            </a>
        </div>

        <div class="space-y-3.5">
            @forelse($recentOrders as $ord)
                <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs font-bold text-slate-700">{{ $ord->order_code }}</span>
                            @if($ord->payment_status === \App\Models\Order::STATUS_PAID)
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Lunas</span>
                            @elseif($ord->payment_status === \App\Models\Order::STATUS_WAITING)
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Verifikasi</span>
                            @elseif($ord->payment_status === \App\Models\Order::STATUS_REJECTED)
                                <span class="text-[10px] font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-md">Ditolak</span>
                            @else
                                <span class="text-[10px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">{{ $ord->payment_status }}</span>
                            @endif
                        </div>
                        <p class="text-xs font-semibold text-slate-800 truncate">
                            {{ $ord->user->name ?? 'Pembeli' }} • {{ $ord->event->title ?? 'Event' }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $ord->created_at ? $ord->created_at->diffForHumans() : '-' }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <p class="text-xs font-extrabold text-slate-900">
                            Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                        </p>
                        <a href="{{ route('admin.orders') }}" class="text-[11px] font-semibold text-blue-600 hover:underline mt-1 block">
                            Detail →
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">Belum ada pesanan tiket.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
