@extends('layouts.app')

@section('title', 'SeTiket | Platform Beli Tiket Konser & Event Terpercaya di Indonesia')

@php
    $trendingEvents = collect($highlightEvents);
    $upcoming = collect($upcomingEvents);
    $ended = collect($endedEvents ?? []);
    $allEvents = $trendingEvents->concat($upcoming)->concat($ended);

    $q = request('q', '');

    // Event untuk Carousel Hero Banner (utamakan event trending)
    $bannerEvents = $trendingEvents->isNotEmpty() ? $trendingEvents : $allEvents->take(5);

    // Helper untuk mengurai tanggal badge Loket Event2Go (contoh: "OKT \n 6 \n SEL")
    $parseDateBadge = function($tanggalStr) {
        $bulanMap = [
            'januari' => ['code' => 'JAN', 'num' => 1],
            'februari' => ['code' => 'FEB', 'num' => 2],
            'maret' => ['code' => 'MAR', 'num' => 3],
            'april' => ['code' => 'APR', 'num' => 4],
            'mei' => ['code' => 'MEI', 'num' => 5],
            'juni' => ['code' => 'JUN', 'num' => 6],
            'juli' => ['code' => 'JUL', 'num' => 7],
            'agustus' => ['code' => 'AGU', 'num' => 8],
            'september' => ['code' => 'SEP', 'num' => 9],
            'oktober' => ['code' => 'OKT', 'num' => 10],
            'november' => ['code' => 'NOV', 'num' => 11],
            'desember' => ['code' => 'DES', 'num' => 12]
        ];
        $hariMap = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
        
        $day = 1;
        if (preg_match('/(\d{1,2})/', $tanggalStr, $m)) {
            $day = (int)$m[1];
        }
        
        $monthStr = 'OKT';
        $monthNum = 10;
        foreach ($bulanMap as $k => $v) {
            if (mb_stripos($tanggalStr, $k) !== false) {
                $monthStr = $v['code'];
                $monthNum = $v['num'];
                break;
            }
        }
        
        $year = 2026;
        if (preg_match('/(\d{4})/', $tanggalStr, $m)) {
            $year = (int)$m[1];
        }
        
        $timestamp = mktime(0, 0, 0, $monthNum, $day, $year);
        $dayName = $hariMap[date('w', $timestamp)];
        
        return [
            'month' => $monthStr,
            'day' => $day,
            'day_name' => $dayName
        ];
    };
@endphp

@section('content')

{{-- ===== HERO BANNER CAROUSEL (LOKET STYLE) ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-7 pb-3 sm:pb-4">
    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-md sm:shadow-lg border border-gray-100 dark:border-slate-800 bg-gray-900" id="heroCarousel">
        
        {{-- Carousel Slides Container --}}
        <div class="relative h-[200px] sm:h-[380px] md:h-[440px] lg:h-[480px] overflow-hidden" id="carouselTrack">
            @foreach($bannerEvents as $index => $bEvent)
                <div class="carousel-slide absolute inset-0 transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                    data-slide-index="{{ $index }}">
                    
                    {{-- Banner Image --}}
                    @if(!empty($bEvent['thumbnail']))
                        <img src="{{ $bEvent['thumbnail'] }}" alt="{{ $bEvent['nama'] }}" class="w-full h-full object-cover">
                    @else
                        @include('partials.event-image', [
                            'nama' => $bEvent['nama'],
                            'thumbnail' => null,
                            'class' => 'w-full h-full object-cover'
                        ])
                    @endif

                    {{-- Dark Overlay for readable text - Desktop only --}}
                    <div class="hidden sm:block absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                    {{-- Mobile clickable link: seluruh banner poster di mobile langsung bisa diklik --}}
                    <a href="{{ route('event.show', $bEvent['id']) }}" class="absolute inset-0 z-10 sm:hidden" aria-label="{{ $bEvent['nama'] }}"></a>

                    {{-- Banner Content & Floating CTA Pill (Desktop Only - di mobile di-hidden agar poster bersih) --}}
                    <div class="hidden sm:flex absolute bottom-5 sm:bottom-8 left-4 sm:left-8 right-4 sm:right-8 flex-col sm:flex-row items-start sm:items-end justify-between gap-3 z-10 pointer-events-none">
                        <div class="max-w-2xl text-white pointer-events-auto">
                            <span class="inline-block px-3 py-1 bg-[#0050ff] text-white text-[11px] sm:text-xs font-bold rounded-full mb-2 tracking-wide uppercase">
                                {{ ($bEvent['kategori'] ?? '') === 'highlight' ? 'Featured Event' : 'Akan Datang' }}
                            </span>
                            <h2 class="text-xl sm:text-3xl md:text-4xl font-black leading-tight drop-shadow-md">
                                {{ $bEvent['nama'] }}
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-200 mt-1.5 flex items-center gap-3 drop-shadow">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.5-5 7-8.5 7-12a7 7 0 1 0-14 0c0 3.5 2.5 7 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                                    {{ $bEvent['lokasi'] }}
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    {{ $bEvent['tanggal'] }}
                                </span>
                            </p>
                        </div>

                        {{-- Loket Floating Action Pill --}}
                        <a href="{{ route('event.show', $bEvent['id']) }}"
                            class="pointer-events-auto inline-flex items-center gap-2.5 px-4 sm:px-6 py-2.5 sm:py-3 rounded-full bg-white/95 hover:bg-white text-gray-900 hover:text-[#0050ff] font-bold text-xs sm:text-sm shadow-xl backdrop-blur-md transition-all transform hover:scale-105 shrink-0">
                            <span>Beli Tiketnya di Sini</span>
                            <svg class="w-4 h-4 text-[#0050ff]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Carousel Arrows --}}
        <button type="button" onclick="prevSlide()" aria-label="Slide sebelumnya"
            class="absolute left-2.5 sm:left-5 top-1/2 -translate-y-1/2 z-20 w-8 sm:w-11 h-8 sm:h-11 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-sm flex items-center justify-center transition-all">
            <svg class="w-4 sm:w-6 h-4 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
        </button>
        <button type="button" onclick="nextSlide()" aria-label="Slide berikutnya"
            class="absolute right-2.5 sm:right-5 top-1/2 -translate-y-1/2 z-20 w-8 sm:w-11 h-8 sm:h-11 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-sm flex items-center justify-center transition-all">
            <svg class="w-4 sm:w-6 h-4 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
            </svg>
        </button>

        {{-- Carousel Dots Indicator --}}
        <div class="absolute bottom-2.5 sm:bottom-3 left-1/2 -translate-x-1/2 sm:left-8 sm:translate-x-0 z-20 flex items-center gap-1.5" id="carouselDots">
            @foreach($bannerEvents as $index => $bEvent)
                <button type="button" onclick="goToSlide({{ $index }})" aria-label="Pilih Slide {{ $index + 1 }}"
                    class="carousel-dot h-2 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/80' }}"
                    data-dot-index="{{ $index }}"></button>
            @endforeach
        </div>

    </div>
</section>

{{-- ===== SAPAAN PENGGUNA TERDAFTAR ===== --}}
@auth
    @if(auth()->user()->isUser() && ($myTicketCount > 0 || $myPendingOrders > 0))
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-slate-800/80 dark:to-slate-900/80 border border-blue-100 dark:border-slate-700/80 p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-[#0050ff] text-white flex items-center justify-center shadow-md shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base">
                            Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}!
                        </h4>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                            @if($myTicketCount > 0)
                                Anda memiliki <span class="font-bold text-[#0050ff] dark:text-blue-400">{{ $myTicketCount }} tiket aktif</span>.
                            @endif
                            @if($myPendingOrders > 0)
                                <span class="font-medium text-amber-700 dark:text-amber-400">{{ $myPendingOrders }} pesanan menunggu verifikasi transfer</span>.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('tickets') }}" class="px-4 py-2 rounded-xl bg-[#0050ff] hover:bg-[#0043d4] text-white text-xs sm:text-sm font-bold shadow-sm transition-colors">
                        Lihat Tiket Saya →
                    </a>
                </div>
            </div>
        </section>
    @endif
@endauth

{{-- ===== SECTION 1: EVENT YANG LAGI TRENDING (LOKET STYLE SCROLLER) ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="events">
    <div class="mb-5">
        <div class="flex items-center gap-2.5">
            {{-- Loket-style Animated Section Icon (Clean & Transparent Background) --}}
            <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 relative flex items-center justify-center anim-trending-box" role="img" aria-label="Icon Trending">
                <svg class="w-full h-full" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="trendCleanGrad" x1="0%" y1="100%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#0050ff"/>
                            <stop offset="100%" stop-color="#00c0fa"/>
                        </linearGradient>
                    </defs>
                    <path d="M4 27L13 17L20 22L30 8" stroke="url(#trendCleanGrad)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <g class="anim-trending-arrow">
                        <path d="M22 8H30V16" stroke="url(#trendCleanGrad)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                    <circle cx="32" cy="4" r="2" fill="#f59e0b" class="anim-sparkle"/>
                    <circle cx="6" cy="12" r="1.5" fill="#0050ff" opacity="0.75" class="anim-sparkle" style="animation-delay: 0.6s;"/>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Event yang Lagi Trending
            </h2>
        </div>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
            Event terpopuler dan paling banyak dicari minggu ini
        </p>
    </div>

    {{-- Horizontal Scrollable Cards with Left/Right Navigation Buttons --}}
    <div class="relative">
        {{-- Tombol Geser Kiri --}}
        <button type="button" onclick="scrollSection('eventTrendingTrack', -320)" aria-label="Sebelumnya"
            class="hidden sm:flex absolute -left-3 sm:-left-5 top-[32%] -translate-y-1/2 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-100 border border-gray-200 dark:border-slate-700 shadow-md hover:shadow-xl hover:scale-110 active:scale-95 items-center justify-center transition-all opacity-90 hover:opacity-100 hover:text-[#0050ff] dark:hover:text-blue-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </button>

        {{-- Carousel Track --}}
        <div class="flex gap-5 sm:gap-6 overflow-x-auto no-scrollbar scroll-smooth pb-4 px-1" id="eventTrendingTrack">
            @forelse($trendingEvents as $index => $event)
                <div class="min-w-[260px] sm:min-w-[280px] md:min-w-[290px] max-w-[290px] shrink-0">
                    <a href="{{ route('event.show', $event['id']) }}" class="block group/card text-decoration-none loket-event-card">
                        
                        {{-- Poster Image 16:9 with zoom effect --}}
                        <div class="poster-wrapper">
                            @if(!empty($event['thumbnail']))
                                <img src="{{ $event['thumbnail'] }}" alt="{{ $event['nama'] }}" class="poster-img" loading="lazy">
                            @else
                                @include('partials.event-image', [
                                    'nama' => $event['nama'],
                                    'thumbnail' => null,
                                    'class' => 'poster-img'
                                ])
                            @endif

                            @if($myEventIds->contains($event['id']))
                                <span class="absolute top-2.5 right-2.5 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1 shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    Terdaftar
                                </span>
                            @endif
                        </div>

                        {{-- Card Body (Persis seperti Gambar 3 - Bersih, Elegan, Tanpa Border Garis) --}}
                        <div class="pt-2.5">
                            <p class="text-xs font-normal text-gray-500 dark:text-gray-400 truncate">
                                {{ $event['kota'] ?? trim(last(explode(',', $event['lokasi'] ?? ''))) }}
                            </p>

                            <h3 class="text-sm sm:text-[15px] font-bold text-gray-900 dark:text-gray-100 group-hover/card:text-[#0050ff] dark:group-hover/card:text-blue-400 transition-colors line-clamp-1 mt-1 leading-snug">
                                {{ $event['nama'] }}
                            </h3>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                Oleh {{ $event['penyelenggara'] ?? 'SeTiket Official' }}
                            </p>

                            <div class="mt-3">
                                <span class="text-xs text-gray-400 dark:text-gray-500 block font-normal leading-none mb-1">Mulai dari</span>
                                <span class="text-sm sm:text-base font-extrabold text-gray-900 dark:text-white block">
                                    {{ (int)($event['harga'] ?? 0) === 0 ? 'Gratis' : 'Rp' . number_format($event['harga'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                    </a>
                </div>
            @empty
                <div class="w-full text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                    Tidak ada event trending saat ini.
                </div>
            @endforelse
        </div>

        {{-- Tombol Geser Kanan --}}
        <button type="button" onclick="scrollSection('eventTrendingTrack', 320)" aria-label="Berikutnya"
            class="hidden sm:flex absolute -right-3 sm:-right-5 top-[32%] -translate-y-1/2 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-100 border border-gray-200 dark:border-slate-700 shadow-md hover:shadow-xl hover:scale-110 active:scale-95 items-center justify-center transition-all opacity-90 hover:opacity-100 hover:text-[#0050ff] dark:hover:text-blue-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </button>
    </div>
</section>

{{-- ===== SECTION 2: EVENT YANG AKAN DATANG (DENGAN WAVE BACKGROUND SE-FREKUENSI SETIKET) ===== --}}
<div class="relative w-full my-6 sm:my-10" id="upcoming-events">
    {{-- Top Wave Divider: Transisi organik bergelombang dari section atas ke warna SeTiket --}}
    <div class="w-full overflow-hidden leading-none text-[#eff5ff] dark:text-[#0c152a] -mb-px">
        <svg class="relative block w-full h-8 sm:h-12 md:h-16" viewBox="0 0 1440 60" fill="currentColor" preserveAspectRatio="none">
            <path d="M0,24 C240,54 480,62 720,38 C960,14 1200,48 1440,28 L1440,60 L0,60 Z"></path>
        </svg>
    </div>

    {{-- Container Konten Section 2 --}}
    <section class="bg-[#eff5ff] dark:bg-[#0c152a] py-6 sm:py-10 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="flex items-center gap-2.5">
                        {{-- Loket-style Animated Section Icon (Clean & Transparent Background) --}}
                        <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 relative flex items-center justify-center anim-calendar-box" role="img" aria-label="Icon Akan Datang">
                            <svg class="w-full h-full" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="calCleanGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#4f46e5"/>
                                        <stop offset="100%" stop-color="#8b5cf6"/>
                                    </linearGradient>
                                </defs>
                                <rect x="3.5" y="6.5" width="29" height="25" rx="6" stroke="url(#calCleanGrad)" stroke-width="2.75" fill="none"/>
                                <path d="M3.5 13.5H32.5" stroke="url(#calCleanGrad)" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M10.5 3V7.5M25.5 3V7.5" stroke="url(#calCleanGrad)" stroke-width="2.75" stroke-linecap="round"/>
                                <circle cx="11" cy="19.5" r="1.5" fill="#6366f1"/>
                                <circle cx="18" cy="19.5" r="1.5" fill="#6366f1"/>
                                <circle cx="25" cy="19.5" r="1.5" fill="#6366f1"/>
                                <circle cx="11" cy="25.5" r="1.5" fill="#6366f1"/>
                                <circle cx="18" cy="25.5" r="2.25" fill="#ef4444" class="anim-sparkle"/>
                                <circle cx="25" cy="25.5" r="1.5" fill="#8b5cf6"/>
                            </svg>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                            Event yang Akan Datang
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Jelajahi konser, festival, dan turnamen mendatang yang siap kamu hadiri
                    </p>
                </div>
            </div>

            {{-- Timeline Container with Vertical Connecting Dashed Line --}}
            <div class="relative w-full">
                {{-- Vertical Dashed Line behind date badges --}}
                <div class="absolute left-[24px] sm:left-[27px] top-6 bottom-10 w-0 border-l-2 border-dashed border-blue-200 dark:border-slate-700 pointer-events-none z-0"></div>

                <div class="space-y-4 sm:space-y-3">
                    @forelse($upcoming as $event)
                        @php
                            $badge = $parseDateBadge($event['tanggal'] ?? '');
                        @endphp
                        <div class="relative z-10">
                            {{-- Hover active applies ONLY to this specific card (group/event) --}}
                            <a href="{{ route('event.show', $event['id']) }}"
                                class="group/event block text-decoration-none">
                                
                                {{-- 1. TAMPILAN DESKTOP (sm:flex) - PERSIS SEPERTI GAMBAR 1 --}}
                                <div class="hidden sm:flex items-center justify-between gap-5 py-3.5 px-3 rounded-2xl hover:bg-white/70 dark:hover:bg-slate-800/50 border-b border-dashed border-blue-100 dark:border-slate-800/80 transition-all duration-200">
                                    {{-- Kolom Kiri: Date Badge Box (OKT \n 6 \n SEL) --}}
                                    <div class="w-14 py-2 bg-white dark:bg-slate-800 border border-blue-100 dark:border-slate-700 rounded-xl shadow-xs text-center shrink-0 transition-transform duration-200 group-hover/event:scale-105 group-hover/event:border-[#0050ff]/60">
                                        <span class="block text-[10px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider leading-none">
                                            {{ $badge['month'] }}
                                        </span>
                                        <span class="block text-xl font-black text-gray-900 dark:text-white leading-tight mt-0.5">
                                            {{ $badge['day'] }}
                                        </span>
                                        <span class="block text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider leading-none mt-0.5">
                                            {{ $badge['day_name'] }}
                                        </span>
                                    </div>

                                    {{-- Kolom Tengah: Judul & Lokasi Event --}}
                                    <div class="flex-1 min-w-0 pr-4">
                                        <h3 class="text-sm md:text-[15px] font-bold text-gray-900 dark:text-white group-hover/event:text-[#0050ff] dark:group-hover/event:text-blue-400 transition-colors line-clamp-1 leading-snug">
                                            {{ $event['nama'] }}
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                            {{ $event['tanggal'] }} • {{ $event['lokasi'] }}
                                        </p>
                                    </div>

                                    {{-- Kolom Kanan: Compact 16:9 Thumbnail --}}
                                    <div class="w-36 sm:w-44 lg:w-48 aspect-[16/9] rounded-xl overflow-hidden shrink-0 shadow-xs border border-gray-100 dark:border-slate-700/80 bg-gray-100 dark:bg-slate-800">
                                        @if(!empty($event['thumbnail']))
                                            <img src="{{ $event['thumbnail'] }}" alt="{{ $event['nama'] }}" class="w-full h-full object-cover transition-transform duration-300 group-hover/event:scale-105" loading="lazy">
                                        @else
                                            @include('partials.event-image', ['nama' => $event['nama'], 'thumbnail' => null, 'class' => 'w-full h-full object-cover'])
                                        @endif
                                    </div>
                                </div>

                                {{-- 2. TAMPILAN MOBILE (sm:hidden) - PERSIS SEPERTI GAMBAR 2 --}}
                                <div class="flex sm:hidden items-start gap-3.5 py-2">
                                    {{-- Kolom Kiri: Date Badge Box (OKT \n 6 \n SEL) --}}
                                    <div class="w-12 py-2 bg-white dark:bg-slate-800 border border-blue-100 dark:border-slate-700 rounded-xl shadow-xs text-center shrink-0 transition-transform duration-200 group-hover/event:scale-105 group-hover/event:border-[#0050ff]/60">
                                        <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider leading-none">
                                            {{ $badge['month'] }}
                                        </span>
                                        <span class="block text-lg font-black text-gray-900 dark:text-white leading-tight mt-0.5">
                                            {{ $badge['day'] }}
                                        </span>
                                        <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-wider leading-none mt-0.5">
                                            {{ $badge['day_name'] }}
                                        </span>
                                    </div>

                                    {{-- Kolom Kanan: Container Card (Thumbnail Lebar di Atas, Judul & Info di Bawah) --}}
                                    <div class="flex-1 min-w-0 bg-white dark:bg-slate-800/90 p-2.5 rounded-2xl border border-blue-100/70 dark:border-slate-700/60 shadow-xs transition-shadow duration-200 group-hover/event:shadow-md">
                                        {{-- 16:9 Thumbnail di bagian atas --}}
                                        <div class="w-full aspect-[16/9] rounded-xl overflow-hidden bg-gray-100 dark:bg-slate-800 mb-2.5">
                                            @if(!empty($event['thumbnail']))
                                                <img src="{{ $event['thumbnail'] }}" alt="{{ $event['nama'] }}" class="w-full h-full object-cover transition-transform duration-300 group-hover/event:scale-105" loading="lazy">
                                            @else
                                                @include('partials.event-image', ['nama' => $event['nama'], 'thumbnail' => null, 'class' => 'w-full h-full object-cover'])
                                            @endif
                                        </div>

                                        {{-- Judul Event (Biru khas Mobile) & Detail --}}
                                        <h3 class="text-sm font-bold text-[#0050ff] dark:text-blue-400 group-hover/event:text-[#003ec8] transition-colors line-clamp-2 leading-snug">
                                            {{ $event['nama'] }}
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                            {{ $event['tanggal'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ $event['lokasi'] }}
                                        </p>
                                    </div>
                                </div>

                            </a>
                        </div>
                    @empty
                        <div class="w-full text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                            Belum ada event mendatang saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Bottom Wave Divider: Transisi organik bergelombang menuju section bawah --}}
    <div class="w-full overflow-hidden leading-none text-[#eff5ff] dark:text-[#0c152a] -mt-px">
        <svg class="relative block w-full h-8 sm:h-12 md:h-16" viewBox="0 0 1440 60" fill="currentColor" preserveAspectRatio="none">
            <path d="M0,0 L1440,0 L1440,24 C1200,54 960,62 720,38 C480,14 240,48 0,28 Z"></path>
        </svg>
    </div>
</div>

{{-- ===== SECTION 3: EVENT YANG SUDAH BERAKHIR (MENYAMPING / HORIZONTAL SCROLLER) ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="ended-events">
    <div class="flex items-center justify-between mb-5">
        <div>
            <div class="flex items-center gap-2.5">
                {{-- Loket-style Animated Section Icon (Clean & Transparent Background) --}}
                <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 relative flex items-center justify-center anim-trophy-box" role="img" aria-label="Icon Event Selesai">
                    <svg class="w-full h-full" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="trophyCleanGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f59e0b"/>
                                <stop offset="100%" stop-color="#d97706"/>
                            </linearGradient>
                        </defs>
                        <path d="M10 6H26V15C26 19.4183 22.4183 23 18 23C13.5817 23 10 19.4183 10 15V6Z" stroke="url(#trophyCleanGrad)" stroke-width="2.75" fill="none" stroke-linejoin="round"/>
                        <path d="M10 9H6.5C5.39543 9 4.5 9.89543 4.5 11V12.5C4.5 14.7091 6.29086 16.5 8.5 16.5H10" stroke="url(#trophyCleanGrad)" stroke-width="2.25" stroke-linecap="round"/>
                        <path d="M26 9H29.5C30.6046 9 31.5 9.89543 31.5 11V12.5C31.5 14.7091 29.7091 16.5 27.5 16.5H26" stroke="url(#trophyCleanGrad)" stroke-width="2.25" stroke-linecap="round"/>
                        <path d="M18 23V28M12 29.5H24" stroke="url(#trophyCleanGrad)" stroke-width="2.75" stroke-linecap="round"/>
                        <path d="M28 2.5L29 4.5L31 5.5L29 6.5L28 8.5L27 6.5L25 5.5L27 4.5L28 2.5Z" fill="#f59e0b" class="anim-sparkle"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    Event yang Sudah Berakhir
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                Koleksi event legendaris yang sukses terselenggara bersama SeTiket
            </p>
        </div>
    </div>

    {{-- Horizontal Scrollable Cards with Left/Right Navigation Buttons --}}
    <div class="relative">
        {{-- Tombol Geser Kiri --}}
        <button type="button" onclick="scrollSection('eventEndedTrack', -320)" aria-label="Sebelumnya"
            class="hidden sm:flex absolute -left-3 sm:-left-5 top-[32%] -translate-y-1/2 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-100 border border-gray-200 dark:border-slate-700 shadow-md hover:shadow-xl hover:scale-110 active:scale-95 items-center justify-center transition-all opacity-90 hover:opacity-100 hover:text-[#0050ff] dark:hover:text-blue-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </button>

        {{-- Carousel Track --}}
        <div class="flex gap-5 sm:gap-6 overflow-x-auto no-scrollbar scroll-smooth pb-4 px-1" id="eventEndedTrack">
            @forelse($ended as $event)
                <div class="min-w-[260px] sm:min-w-[280px] md:min-w-[290px] max-w-[290px] shrink-0">
                    <a href="{{ route('event.show', $event['id']) }}" class="block group/card text-decoration-none loket-event-card opacity-90 hover:opacity-100 transition-all">
                        <div class="poster-wrapper filter grayscale-[25%] group-hover/card:grayscale-0 transition-all">
                            @if(!empty($event['thumbnail']))
                                <img src="{{ $event['thumbnail'] }}" alt="{{ $event['nama'] }}" class="poster-img" loading="lazy">
                            @else
                                @include('partials.event-image', ['nama' => $event['nama'], 'thumbnail' => null, 'class' => 'poster-img'])
                            @endif
                        </div>
                        <div class="pt-2.5">
                            <p class="text-xs font-normal text-gray-500 dark:text-gray-400 truncate">
                                {{ $event['kota'] ?? trim(last(explode(',', $event['lokasi'] ?? ''))) }}
                            </p>
                            <h3 class="text-sm sm:text-[15px] font-bold text-gray-800 dark:text-gray-100 group-hover/card:text-[#0050ff] dark:group-hover/card:text-blue-400 transition-colors line-clamp-1 mt-1 leading-snug">
                                {{ $event['nama'] }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                Oleh {{ $event['penyelenggara'] ?? 'SeTiket Official' }}
                            </p>
                            
                            <div class="mt-3">
                                <span class="text-xs text-gray-400 dark:text-gray-500 block font-normal leading-none mb-1">Status</span>
                                <span class="text-sm sm:text-base font-bold text-gray-600 dark:text-gray-300 block">
                                    Penjualan Ditutup
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="w-full text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                    Belum ada arsip event selesai.
                </div>
            @endforelse
        </div>

        {{-- Tombol Geser Kanan --}}
        <button type="button" onclick="scrollSection('eventEndedTrack', 320)" aria-label="Berikutnya"
            class="hidden sm:flex absolute -right-3 sm:-right-5 top-[32%] -translate-y-1/2 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-100 border border-gray-200 dark:border-slate-700 shadow-md hover:shadow-xl hover:scale-110 active:scale-95 items-center justify-center transition-all opacity-90 hover:opacity-100 hover:text-[#0050ff] dark:hover:text-blue-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </button>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // ===== HERO CAROUSEL CONTROLS =====
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    let autoSlideInterval = null;

    function updateSlide(index) {
        if (!slides.length) return;
        currentSlide = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            if (i === currentSlide) {
                slide.classList.remove('opacity-0', 'z-0', 'pointer-events-none');
                slide.classList.add('opacity-100', 'z-10');
            } else {
                slide.classList.remove('opacity-100', 'z-10');
                slide.classList.add('opacity-0', 'z-0', 'pointer-events-none');
            }
        });

        dots.forEach((dot, i) => {
            if (i === currentSlide) {
                dot.classList.remove('w-2', 'bg-white/50');
                dot.classList.add('w-6', 'bg-white');
            } else {
                dot.classList.remove('w-6', 'bg-white');
                dot.classList.add('w-2', 'bg-white/50');
            }
        });
    }

    function nextSlide() {
        updateSlide(currentSlide + 1);
        resetAutoSlide();
    }

    function prevSlide() {
        updateSlide(currentSlide - 1);
        resetAutoSlide();
    }

    function goToSlide(index) {
        updateSlide(index);
        resetAutoSlide();
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(nextSlide, 5000);
    }

    if (slides.length > 1) {
        autoSlideInterval = setInterval(nextSlide, 5000);
    }

    // ===== HORIZONTAL SCROLLER =====
    function scrollSection(trackId, amount) {
        const track = document.getElementById(trackId);
        if (track) {
            track.scrollBy({ left: amount, behavior: 'smooth' });
        }
    }
</script>
@endpush
