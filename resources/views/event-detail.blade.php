@extends('layouts.app')

@section('title', $event['nama'] . ' — SeTiket')

@php
    $sudahTerdaftar = $myEventIds->contains($event['id']);
    $gratis = (int) ($event['harga'] ?? 0) === 0;
    $petaUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($event['lokasi'] ?? '');
@endphp

@section('content')

{{-- ===== COVER ===== --}}
<section class="bg-white border-b border-line">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <nav class="flex items-center gap-2 text-sm text-ink-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-ink-900 transition-colors">Beranda</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-ink-900 font-medium truncate">{{ $event['nama'] }}</span>
        </nav>

        <div class="rounded-card overflow-hidden animate-fade-in" style="box-shadow: var(--shadow-card);">
            @include('partials.event-image', [
                'nama' => $event['nama'],
                'thumbnail' => $event['thumbnail'] ?? null,
                'class' => 'media-16-9 max-h-[460px]',
            ])
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid lg:grid-cols-3 gap-10">

        {{-- ===== KOLOM KIRI ===== --}}
        <div class="lg:col-span-2 space-y-8">

            <div class="animate-fade-up">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="badge bg-brand-50 text-brand-700">
                        {{ ucfirst($event['kategori']) === 'Highlight' ? 'Event Pilihan' : 'Akan Datang' }}
                    </span>
                    @if($gratis)<span class="badge bg-green-50 text-green-700">Gratis</span>@endif
                    @if($sudahTerdaftar)
                        <span class="badge bg-green-600 text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                            </svg>
                            Anda sudah terdaftar
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl font-bold text-ink-900 leading-tight mb-6">{{ $event['nama'] }}</h1>

                {{-- Ringkasan waktu & tempat --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="card p-5 flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs text-ink-500 mb-0.5">Tanggal & Waktu</p>
                            <p class="font-semibold text-ink-900">{{ $event['tanggal'] }}</p>
                            <p class="text-sm text-ink-500">{{ $event['waktu'] }} WIB</p>
                        </div>
                    </div>

                    <div class="card p-5 flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7 13.4 21a2 2 0 0 1-2.8 0l-4.3-4.3a8 8 0 1 1 11.4 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs text-ink-500 mb-0.5">Lokasi</p>
                            <p class="font-semibold text-ink-900 truncate">{{ $event['lokasi'] }}</p>
                            <a href="{{ $petaUrl }}" target="_blank" rel="noopener"
                                class="text-sm text-brand-600 hover:text-brand-700 font-medium inline-flex items-center gap-1 mt-0.5">
                                Buka di Google Maps
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H18v4.5M18 6l-7.5 7.5M17 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h4" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="card p-7 sm:p-8">
                <h2 class="text-xl font-bold text-ink-900 mb-4">Tentang Event Ini</h2>
                <div class="text-ink-500 leading-[1.85] whitespace-pre-line">{!! e($event['deskripsi']) !!}</div>
            </div>

            {{-- Informasi tempat --}}
            <div class="card p-7 sm:p-8">
                <h2 class="text-xl font-bold text-ink-900 mb-5">Informasi Tempat</h2>
                <dl class="grid sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <dt class="text-ink-500 mb-1">Venue</dt>
                        <dd class="font-medium text-ink-900">{{ $event['lokasi'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-500 mb-1">Jam Acara</dt>
                        <dd class="font-medium text-ink-900">{{ $event['waktu'] }} WIB</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-ink-500 mb-2">Peta</dt>
                        <dd>
                            <a href="{{ $petaUrl }}" target="_blank" rel="noopener" class="btn btn-outline px-5 py-2.5 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20 3 17V4l6 3m0 13 6-3m-6 3V7m6 10 6 3V7l-6-3m0 13V4m0 0L9 7" />
                                </svg>
                                Lihat Lokasi di Google Maps
                            </a>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Syarat & ketentuan --}}
            <div class="card p-7 sm:p-8">
                <h2 class="text-xl font-bold text-ink-900 mb-4">Syarat &amp; Ketentuan</h2>
                <div class="text-ink-500 leading-[1.85] whitespace-pre-line text-sm">{!! e($event['syarat_ketentuan']) !!}</div>
            </div>
        </div>

        {{-- ===== KARTU PEMESANAN (STICKY) ===== --}}
        <div class="lg:col-span-1">
            <div class="card p-6 lg:sticky lg:top-24">
                <p class="text-sm text-ink-500 mb-1">Harga mulai dari</p>
                <p class="font-display text-3xl font-bold text-accent-600 mb-6">
                    {{ $gratis ? 'Gratis' : 'Rp' . number_format($event['harga'], 0, ',', '.') }}
                </p>

                @if($sudahTerdaftar)
                    <div class="rounded-btn bg-green-50 border border-green-200 px-4 py-3.5 mb-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                        <div>
                            <p class="font-semibold text-green-800 text-sm">Anda sudah terdaftar</p>
                            <p class="text-xs text-green-700 mt-0.5">Cek status pesanan Anda kapan saja.</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}" class="btn btn-primary w-full py-3.5 mb-3">Lihat Pesanan Saya</a>
                    <a href="{{ route('event.register', ['event_id' => $event['id']]) }}"
                        class="btn btn-outline w-full py-3.5">Pesan Lagi</a>
                @else
                    <a href="{{ route('event.register', ['event_id' => $event['id']]) }}"
                        class="btn btn-accent w-full py-3.5 text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z" />
                        </svg>
                        Pesan Tiket
                    </a>

                    @guest
                        <p class="text-xs text-ink-500 text-center mt-3 leading-relaxed">
                            <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-semibold">Masuk</a>
                            atau
                            <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-semibold">buat akun</a>
                            dulu untuk memesan.
                        </p>
                    @endguest
                @endif

                <ul class="mt-6 pt-6 border-t border-line space-y-3 text-sm text-ink-500">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                        E-ticket dengan QR code, langsung di akun Anda
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                        Bisa memesan beberapa tiket sekaligus
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                        Pembayaran diverifikasi panitia resmi
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
