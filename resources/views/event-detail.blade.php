@extends('layouts.app')

@section('title', $event['nama'] . ' — Detail Event')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8 mt-10">
    {{-- Back Button / Breadcrumbs --}}
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    {{-- Event Card / Container --}}
    <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl border border-white/10 p-6 md:p-8 space-y-8">
        {{-- Banner Image --}}
        <div class="relative overflow-hidden rounded-2xl aspect-[16/9] md:h-[400px]">
            <img src="{{ $event['thumbnail'] ?: 'https://placehold.co/1200x675/1a0a5e/ffffff?text='.rawurlencode($event['nama']) }}"
                 alt="{{ $event['nama'] }}"
                 class="w-full h-full object-cover"
                 onerror="this.src='https://placehold.co/1200x675/1a0a5e/ffffff?text=Event'">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
        </div>

        {{-- Header: Title & CTA --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-white/10">
            <div class="space-y-2">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    {{ $event['nama'] }}
                </h1>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    {{ ucfirst($event['kategori']) }} Event
                </div>
            </div>
            <div class="shrink-0">
                <a href="{{ route('register', ['event_id' => $event['id']]) }}"
                   class="inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-2xl font-bold text-lg transition-all transform hover:-translate-y-1 hover:shadow-2xl"
                   style="background: linear-gradient(135deg, #F5A623, #d48f1a); color: #0a0425; box-shadow: 0 4px 20px rgba(245,166,35,0.25);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    Beli Tiket Sekarang
                </a>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 bg-slate-900/50 p-6 rounded-2xl border border-white/5">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Lokasi</div>
                    <div class="text-sm font-semibold text-white truncate" title="{{ $event['lokasi'] }}">{{ $event['lokasi'] }}</div>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Tanggal</div>
                    <div class="text-sm font-semibold text-white truncate">{{ $event['tanggal'] }}</div>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Waktu</div>
                    <div class="text-sm font-semibold text-white truncate">{{ $event['waktu'] }}</div>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"/>
                        <circle cx="12" cy="12" r="2" stroke-width="2"/>
                        <path d="M6 12h.01M18 12h.01" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Harga Tiket</div>
                    <div class="text-sm font-semibold text-amber-400 truncate">
                        {{ $event['harga'] == 0 ? 'Gratis' : 'Rp' . number_format($event['harga'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- About This Event --}}
        <div class="space-y-3">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-full bg-blue-500"></span>
                About This Event
            </h2>
            <div class="text-gray-300 text-base leading-relaxed whitespace-pre-line">
                {!! e($event['deskripsi']) !!}
            </div>
        </div>

        {{-- Media Sosial Event --}}
        <div class="space-y-4 pt-4 border-t border-white/10">
            <h3 class="text-lg font-bold text-white">Media Sosial Event</h3>
            <div class="flex items-center gap-4">
                <a href="https://instagram.com" target="_blank" class="w-11 h-11 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-center text-gray-400 hover:text-pink-500 hover:border-pink-500/30 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2"/>
                        <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" stroke-width="2"/>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
                <a href="https://tiktok.com" target="_blank" class="w-11 h-11 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:border-white/30 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18a4 4 0 100-8c-1.127 0-2.155.433-2.923 1.142A4 4 0 005 14c0 2.21 1.79 4 4 4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V2h4a3 3 0 013 3v2"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Syarat dan Ketentuan (Accordion) --}}
        <div class="pt-6 border-t border-white/10">
            <div class="rounded-2xl border border-white/10 overflow-hidden bg-slate-900/30">
                <button id="accordionToggle" class="w-full flex items-center justify-between px-6 py-5 text-left text-white hover:bg-white/5 transition-colors font-bold text-lg">
                    <span>Syarat & Ketentuan</span>
                    <svg id="accordionIcon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="accordionContent" class="hidden border-t border-white/5 px-6 py-5 bg-slate-950/40 text-sm text-gray-300 leading-relaxed whitespace-pre-line">
                    {!! e($event['syarat_ketentuan']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('accordionToggle');
    const content = document.getElementById('accordionContent');
    const icon = document.getElementById('accordionIcon');

    toggle.addEventListener('click', function() {
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    });
});
</script>
@endsection
