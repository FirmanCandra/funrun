@extends('layouts.app')

@section('title', 'SeTiket — Platform Beli Tiket Konser & Event Terpercaya di Indonesia')

@php
    $trendingEvents = collect($highlightEvents);
    $upcoming = collect($upcomingEvents);
    $ended = collect($endedEvents ?? []);
    $allEvents = $trendingEvents->concat($upcoming)->concat($ended);

    $q = request('q', '');

    // Event untuk Carousel Hero Banner (utamakan event trending)
    $bannerEvents = $trendingEvents->isNotEmpty() ? $trendingEvents : $allEvents->take(5);
@endphp

@section('content')

{{-- ===== HERO BANNER CAROUSEL (LOKET STYLE) ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 sm:pt-7 pb-4">
    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg border border-gray-100 bg-gray-900" id="heroCarousel">
        
        {{-- Carousel Slides Container --}}
        <div class="relative h-[220px] sm:h-[380px] md:h-[440px] lg:h-[480px] overflow-hidden" id="carouselTrack">
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

                    {{-- Dark Overlay for readable text --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                    {{-- Banner Content & Floating CTA Pill (Loket Style) --}}
                    <div class="absolute bottom-5 sm:bottom-8 left-4 sm:left-8 right-4 sm:right-8 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3">
                        <div class="max-w-2xl text-white">
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
                            class="inline-flex items-center gap-2.5 px-4 sm:px-6 py-2.5 sm:py-3 rounded-full bg-white/95 hover:bg-white text-gray-900 hover:text-[#0050ff] font-bold text-xs sm:text-sm shadow-xl backdrop-blur-md transition-all transform hover:scale-105 shrink-0">
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
            class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-20 w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-sm flex items-center justify-center transition-all">
            <svg class="w-5 sm:w-6 h-5 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
        </button>
        <button type="button" onclick="nextSlide()" aria-label="Slide berikutnya"
            class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-20 w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-sm flex items-center justify-center transition-all">
            <svg class="w-5 sm:w-6 h-5 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
            </svg>
        </button>

        {{-- Carousel Dots Indicator --}}
        <div class="absolute bottom-3 left-4 sm:left-8 z-20 flex items-center gap-1.5" id="carouselDots">
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
            <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-[#0050ff] text-white flex items-center justify-center shadow-md shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm sm:text-base">
                            Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}!
                        </h4>
                        <p class="text-xs sm:text-sm text-gray-600">
                            @if($myTicketCount > 0)
                                Anda memiliki <span class="font-bold text-[#0050ff]">{{ $myTicketCount }} tiket aktif</span>.
                            @endif
                            @if($myPendingOrders > 0)
                                <span class="font-medium text-amber-700">{{ $myPendingOrders }} pesanan menunggu verifikasi transfer</span>.
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
    <div class="flex items-center justify-between mb-5">
        <div>
            <div class="flex items-center gap-2.5">
                {{-- Loket-style Animated Section Icon (GIF-like vector motion) --}}
                <div class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 relative flex items-center justify-center anim-trending-box" role="img" aria-label="Icon Trending">
                    <svg class="w-full h-full drop-shadow-sm" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="trendBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0050ff"/>
                                <stop offset="100%" stop-color="#00c0fa"/>
                            </linearGradient>
                        </defs>
                        <rect width="48" height="48" rx="14" fill="url(#trendBg)"/>
                        <path d="M10 38H38M10 30H38M10 22H38" stroke="white" stroke-opacity="0.18" stroke-width="1.5" stroke-dasharray="2 3"/>
                        <path d="M12 34L20 25L27 29L35 17" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <g class="anim-trending-arrow">
                            <path d="M29 17H35V23" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <circle cx="37" cy="11" r="2" fill="#fef08a" class="anim-sparkle"/>
                        <circle cx="13" cy="18" r="1.5" fill="#ffffff" opacity="0.8" class="anim-sparkle" style="animation-delay: 0.6s;"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                    Event yang Lagi Trending
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Event terpopuler dan paling banyak dicari minggu ini
            </p>
        </div>
        
        <div class="flex items-center gap-2">
            <button type="button" onclick="scrollSection('eventTrendingTrack', -320)" aria-label="Sebelumnya"
                class="w-9 h-9 rounded-full border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-[#0050ff] shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </button>
            <button type="button" onclick="scrollSection('eventTrendingTrack', 320)" aria-label="Berikutnya"
                class="w-9 h-9 rounded-full border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-[#0050ff] shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </button>
        </div>
    </div>

    {{-- Horizontal Scrollable Cards (Exact Loket Card Anatomy) --}}
    <div class="relative">
        <div class="flex gap-5 sm:gap-6 overflow-x-auto no-scrollbar scroll-smooth pb-4" id="eventTrendingTrack">
            @forelse($trendingEvents as $index => $event)
                <div class="min-w-[260px] sm:min-w-[280px] md:min-w-[290px] max-w-[290px] shrink-0">
                    <a href="{{ route('event.show', $event['id']) }}" class="block group text-decoration-none loket-event-card">
                        
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

                        {{-- Card Body (Loket exact typography) --}}
                        <div class="pt-3">
                            {{-- Kota / Lokasi (gray text) --}}
                            <p class="text-xs font-medium text-gray-500 truncate">
                                {{ $event['kota'] ?? trim(last(explode(',', $event['lokasi'] ?? ''))) }}
                            </p>

                            {{-- Judul Event (bold dark text) --}}
                            <h3 class="text-[15px] font-bold text-gray-900 group-hover:text-[#0050ff] transition-colors line-clamp-1 mt-0.5 leading-snug">
                                {{ $event['nama'] }}
                            </h3>

                            {{-- Penyelenggara (gray text) --}}
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                Oleh {{ $event['penyelenggara'] ?? 'SeTiket Official' }}
                            </p>

                            {{-- Harga (Loket format) --}}
                            <div class="mt-3 pt-2 border-t border-gray-100 flex items-baseline justify-between">
                                <div>
                                    <span class="text-[11px] text-gray-400 block font-normal leading-tight">Mulai dari</span>
                                    <span class="text-[15px] font-extrabold text-gray-900">
                                        {{ (int)($event['harga'] ?? 0) === 0 ? 'Gratis' : 'Rp' . number_format($event['harga'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-[#0050ff] group-hover:translate-x-1 transition-transform">
                                    Detail →
                                </span>
                            </div>
                        </div>

                    </a>
                </div>
            @empty
                <div class="w-full text-center py-8 text-gray-500 text-sm">
                    Tidak ada event trending saat ini.
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== SECTION 2: EVENT YANG AKAN DATANG ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-gray-100">
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2.5">
                {{-- Loket-style Animated Section Icon (GIF-like vector motion) --}}
                <div class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 relative flex items-center justify-center anim-calendar-box" role="img" aria-label="Icon Akan Datang">
                    <svg class="w-full h-full drop-shadow-sm" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="calBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#4f46e5"/>
                                <stop offset="100%" stop-color="#7c3aed"/>
                            </linearGradient>
                        </defs>
                        <rect width="48" height="48" rx="14" fill="url(#calBg)"/>
                        <rect x="11" y="14" width="26" height="24" rx="5" fill="white"/>
                        <path d="M11 19C11 16.2386 13.2386 14 16 14H32C34.7614 14 37 16.2386 37 19V22H11V19Z" fill="#e0e7ff"/>
                        <rect x="16" y="10" width="3" height="6" rx="1.5" fill="#facc15"/>
                        <rect x="29" y="10" width="3" height="6" rx="1.5" fill="#facc15"/>
                        <circle cx="17" cy="27" r="1.5" fill="#4f46e5"/>
                        <circle cx="24" cy="27" r="1.5" fill="#4f46e5"/>
                        <circle cx="31" cy="27" r="1.5" fill="#7c3aed"/>
                        <circle cx="17" cy="33" r="1.5" fill="#4f46e5"/>
                        <circle cx="24" cy="33" r="2.5" fill="#ef4444" class="anim-sparkle"/>
                        <circle cx="31" cy="33" r="1.5" fill="#9ca3af"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                    Event yang Akan Datang
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Jelajahi konser, festival, dan turnamen mendatang yang siap kamu hadiri
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($upcoming as $event)
            <div class="bg-white rounded-2xl border border-gray-100 p-2.5 shadow-sm loket-event-card">
                <a href="{{ route('event.show', $event['id']) }}" class="block group">
                    <div class="poster-wrapper">
                        @if(!empty($event['thumbnail']))
                            <img src="{{ $event['thumbnail'] }}" alt="{{ $event['nama'] }}" class="poster-img" loading="lazy">
                        @else
                            @include('partials.event-image', ['nama' => $event['nama'], 'thumbnail' => null, 'class' => 'poster-img'])
                        @endif
                    </div>
                    <div class="p-2 pt-3">
                        <span class="text-[11px] font-semibold text-[#0050ff] uppercase tracking-wider block truncate">
                            {{ $event['tag'] ?? 'Event Resmi' }}
                        </span>
                        <h3 class="text-sm sm:text-[15px] font-bold text-gray-900 group-hover:text-[#0050ff] transition-colors line-clamp-1 mt-1">
                            {{ $event['nama'] }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 truncate flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.5-5 7-8.5 7-12a7 7 0 1 0-14 0c0 3.5 2.5 7 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                            {{ $event['lokasi'] }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                            <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                            {{ $event['tanggal'] }}
                        </p>
                        
                        <div class="mt-3 pt-2 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-gray-400 block font-normal leading-tight">Mulai dari</span>
                                <span class="text-sm sm:text-base font-extrabold text-gray-900">
                                    {{ (int)($event['harga'] ?? 0) === 0 ? 'Gratis' : 'Rp' . number_format($event['harga'], 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="btn-loket text-xs py-1.5 px-3">Beli Tiket</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-8 text-gray-500 text-sm">
                Belum ada event mendatang yang terdaftar.
            </div>
        @endforelse
    </div>
</section>

{{-- ===== SECTION 3: EVENT YANG SUDAH BERAKHIR ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-gray-100">
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2.5">
                {{-- Loket-style Animated Section Icon (GIF-like vector motion) --}}
                <div class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 relative flex items-center justify-center anim-trophy-box" role="img" aria-label="Icon Event Selesai">
                    <svg class="w-full h-full drop-shadow-sm" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="trophyBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f59e0b"/>
                                <stop offset="100%" stop-color="#d97706"/>
                            </linearGradient>
                        </defs>
                        <rect width="48" height="48" rx="14" fill="url(#trophyBg)"/>
                        <path d="M17 14H31V23C31 26.866 27.866 30 24 30C20.134 30 17 26.866 17 23V14Z" fill="white"/>
                        <path d="M17 17H13C12.4477 17 12 17.4477 12 18V21C12 22.6569 13.3431 24 15 24H17" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M31 17H35C35.5523 17 36 17.4477 36 18V21C36 22.6569 34.6569 24 33 24H31" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M24 30V34M19 36H29" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M36 11L37 13L39 14L37 15L36 17L35 15L33 14L35 13L36 11Z" fill="#fef08a" class="anim-sparkle"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                    Event yang Sudah Berakhir
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Koleksi event legendaris yang sukses terselenggara bersama SeTiket
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ended as $event)
            <div class="bg-white rounded-2xl border border-gray-200/80 p-2.5 shadow-xs loket-event-card opacity-90 hover:opacity-100 transition-opacity">
                <a href="{{ route('event.show', $event['id']) }}" class="block group">
                    <div class="poster-wrapper filter grayscale-[20%] group-hover:grayscale-0 transition-all">
                        @if(!empty($event['thumbnail']))
                            <img src="{{ $event['thumbnail'] }}" alt="{{ $event['nama'] }}" class="poster-img" loading="lazy">
                        @else
                            @include('partials.event-image', ['nama' => $event['nama'], 'thumbnail' => null, 'class' => 'poster-img'])
                        @endif
                    </div>
                    <div class="p-2 pt-3">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block truncate">
                            {{ $event['tag'] ?? 'Arsip Event' }}
                        </span>
                        <h3 class="text-sm sm:text-[15px] font-bold text-gray-800 group-hover:text-[#0050ff] transition-colors line-clamp-1 mt-1">
                            {{ $event['nama'] }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 truncate flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.5-5 7-8.5 7-12a7 7 0 1 0-14 0c0 3.5 2.5 7 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                            {{ $event['lokasi'] }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                            <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                            {{ $event['tanggal'] }}
                        </p>
                        
                        <div class="mt-3 pt-2 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-gray-400 block font-normal leading-tight">Status</span>
                                <span class="text-xs font-bold text-gray-500">
                                    Penjualan Ditutup
                                </span>
                            </div>
                            <span class="px-3 py-1.5 rounded-xl border border-gray-200 text-gray-600 group-hover:text-[#0050ff] group-hover:border-[#0050ff] text-xs font-semibold transition-colors">
                                Detail Event
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-8 text-gray-500 text-sm">
                Belum ada arsip event selesai.
            </div>
        @endforelse
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
