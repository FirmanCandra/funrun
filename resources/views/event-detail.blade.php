@extends('layouts.app')

@section('title', $event['nama'] . ' — SeTiket')

@php
    $sudahTerdaftar = $myEventIds->contains($event['id']);
    $gratis = (int) ($event['harga'] ?? 0) === 0;
    $petaUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($event['lokasi'] ?? '');
@endphp

@section('meta_description', Str::limit(strip_tags($event['deskripsi'] ?? ''), 155))
@section('og_image', !empty($event['thumbnail']) ? asset($event['thumbnail']) : asset('images/setiket.webp'))
@section('og_type', 'event')

@section('content')

{{-- ===== COVER ===== --}}
<section class="bg-white border-b border-line w-full overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 w-full overflow-hidden">
        <nav class="flex items-center gap-2 text-sm text-ink-500 mb-6 min-w-0 w-full overflow-hidden">
            <a href="{{ route('home') }}" class="shrink-0 hover:text-ink-900 transition-colors">Beranda</a>
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-ink-900 font-medium truncate min-w-0 flex-1">{{ $event['nama'] }}</span>
        </nav>

        <div class="rounded-card overflow-hidden animate-fade-in w-full" style="box-shadow: var(--shadow-card);">
            @include('partials.event-image', [
                'nama' => $event['nama'],
                'thumbnail' => $event['thumbnail'] ?? null,
                'class' => 'media-16-9 max-h-[460px]',
            ])
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 w-full overflow-x-hidden">
    <div class="grid lg:grid-cols-3 gap-6 lg:gap-10 w-full min-w-0">

        {{-- ===== KOLOM KIRI ===== --}}
        <div class="lg:col-span-2 space-y-6 sm:space-y-8 w-full min-w-0">

            <div class="animate-fade-up w-full min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="badge" style="background-color: var(--color-brand-50); color: var(--color-brand-700);">{{ ($event['kategori'] ?? '') === 'highlight' ? 'Event Pilihan' : 'Akan Datang' }}</span>
                    @if($gratis)<span class="badge" style="background-color: #f0fdf4; color: #16a34a;">Gratis</span>@endif
                    @if($sudahTerdaftar)
                        <span class="badge bg-green-600 text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                            </svg>
                            Anda sudah terdaftar
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-bold text-ink-900 leading-tight mb-6 break-words">{{ $event['nama'] }}</h1>

                {{-- Ringkasan waktu & tempat --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full min-w-0">
                    <div class="card p-4 sm:p-6 flex flex-row items-center gap-3.5 text-left overflow-hidden min-w-0 w-full">
                        <span class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-ink-500 mb-0.5">Tanggal & Waktu</p>
                            <p class="font-semibold text-ink-900 text-sm sm:text-base break-words">{{ $event['tanggal'] }}</p>
                            <p class="text-xs sm:text-sm text-ink-500 break-words">{{ $event['waktu'] }} WIB</p>
                        </div>
                    </div>

                    <div class="card p-4 sm:p-6 flex flex-row items-center gap-3.5 text-left overflow-hidden min-w-0 w-full">
                        <span class="w-11 h-11 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7 13.4 21a2 2 0 0 1-2.8 0l-4.3-4.3a8 8 0 1 1 11.4 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-ink-500 mb-0.5">Lokasi</p>
                            <p class="font-semibold text-ink-900 text-sm sm:text-base break-words">{{ $event['lokasi'] }}</p>
                            <a href="{{ $petaUrl }}" target="_blank" rel="noopener"
                                class="text-xs sm:text-sm text-brand-600 hover:text-brand-700 font-medium inline-flex items-center gap-1 mt-0.5">
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
                        class="btn-ticket-cta w-full py-3.5 text-base">
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

@push('scripts')
@php
    // Tanggal event disimpan sebagai string lokal (e.g. '25 Juli 2026').
    // Carbon biasanya bisa parse format ini, tapi kita beri fallback agar aman.
    try {
        $eventDateIso = \Carbon\Carbon::parse($event['tanggal'])->toIso8601String();
    } catch (\Exception $e) {
        $eventDateIso = now()->toIso8601String();
    }
@endphp
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "{{ $event['nama'] }}",
  "startDate": "{{ $eventDateIso }}",
  "endDate": "{{ $eventDateIso }}",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "eventStatus": "https://schema.org/EventScheduled",
  "location": {
    "@type": "Place",
    "name": "{{ $event['lokasi'] }}",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "{{ trim(last(explode(',', $event['lokasi'] ?? ''))) }}"
    }
  },
  "image": [
    "{{ !empty($event['thumbnail']) ? asset($event['thumbnail']) : asset('images/setiket.webp') }}"
  ],
  "description": "{{ Str::limit(strip_tags($event['deskripsi'] ?? ''), 155) }}",
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "price": "{{ $event['harga'] ?? 0 }}",
    "priceCurrency": "IDR",
    "availability": "https://schema.org/InStock",
    "validFrom": "{{ now()->toIso8601String() }}"
  }
}
</script>
@endpush
