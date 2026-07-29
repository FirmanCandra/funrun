@extends('layouts.app')

@section('title', 'SeTiket — Temukan Eventmu Sekarang')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
    {{-- Layered background --}}
    <div class="absolute inset-0 z-0" style="background: linear-gradient(160deg, #0a0425 0%, #1a0a5e 40%, #2a1a7e 65%, #0a0425 100%);"></div>
    {{-- Glowing orbs --}}
    <div class="absolute top-[-15%] left-[-10%] w-[55%] h-[55%] rounded-full pointer-events-none" style="background:radial-gradient(ellipse, rgba(245,166,35,0.07) 0%, transparent 70%);"></div>
    <div class="absolute bottom-[-20%] right-[-5%] w-[50%] h-[60%] rounded-full pointer-events-none" style="background:radial-gradient(ellipse, rgba(61,42,158,0.18) 0%, transparent 70%);"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-20">

        @auth
            @if(auth()->user()->isUser())

                {{-- ===== TAMPILAN PESERTA YANG SUDAH LOGIN ===== --}}
                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full mb-8 text-sm font-semibold tracking-wide"
                     style="background:rgba(56,189,248,0.10);border:1px solid rgba(56,189,248,0.25);color:#38bdf8;">
                    <span class="w-6 h-6 rounded-full bg-sky-500 text-slate-900 flex items-center justify-center text-xs font-black">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    Selamat datang kembali
                </div>

                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-6 leading-none"
                    style="background:linear-gradient(135deg,#fff 0%,#7dd3fc 50%,#818cf8 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                    Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                    @if($myTicketCount > 0)
                        Tiket Anda sudah siap. Tunjukkan QR-nya saat check-in di lokasi acara.
                    @elseif($myPendingOrders > 0)
                        Pesanan Anda sedang diverifikasi panitia. Tiket akan muncul di sini setelah disetujui.
                    @else
                        Belum ada tiket aktif. Pilih event di bawah untuk mulai memesan.
                    @endif
                </p>

                {{-- Ringkasan milik peserta --}}
                <div class="grid grid-cols-2 gap-4 max-w-md mx-auto mb-10">
                    <a href="{{ route('tickets') }}" class="glass-panel p-5 rounded-2xl text-center hover:bg-white/10 transition-all">
                        <div class="text-3xl font-extrabold text-white mb-1">{{ $myTicketCount }}</div>
                        <div class="text-xs text-gray-400 uppercase tracking-widest">Tiket Aktif</div>
                    </a>
                    <a href="{{ route('dashboard') }}" class="glass-panel p-5 rounded-2xl text-center hover:bg-white/10 transition-all">
                        <div class="text-3xl font-extrabold {{ $myPendingOrders > 0 ? 'text-amber-400' : 'text-white' }} mb-1">
                            {{ $myPendingOrders }}
                        </div>
                        <div class="text-xs text-gray-400 uppercase tracking-widest">Menunggu Verifikasi</div>
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('tickets') }}"
                       class="w-full sm:w-auto px-10 py-4 rounded-full font-bold text-lg text-white transition-all transform hover:-translate-y-1"
                       style="background:linear-gradient(135deg,#0ea5e9,#6366f1);box-shadow:0 4px 24px rgba(14,165,233,0.3);">
                        Lihat Tiket Saya
                    </a>
                    <a href="#upcoming-events"
                       class="w-full sm:w-auto glass-panel text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-all">
                        Jelajahi Event →
                    </a>
                </div>

            @else

                {{-- ===== TAMPILAN ADMIN / SUPER ADMIN ===== --}}
                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full mb-8 text-sm font-semibold tracking-wide"
                     style="background:rgba(148,163,184,0.10);border:1px solid rgba(148,163,184,0.25);color:#cbd5e1;">
                    Mode Pengelola
                </div>

                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-6 leading-none text-white">
                    Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}
                </h1>
                <p class="text-lg text-gray-300 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                    Anda masuk sebagai {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin Event' }}.
                    Akun pengelola tidak dipakai untuk membeli tiket.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('admin.dashboard') }}"
                       class="w-full sm:w-auto px-10 py-4 rounded-full font-bold text-lg text-white transition-all transform hover:-translate-y-1"
                       style="background:linear-gradient(135deg,#0ea5e9,#6366f1);box-shadow:0 4px 24px rgba(14,165,233,0.3);">
                        Buka Panel Admin
                    </a>
                    <a href="#upcoming-events"
                       class="w-full sm:w-auto glass-panel text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-all">
                        Lihat Event →
                    </a>
                </div>

            @endif
        @else

            {{-- ===== TAMPILAN PENGUNJUNG (BELUM LOGIN) ===== --}}
            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full mb-8 text-sm font-semibold uppercase tracking-widest"
                 style="background:rgba(245,166,35,0.10);border:1px solid rgba(245,166,35,0.25);color:#F5A623;">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background:#F5A623;"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5" style="background:#F5A623;"></span>
                </span>
                Registrasi Dibuka Sekarang!
            </div>

            <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6 leading-none"
                style="background:linear-gradient(135deg,#fff 0%,#ffc857 50%,#F5A623 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                TEMUKAN EVENTMU<br>SEKARANG
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-10 max-w-3xl mx-auto font-light leading-relaxed">
                Tiket event terlengkap, mudah, dan terpercaya. Dari fun run hingga konser musik — semua ada di sini.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}"
                   class="w-full sm:w-auto px-10 py-4 rounded-full font-bold text-lg transition-all transform hover:-translate-y-1"
                   style="background:linear-gradient(135deg,#F5A623,#d48f1a);color:#0a0425;box-shadow:0 4px 24px rgba(245,166,35,0.3);">
                    Buat Akun Gratis
                </a>
                <a href="#upcoming-events"
                   class="w-full sm:w-auto glass-panel text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-all">
                    Lihat Event →
                </a>
            </div>

            <p class="mt-5 text-sm text-gray-400">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 font-semibold transition-colors">Masuk di sini</a>
            </p>

        @endauth

        {{-- Live Countdown --}}
        <div class="mt-16 grid grid-cols-4 gap-4 max-w-sm mx-auto sm:max-w-md">
            @foreach([['id'=>'cd-days','label'=>'Hari'],['id'=>'cd-hours','label'=>'Jam'],['id'=>'cd-mins','label'=>'Menit'],['id'=>'cd-secs','label'=>'Detik']] as $c)
            <div class="glass-panel p-4 rounded-2xl text-center">
                <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1" id="{{ $c['id'] }}">--</div>
                <div class="text-xs text-gray-400 uppercase tracking-widest">{{ $c['label'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Angka promosi hanya untuk pengunjung — sudah tidak relevan
             bagi orang yang sudah punya akun. --}}
        @guest
            <div class="mt-14 flex justify-center gap-12">
                <div class="text-center">
                    <div class="text-2xl font-extrabold" style="color:#F5A623;">500+</div>
                    <div class="text-xs text-gray-400 mt-0.5">Event Terlaksana</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-extrabold" style="color:#F5A623;">100K+</div>
                    <div class="text-xs text-gray-400 mt-0.5">Tiket Terjual</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-extrabold" style="color:#F5A623;">50+</div>
                    <div class="text-xs text-gray-400 mt-0.5">Kota di Indonesia</div>
                </div>
            </div>
        @endguest
    </div>
</section>

{{-- ===== UPCOMING EVENTS ===== --}}
<section id="upcoming-events" class="py-20 relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white">Upcoming Events</h2>
                <div class="mt-2 h-1 w-16 rounded-full" style="background:linear-gradient(90deg,#F5A623,transparent);"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($upcomingEvents as $ev)
            <div class="event-card rounded-2xl overflow-hidden shadow-lg flex flex-col transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
                 style="background:#fff;" data-nama="{{ strtolower($ev['nama']) }}">
                @php($sudahTerdaftar = $myEventIds->contains($ev['id']))
                <div class="relative">
                    <img src="{{ $ev['thumbnail'] ?: 'https://placehold.co/300x175/1a0a5e/ffffff?text='.rawurlencode(mb_substr($ev['nama'],0,12)) }}"
                         alt="{{ $ev['nama'] }}"
                         class="w-full object-cover"
                         style="height:175px;"
                         onerror="this.src='https://placehold.co/300x175/1a0a5e/ffffff?text=Event'">
                    {{-- Penanda khusus peserta yang sudah pernah mendaftar di event ini --}}
                    @if($sudahTerdaftar)
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold text-white"
                              style="background:rgba(16,185,129,0.95);">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sudah Terdaftar
                        </span>
                    @endif
                </div>
                <div class="p-4 flex flex-col flex-1" style="color:#1a1a2e;">
                    <div class="font-bold text-sm mb-2 leading-snug"
                         style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $ev['nama'] }}
                    </div>
                    <div class="flex items-start gap-1 text-xs text-gray-500 mb-1">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" style="color:#1a0a5e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="truncate">{{ $ev['lokasi'] }}</span>
                    </div>
                    <div class="flex items-start gap-1 text-xs text-gray-500 mb-3">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" style="color:#1a0a5e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                        </svg>
                        <span>{{ $ev['tanggal'] }}</span>
                    </div>
                    <div class="mt-auto flex items-baseline gap-1">
                        <span class="text-xs text-gray-400">Mulai Dari</span>
                        <span class="font-bold text-base" style="color:#d48f1a;">
                            {{ $ev['harga'] == 0 ? 'Gratis' : 'Rp'.number_format($ev['harga'],0,',','.') }}
                        </span>
                    </div>
                </div>
                @if($sudahTerdaftar)
                    <a href="{{ route('dashboard') }}"
                       class="block w-full py-3 text-center text-sm font-bold text-white transition-all hover:opacity-90"
                       style="background:linear-gradient(135deg,#0f766e,#065f46);">
                        Lihat Pesanan Saya
                    </a>
                @else
                    <a href="{{ route('event.show', $ev['id']) }}"
                       class="block w-full py-3 text-center text-sm font-bold text-white transition-all hover:opacity-90"
                       style="background:linear-gradient(135deg,#1a0a5e,#0a0425);">
                        Beli Tiket
                    </a>
                @endif
            </div>
            @empty
            <div class="col-span-4 text-center py-16 text-gray-400">
                <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p>Belum ada upcoming event. Tambahkan lewat panel admin.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== HIGHLIGHT EVENTS ===== --}}
<section id="highlight-events" class="py-20 relative z-10" style="background:linear-gradient(180deg,transparent,rgba(26,10,94,0.07),transparent);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white">Highlight Events</h2>
                <div class="mt-2 h-1 w-16 rounded-full" style="background:linear-gradient(90deg,#F5A623,transparent);"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($highlightEvents as $ev)
            <div class="event-card rounded-2xl overflow-hidden shadow-lg flex flex-col transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
                 style="background:#fff;" data-nama="{{ strtolower($ev['nama']) }}">
                @php($sudahTerdaftar = $myEventIds->contains($ev['id']))
                <div class="relative">
                    <img src="{{ $ev['thumbnail'] ?: 'https://placehold.co/300x175/1a0a5e/ffffff?text='.rawurlencode(mb_substr($ev['nama'],0,12)) }}"
                         alt="{{ $ev['nama'] }}"
                         class="w-full object-cover"
                         style="height:175px;"
                         onerror="this.src='https://placehold.co/300x175/1a0a5e/ffffff?text=Event'">
                    {{-- Penanda khusus peserta yang sudah pernah mendaftar di event ini --}}
                    @if($sudahTerdaftar)
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold text-white"
                              style="background:rgba(16,185,129,0.95);">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sudah Terdaftar
                        </span>
                    @endif
                </div>
                <div class="p-4 flex flex-col flex-1" style="color:#1a1a2e;">
                    <div class="font-bold text-sm mb-2 leading-snug"
                         style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $ev['nama'] }}
                    </div>
                    <div class="flex items-start gap-1 text-xs text-gray-500 mb-1">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" style="color:#1a0a5e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="truncate">{{ $ev['lokasi'] }}</span>
                    </div>
                    <div class="flex items-start gap-1 text-xs text-gray-500 mb-3">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" style="color:#1a0a5e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                        </svg>
                        <span>{{ $ev['tanggal'] }}</span>
                    </div>
                    <div class="mt-auto flex items-baseline gap-1">
                        <span class="text-xs text-gray-400">Mulai Dari</span>
                        <span class="font-bold text-base" style="color:#d48f1a;">
                            {{ $ev['harga'] == 0 ? 'Gratis' : 'Rp'.number_format($ev['harga'],0,',','.') }}
                        </span>
                    </div>
                </div>
                @if($sudahTerdaftar)
                    <a href="{{ route('dashboard') }}"
                       class="block w-full py-3 text-center text-sm font-bold text-white transition-all hover:opacity-90"
                       style="background:linear-gradient(135deg,#0f766e,#065f46);">
                        Lihat Pesanan Saya
                    </a>
                @else
                    <a href="{{ route('event.show', $ev['id']) }}"
                       class="block w-full py-3 text-center text-sm font-bold text-white transition-all hover:opacity-90"
                       style="background:linear-gradient(135deg,#1a0a5e,#0a0425);">
                        Beli Tiket
                    </a>
                @endif
            </div>
            @empty
            <div class="col-span-4 text-center py-16 text-gray-400">
                <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p>Belum ada highlight event. Tambahkan lewat panel admin.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>



<script>
(function() {
    var target = new Date('2026-09-15T06:00:00');
    function pad(n) { return String(n).padStart(2,'0'); }
    function tick() {
        var now = new Date(), diff = target - now;
        if (diff < 0) diff = 0;
        var d = Math.floor(diff/86400000);
        var h = Math.floor((diff%86400000)/3600000);
        var m = Math.floor((diff%3600000)/60000);
        var s = Math.floor((diff%60000)/1000);
        document.getElementById('cd-days').textContent  = pad(d);
        document.getElementById('cd-hours').textContent = pad(h);
        document.getElementById('cd-mins').textContent  = pad(m);
        document.getElementById('cd-secs').textContent  = pad(s);
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
@endsection
