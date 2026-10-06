@extends('layouts.app')

@section('title', $event['nama'] . ' — Beli Tiket Resmi di SeTiket')

@php
    $canonicalSlug = $event['slug'] ?? \Illuminate\Support\Str::slug($event['nama']);
    $canonicalUrl = route('event.show', ['identifier' => $canonicalSlug]);
    $currentUrl = $canonicalUrl;
    $sudahTerdaftar = $myEventIds->contains($event['id']);
    $gratis = (int) ($event['harga'] ?? 0) === 0;
    $petaUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($event['lokasi'] ?? '');
    $shareText = rawurlencode('Yuk nonton ' . $event['nama'] . ' di SeTiket! Beli tiket resminya di sini: ' . $canonicalUrl);
@endphp

@section('content')

{{-- ===== HERO BANNER DENGAN DARK ATMOSPHERIC BACKDROP (LOKET SCREENSHOT 3) ===== --}}
<div class="relative bg-slate-950 text-white overflow-hidden py-10 sm:py-14 border-b border-slate-800">
    
    {{-- Blurred Backdrop from Poster Image --}}
    @if(!empty($event['thumbnail']))
        <div class="absolute inset-0 bg-cover bg-center filter blur-3xl opacity-20 transform scale-110 pointer-events-none"
            style="background-image: url('{{ $event['thumbnail'] }}');"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/90 to-slate-900/80 pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('home') }}#events" class="hover:text-white transition-colors">Event</a>
            <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            <span class="text-white font-medium truncate max-w-xs">{{ $event['nama'] }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left: Event Details (Loket screenshot 3) --}}
            <div class="lg:col-span-7 xl:col-span-8 space-y-5">
                
                {{-- Tags / Badges --}}
                <div class="flex flex-wrap items-center gap-2">
                    @if(($event['kategori'] ?? '') === 'ended')
                        <span class="px-3 py-1 rounded-full bg-gray-500/30 text-gray-300 border border-gray-500/40 text-xs font-bold uppercase tracking-wider">
                            Event Selesai
                        </span>
                    @elseif(($event['kategori'] ?? '') === 'highlight')
                        <span class="px-3 py-1 rounded-full bg-[#0050ff]/20 text-[#0050ff] border border-[#0050ff]/30 text-xs font-bold uppercase tracking-wider">
                            Event Pilihan
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-[#0050ff]/20 text-[#0050ff] border border-[#0050ff]/30 text-xs font-bold uppercase tracking-wider">
                            Akan Datang
                        </span>
                    @endif
                    @if($gratis)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold">
                            Tiket Gratis
                        </span>
                    @endif
                    @if($sudahTerdaftar)
                        <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            Anda Sudah Terdaftar
                        </span>
                    @endif
                </div>

                {{-- Judul Event Besar --}}
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-white leading-tight tracking-tight">
                    {{ $event['nama'] }}
                </h1>

                {{-- Metadata: Venue, Tanggal/Waktu, Kategori --}}
                <div class="space-y-3 pt-2 text-sm text-gray-300">
                    {{-- Venue --}}
                    <div class="flex items-start gap-3">
                        <span class="text-red-400 shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        </span>
                        <div>
                            <span class="font-medium text-white">{{ $event['lokasi'] }}</span>
                            <a href="{{ $petaUrl }}" target="_blank" rel="noopener" class="text-xs text-[#0050ff] hover:underline ml-2">
                                (Lihat Peta Google Maps)
                            </a>
                        </div>
                    </div>

                    {{-- Tanggal & Waktu --}}
                    <div class="flex items-center gap-3">
                        <span class="text-blue-400 shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        </span>
                        <div>
                            <span class="font-medium text-white">{{ $event['tanggal'] }}</span>
                            <span class="text-gray-400 text-xs ml-1.5">• {{ $event['waktu'] ?? '16.00 - 23.00' }} WIB</span>
                        </div>
                    </div>

                    {{-- Kategori / Tag --}}
                    <div class="flex items-center gap-3">
                        <span class="text-purple-400 shrink-0">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.999-3.199a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                        </span>
                        <div class="text-gray-300 text-xs sm:text-sm">
                            {{ $event['tag'] ?? 'Komunitas & Hiburan • Musik & Festival' }}
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: Loket Floating Booking Card (Loket Screenshot 3) --}}
            <div class="lg:col-span-5 xl:col-span-4">
                <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden text-gray-900 sticky top-[118px]">
                    
                    {{-- Poster 16:9 di bagian atas kartu --}}
                    <div class="aspect-video relative overflow-hidden bg-gray-100">
                        @if(!empty($event['thumbnail']))
                            <img src="{{ $event['thumbnail'] }}" alt="{{ $event['nama'] }}" class="w-full h-full object-cover">
                        @else
                            @include('partials.event-image', [
                                'nama' => $event['nama'],
                                'thumbnail' => null,
                                'class' => 'w-full h-full object-cover'
                            ])
                        @endif
                    </div>

                    {{-- Booking Body --}}
                    <div class="p-5 sm:p-6 space-y-5">
                        
                        {{-- Harga Mulai Dari --}}
                        <div class="flex items-baseline justify-between">
                            <div>
                                <span class="text-xs text-gray-400 block font-medium">Harga mulai dari</span>
                                <span class="text-2xl sm:text-3xl font-black text-gray-900">
                                    {{ $gratis ? 'Gratis' : 'Rp' . number_format($event['harga'], 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg {{ ($event['kategori'] ?? '') === 'ended' ? 'bg-gray-100 text-gray-500' : 'bg-blue-50 text-[#0050ff]' }}">
                                {{ ($event['kategori'] ?? '') === 'ended' ? 'Event Selesai' : 'Tiket Resmi' }}
                            </span>
                        </div>

                        {{-- Tombol Utama Beli Tiket (Loket Vibrant Blue) --}}
                        <div>
                            @if(($event['kategori'] ?? '') === 'ended')
                                <div class="w-full py-3.5 px-6 rounded-xl bg-gray-100 text-gray-400 font-bold text-sm text-center border border-gray-200 cursor-not-allowed">
                                    Penjualan Tiket Ditutup
                                </div>
                            @elseif($sudahTerdaftar)
                                <div class="space-y-2">
                                    <a href="{{ route('dashboard') }}"
                                        class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm text-center flex items-center justify-center gap-2 shadow-md transition-colors">
                                        <span>Lihat Pesanan Saya</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                    </a>
                                    <a href="{{ route('event.register', ['event_id' => $event['id']]) }}"
                                        class="w-full py-2.5 px-4 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs text-center block transition-colors">
                                        Pesan Tiket Tambahan
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('event.register', ['event_id' => $event['id']]) }}"
                                    class="w-full py-3.5 px-6 rounded-xl bg-[#0050ff] hover:bg-[#0043d4] text-white font-bold text-base text-center block shadow-[0_6px_20px_rgba(0,80,255,0.35)] hover:shadow-[0_8px_25px_rgba(0,80,255,0.45)] transition-all transform hover:-translate-y-0.5">
                                    Beli Tiket
                                </a>
                            @endif
                        </div>

                        {{-- Diselenggarakan Oleh (Loket style) --}}
                        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-[#0050ff] flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(mb_substr($event['penyelenggara'] ?? 'LK', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] text-gray-400 block">Diselenggarakan oleh</span>
                                <h4 class="text-sm font-bold text-gray-900 truncate">
                                    {{ $event['penyelenggara'] ?? 'LOKET Official Organizer' }}
                                </h4>
                            </div>
                        </div>

                        {{-- Bagikan Event (Loket Share Icons) --}}
                        <div class="pt-3 border-t border-gray-100">
                            <span class="text-xs font-bold text-gray-700 block mb-2.5">Bagikan Event</span>
                            <div class="flex items-center gap-2.5">
                                {{-- Salin Link --}}
                                <button type="button" onclick="copyEventLink()" id="copyBtn" aria-label="Salin Tautan"
                                    class="w-9 h-9 rounded-full border border-gray-200 hover:border-gray-300 hover:bg-gray-50 flex items-center justify-center text-gray-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-3.051a4.5 4.5 0 0 0-1.242-7.244l-4.5-4.5a4.5 4.5 0 0 0-6.364 6.364l1.757 1.757"/></svg>
                                </button>

                                {{-- WhatsApp --}}
                                <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" rel="noopener" aria-label="Bagikan ke WhatsApp"
                                    class="w-9 h-9 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.187-2.59-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.311.045-.698.058-2.029-.496-1.554-.648-2.531-2.228-2.608-2.33-.078-.104-.627-.836-.627-1.592 0-.756.395-1.127.536-1.282.14-.155.307-.194.409-.194.103 0 .205.002.296.006.096.004.225-.036.352.27.13.313.444 1.084.483 1.163.039.078.064.17.013.272-.051.103-.077.167-.154.256-.076.09-.161.2-.23.269-.077.077-.157.161-.067.315.09.155.4 0.658.857 1.066.589.524 1.085.688 1.24.765.154.077.243.064.333-.038.09-.103.385-.449.487-.603.103-.154.205-.128.346-.077.141.051.897.423 1.05.5.154.077.256.115.295.18.038.064.038.371-.106.776z"/></svg>
                                </a>

                                {{-- Facebook --}}
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($currentUrl) }}" target="_blank" rel="noopener" aria-label="Bagikan ke Facebook"
                                    class="w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.667 5H18V0h-3.808C10.595 0 9 1.582 9 4.615V8z"/></svg>
                                </a>

                                {{-- X / Twitter --}}
                                <a href="https://twitter.com/intent/tweet?text={{ $shareText }}" target="_blank" rel="noopener" aria-label="Bagikan ke X"
                                    class="w-9 h-9 rounded-full bg-black hover:bg-gray-800 text-white flex items-center justify-center transition-colors">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>

                                <span id="copyToast" class="hidden text-xs text-emerald-600 font-bold ml-2">Tautan Disalin!</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- ===== LOKET TABS SYSTEM (SCREENSHOT 3) ===== --}}
<div class="border-b border-gray-200 bg-white sticky top-[106px] z-30 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex space-x-8" aria-label="Tabs" id="eventTabNav">
            <button type="button" onclick="switchTab('deskripsi')" id="tabBtn-deskripsi"
                class="tab-btn py-4 px-1 border-b-2 font-bold text-sm text-[#0050ff] border-[#0050ff] transition-colors">
                Deskripsi
            </button>
            <button type="button" onclick="switchTab('tiket')" id="tabBtn-tiket"
                class="tab-btn py-4 px-1 border-b-2 font-bold text-sm text-gray-500 border-transparent hover:text-gray-900 transition-colors">
                Tiket
            </button>
            <button type="button" onclick="switchTab('syarat')" id="tabBtn-syarat"
                class="tab-btn py-4 px-1 border-b-2 font-bold text-sm text-gray-500 border-transparent hover:text-gray-900 transition-colors">
                Syarat dan Ketentuan
            </button>
        </nav>
    </div>
</div>

{{-- ===== TAB CONTENT CONTAINER ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Main Left Content --}}
        <div class="lg:col-span-8">

            {{-- TAB 1: DESKRIPSI --}}
            <div id="tabContent-deskripsi" class="tab-content space-y-8">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">
                        Tentang Acara Ini
                    </h2>
                    <div class="prose max-w-none text-gray-600 leading-relaxed whitespace-pre-line text-sm sm:text-base">
                        {!! e($event['deskripsi']) !!}
                    </div>
                </div>

                {{-- Informasi Venue & Peta --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">
                        Lokasi & Venue Acara
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="text-red-500 shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-gray-900 text-base">{{ $event['lokasi'] }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Waktu: {{ $event['waktu'] ?? '16.00 - 23.00' }} WIB</p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <a href="{{ $petaUrl }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 hover:border-[#0050ff] hover:text-[#0050ff] text-gray-700 text-xs sm:text-sm font-semibold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20 3 17V4l6 3m0 13 6-3m-6 3V7m6 10 6 3V7l-6-3m0 13V4m0 0L9 7"/></svg>
                                <span>Buka Lokasi di Google Maps</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: TIKET (REAL CATEGORIES LIST) --}}
            <div id="tabContent-tiket" class="tab-content hidden space-y-5">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Pilihan Kategori Tiket</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih kategori tiket yang sesuai kebutuhan Anda</p>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            Kuota Tersedia
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($categories as $cat)
                            <div class="border border-gray-200 hover:border-[#0050ff] rounded-2xl p-5 transition-all hover:shadow-md bg-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-gray-900 text-base">{{ $cat->name }}</h3>
                                        @if(!empty($cat->code))
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 uppercase">
                                                {{ $cat->code }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Termasuk e-ticket resmi, barcode QR check-in, dan akses gate utama acara.
                                    </p>
                                </div>

                                <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 pt-3 sm:pt-0">
                                    <div class="text-left sm:text-right">
                                        <span class="text-[11px] text-gray-400 block">Harga</span>
                                        <span class="text-lg font-black text-gray-900">
                                            {{ (int)$cat->price === 0 ? 'Gratis' : 'Rp' . number_format($cat->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('event.register', ['event_id' => $event['id']]) }}"
                                        class="px-5 py-2.5 rounded-xl bg-[#0050ff] hover:bg-[#0043d4] text-white text-xs font-bold transition-all shadow-sm">
                                        Pilih Tiket
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 text-sm">
                                Pilihan tiket akan segera dirilis oleh panitia.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                        <a href="{{ route('event.register', ['event_id' => $event['id']]) }}"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-[#0050ff] hover:bg-[#0043d4] text-white font-bold text-sm shadow-md transition-all">
                            <span>Lanjutkan ke Pengisian Formulir Pemesanan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- TAB 3: SYARAT DAN KETENTUAN --}}
            <div id="tabContent-syarat" class="tab-content hidden space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">
                        Syarat & Ketentuan Pembelian
                    </h2>
                    <div class="prose max-w-none text-gray-600 leading-relaxed whitespace-pre-line text-sm sm:text-base">
                        {!! e($event['syarat_ketentuan']) !!}
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Side Promotion Column --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 p-6">
                <h4 class="font-bold text-gray-900 text-base mb-2">Perlindungan Pembelian SeTiket</h4>
                <ul class="space-y-2.5 text-xs text-gray-600">
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </span>
                        E-Ticket terbit langsung dengan QR Code unik anti-duplikat.
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </span>
                        Verifikasi pembayaran resmi oleh panitia acara.
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </span>
                        Bantuan cepat via Customer Service WhatsApp jika ada kendala.
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-gray-200 p-6 bg-white">
                <h4 class="font-bold text-gray-900 text-sm mb-1">Punya Pertanyaan Seputar Event?</h4>
                <p class="text-xs text-gray-500 mb-4">Hubungi tim customer service kami untuk bantuan informasi tiket.</p>
                <a href="https://wa.me/6289681201941" target="_blank" rel="noopener"
                    class="w-full py-2.5 px-4 rounded-xl border border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-bold text-xs text-center block transition-colors">
                    Chat WhatsApp Customer Support
                </a>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tab switcher
    function switchTab(tabName) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        
        // Nonaktifkan semua tab button
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-[#0050ff]', 'border-[#0050ff]');
            btn.classList.add('text-gray-500', 'border-transparent');
        });

        // Tampilkan tab yang dipilih
        const activeContent = document.getElementById('tabContent-' + tabName);
        if (activeContent) activeContent.classList.remove('hidden');

        // Aktifkan button yang dipilih
        const activeBtn = document.getElementById('tabBtn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-500', 'border-transparent');
            activeBtn.classList.add('text-[#0050ff]', 'border-[#0050ff]');
        }
    }

    // Salin link event dengan URL slug rapi (misal: setiket.id/event/nama-event)
    function copyEventLink() {
        const urlToCopy = '{{ $canonicalUrl }}' || window.location.href;
        
        const showToast = () => {
            const toast = document.getElementById('copyToast');
            if (toast) {
                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 2500);
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(urlToCopy).then(showToast).catch(() => {
                fallbackCopy(urlToCopy, showToast);
            });
        } else {
            fallbackCopy(urlToCopy, showToast);
        }
    }

    function fallbackCopy(text, callback) {
        const temp = document.createElement('textarea');
        temp.value = text;
        temp.style.position = 'fixed';
        temp.style.opacity = '0';
        document.body.appendChild(temp);
        temp.focus();
        temp.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (e) {
            console.error('Fallback copy failed', e);
        }
        document.body.removeChild(temp);
    }
</script>
@endpush
