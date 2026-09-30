@extends('layouts.app')

@section('title', 'SeTiket — Temukan & Pesan Tiket Event')

@php
    /**
     * Susun daftar event sekali supaya kartu di seluruh halaman memakai bentuk
     * data yang sama. Tidak ada query baru — semuanya dari variabel controller.
     */
    $allEvents = collect($highlightEvents)->concat($upcomingEvents);

    // Chip penyaring dari data yang benar-benar ada.
    $kotaTeratas = $allEvents
        ->map(fn ($e) => trim(last(explode(',', $e['lokasi'] ?? ''))))
        ->filter()
        ->countBy()
        ->sortDesc()
        ->keys()
        ->take(4);
@endphp

@section('content')

{{-- ===== HERO / MAIN BANNER ===== --}}
<div class="main-banner wow fadeIn" id="top" data-wow-duration="1s" data-wow-delay="0.5s">
    <div class="container">
        <div class="row align-items-center">

            {{-- Kiri: teks + form pencarian --}}
            <div class="col-lg-6">
                <div class="left-content header-text wow fadeInLeft" data-wow-duration="1s" data-wow-delay="0.8s">
                    <h6>Selamat Datang di SeTiket</h6>
                    <h2>
                        Temukan Event <em>Seru</em> &
                        <span>Pesan Tiket</span> Sekarang
                    </h2>
                    <p>
                        Platform pemesanan tiket fun run, festival, seminar, olahraga, dan pameran
                        paling mudah. e-Ticket dengan QR Code langsung tersimpan di akun Anda.
                    </p>

                    {{-- Form pencarian bergaya Space Dynamic --}}
                    <form action="{{ route('home') }}" method="GET" class="banner-search-form">
                        <svg style="width:18px;height:18px;color:rgba(255,255,255,0.8);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari nama event atau kota…" autocomplete="off">
                        <button type="submit">
                            <span class="hidden sm:inline">Cari Event</span>
                            <span class="inline sm:hidden">Cari</span>
                        </button>
                    </form>

                    {{-- Fitur highlights --}}
                    <div class="banner-feature-pills">
                        <span class="banner-pill">
                            <span class="dot-green"></span>
                            E-ticket dengan QR Code
                        </span>
                        <span class="banner-pill">
                            <span class="dot-green"></span>
                            Verifikasi panitia resmi
                        </span>
                        <span class="banner-pill">
                            <span class="dot-green"></span>
                            {{ $allEvents->count() }} event tersedia
                        </span>
                    </div>
                </div>
            </div>

            {{-- Kanan: kartu event unggulan --}}
            @php $sorot = $allEvents->first(); @endphp
            <div class="col-lg-6">
                <div class="right-image wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.5s">
                    @if($sorot)
                        <a href="{{ route('event.show', $sorot['id']) }}"
                            class="event-card-sd d-block" style="text-decoration:none;">
                            <div class="event-thumb">
                                @include('partials.event-image', [
                                    'nama'      => $sorot['nama'],
                                    'thumbnail' => $sorot['thumbnail'] ?? null,
                                ])
                                <span class="badge"
                                    style="position:absolute;top:14px;left:14px;background:#ea580c;color:#fff;font-size:11px;">
                                    Event Pilihan
                                </span>
                            </div>
                            <div class="event-body">
                                <h4>{{ $sorot['nama'] }}</h4>
                                <div class="event-meta">
                                    <span>
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7 13.4 21a2 2 0 0 1-2.8 0l-4.3-4.3a8 8 0 1 1 11.4 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        {{ $sorot['lokasi'] }}
                                    </span>
                                    <span>
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                        </svg>
                                        {{ $sorot['tanggal'] }}
                                    </span>
                                </div>
                                <div class="event-footer">
                                    <div>
                                        <small style="font-size:11px;color:#6b7280;display:block;font-weight:500;">Mulai dari</small>
                                        <span style="font-size:17px;font-weight:700;color:{{ (int)($sorot['harga']??0)===0 ? '#16a34a' : '#111827' }};">
                                            {{ (int)($sorot['harga']??0)===0 ? 'Gratis' : 'Rp'.number_format($sorot['harga'],0,',','.') }}
                                        </span>
                                    </div>
                                    <span class="btn-ticket-cta" style="font-size:13px;padding:9px 18px;">
                                        Pesan Tiket
                                    </span>
                                </div>
                            </div>
                        </a>
                    @else
                        <img src="{{ asset('vendor/space-dynamic/images/banner-right-image.png') }}" alt="SeTiket Events">
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ===== SAPAAN PESERTA YANG SUDAH LOGIN ===== --}}
@auth
    @if(auth()->user()->isUser() && ($myTicketCount > 0 || $myPendingOrders > 0))
        <div class="container" style="margin-top:30px;">
            <div class="greeting-card wow fadeIn d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3"
                data-wow-duration="0.5s">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:22px;height:22px;color:#2563eb;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-weight:600;color:#111827;margin:0;">
                            Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}!
                        </p>
                        <p style="font-size:14px;color:#6b7280;margin:0;">
                            @if($myTicketCount > 0){{ $myTicketCount }} tiket aktif.@endif
                            @if($myPendingOrders > 0){{ $myPendingOrders }} pesanan menunggu verifikasi.@endif
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($myTicketCount > 0)
                        <a href="{{ route('tickets') }}" class="btn-ticket-cta">Lihat Tiket Saya</a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn-ticket-registered">Pesanan Saya</a>
                </div>
            </div>
        </div>
    @endif
@endauth



{{-- ===== EVENT PILIHAN — Blog / Featured layout ===== --}}
@if(!isset($q) || $q === '')
<div id="blog" class="events-section" style="background:#fff;">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.25s">
                <div class="section-heading">
                    <h2>
                        Event <em>Pilihan</em> yang Paling
                        <span>Banyak Dicari</span>
                    </h2>
                    <p>Yang paling banyak dicari peserta bulan ini.</p>
                </div>
            </div>
        </div>

        <div id="featured" class="row g-4 mt-2">
            @forelse($highlightEvents as $ev)
                @include('partials.event-card', ['ev' => $ev, 'grup' => 'featured'])
            @empty
                <div class="col-12">
                    <p style="color:#6b7280;text-align:center;padding:40px 0;">Belum ada event pilihan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endif

{{-- ===== EVENT MENDATANG — horizontal list ===== --}}
<div id="events" class="events-section" style="background:#f8fafc;">
    <div class="container">
        <div class="section-heading mb-5 wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.2s">
            @if(!empty($q))
                <h2>Hasil <em>Pencarian</em> untuk: <span>"{{ $q }}"</span></h2>
                <p>
                    Ditemukan {{ count($upcomingEvents) + count($highlightEvents) }} event.
                    <a href="{{ route('home') }}" style="color:#2563eb;font-size:14px;font-weight:500;margin-left:8px;">← Tampilkan semua</a>
                </p>
            @else
                <h2>Event <em>Akan</em> <span>Datang</span></h2>
                <p>Jadwal terdekat yang masih membuka pendaftaran.</p>
            @endif
        </div>

        <div class="row g-4">
            @forelse($upcomingEvents as $ev)
                @php
                    $sudahTerdaftar = $myEventIds->contains($ev['id']);
                    $gratis = (int) ($ev['harga'] ?? 0) === 0;
                    $kota = strtolower(trim(last(explode(',', $ev['lokasi'] ?? ''))));
                @endphp
                <div class="col-lg-6 wow fadeInUp"
                    data-wow-duration="1s" data-wow-delay="{{ 0.2 + ($loop->index % 2) * 0.15 }}s">
                    <article class="event-card-h event-card"
                        data-search="{{ strtolower(($ev['nama']??'') . ' ' . ($ev['lokasi']??'')) }}"
                        data-groups="upcoming{{ $gratis ? ' free' : '' }} kota:{{ $kota }}">

                        <a href="{{ route('event.show', $ev['id']) }}" class="thumb">
                            @include('partials.event-image', [
                                'nama'      => $ev['nama'],
                                'thumbnail' => $ev['thumbnail'] ?? null,
                                'class'     => '',
                            ])
                            @if($sudahTerdaftar)
                                <span class="badge" style="position:absolute;top:10px;left:10px;background:#22c55e;color:#fff;">
                                    ✓ Terdaftar
                                </span>
                            @endif
                        </a>

                        <div class="body">
                            <div class="d-flex gap-2 mb-2" style="flex-wrap:wrap;">
                                <span class="badge" style="background:#eff6ff;color:#2563eb;">Akan Datang</span>
                                @if($gratis)<span class="badge" style="background:#f0fdf4;color:#16a34a;">Gratis</span>@endif
                            </div>

                            <h4 class="clamp-2">
                                <a href="{{ route('event.show', $ev['id']) }}">{{ $ev['nama'] }}</a>
                            </h4>

                            <div class="meta mt-2">
                                <span>
                                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7 13.4 21a2 2 0 0 1-2.8 0l-4.3-4.3a8 8 0 1 1 11.4 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    {{ $ev['lokasi'] }}
                                </span>
                                <span>
                                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                    </svg>
                                    {{ $ev['tanggal'] }}
                                </span>
                            </div>

                            <div class="footer">
                                <div>
                                    <small style="font-size:11px;color:#6b7280;display:block;font-weight:500;">Mulai dari</small>
                                    <span style="font-size:16px;font-weight:700;color:{{ $gratis ? '#16a34a' : '#111827' }};">
                                        {{ $gratis ? 'Gratis' : 'Rp'.number_format($ev['harga'],0,',','.') }}
                                    </span>
                                </div>
                                <a href="{{ $sudahTerdaftar ? route('dashboard') : route('event.show', $ev['id']) }}"
                                    class="{{ $sudahTerdaftar ? 'btn-ticket-registered' : 'btn-ticket-cta' }}">
                                    @if($sudahTerdaftar)
                                        Lihat Pesanan
                                    @else
                                        Pesan Tiket
                                    @endif
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12" style="text-align:center;padding:60px 0;">
                    @if(!empty($q))
                        <svg style="width:56px;height:56px;color:#d1d5db;margin:0 auto 16px;display:block;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                        </svg>
                        <p style="color:#374151;font-weight:600;font-size:16px;margin:0 0 8px;">Event tidak ditemukan</p>
                        <p style="color:#6b7280;margin:0 0 20px;">Tidak ada event yang cocok dengan <strong>"{{ $q }}"</strong>.</p>
                        <a href="{{ route('home') }}" class="btn-ticket-cta" style="text-decoration:none;">← Lihat Semua Event</a>
                    @else
                        <p style="color:#6b7280;">Belum ada event mendatang.</p>
                    @endif
                </div>
            @endforelse
        </div>

        <p id="searchEmpty" class="hidden text-center" style="color:#6b7280;padding:60px 0;display:none;">
            Tidak ada event yang cocok dengan pencarian Anda.
        </p>
    </div>
</div>

{{-- ===== CTA — Contact-style section ===== --}}
<div id="contact" class="cta-section wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-7 wow fadeInLeft" data-wow-duration="0.5s" data-wow-delay="0.25s">
                <div class="section-heading">
                    <h2 style="color:#fff;">
                        Jangan Sampai <em style="color:#fde68a;">Kehabisan</em>
                        <span style="color:#fed7aa;">Tiket</span> Favorit Anda
                    </h2>
                    <p>
                        Kuota event biasanya habis jauh sebelum hari-H.
                        Amankan tempat Anda sekarang — daftar hanya butuh beberapa menit.
                    </p>
                    <div class="cta-phone">
                        <h4>
                            Butuh bantuan? Hubungi kami:
                            <span>
                                <span class="phone-icon">
                                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 6.5C2 14.5 9.5 22 17.5 22c.66 0 1.3-.05 1.93-.13A1 1 0 0 0 20 21v-3.28a1 1 0 0 0-.75-.97l-3.28-.82a1 1 0 0 0-1.04.38l-.97 1.4a15.06 15.06 0 0 1-6.67-6.67l1.4-.97a1 1 0 0 0 .38-1.04l-.82-3.28A1 1 0 0 0 7.28 5H4a2 2 0 0 0-2 1.5Z"/>
                                    </svg>
                                </span>
                                <a href="https://wa.me/6289681201941" target="_blank" rel="noopener"
                                    style="color:#fde68a;font-size:15px;font-weight:500;">+62 896-8120-1941</a>
                            </span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mt-5 mt-lg-0 wow fadeInRight" data-wow-duration="0.5s" data-wow-delay="0.25s">
                <div style="background:#fff;border-radius:20px;padding:48px 36px;position:relative;">
                    <h3 style="font-size:22px;font-weight:700;color:#2a2a2a;margin-bottom:20px;">
                        Mulai Sekarang
                    </h3>
                    <p style="color:#6b7280;font-size:14px;margin-bottom:28px;">
                        Bergabunglah dengan ribuan peserta yang sudah memesan tiket melalui SeTiket.
                    </p>
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <a href="{{ route('home') }}#events" class="btn-ticket-cta" style="width:100%;padding:13px 24px;font-size:15px;">
                            Jelajahi Semua Event
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn-ticket-registered" style="width:100%;padding:12px 24px;font-size:14px;border:1.5px solid #2563eb;color:#2563eb;background:#eff6ff;">
                                Buat Akun Gratis
                            </a>
                        @endguest
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // ---- Filter kartu event ----
    (function () {
        const chips = document.querySelectorAll('.filter-chip');
        const cards = document.querySelectorAll('.event-card');
        const empty = document.getElementById('searchEmpty');
        if (!chips.length) return;

        chips.forEach(chip => chip.addEventListener('click', function () {
            const key = this.dataset.filter;
            chips.forEach(c => c.classList.toggle('is-active', c === this));

            let visible = 0;
            cards.forEach(card => {
                const groups = (card.dataset.groups || '').split(' ');
                const match = key === 'all' || groups.includes(key);
                card.closest('[class*="col"]').style.display = match ? '' : 'none';
                if (match) visible++;
            });

            if (empty) empty.style.display = (visible === 0) ? 'block' : 'none';
            document.getElementById('featured')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }));
    })();

    // ---- Pencarian dari navbar ----
    (function () {
        const inputs = [document.getElementById('navbarSearch'), document.getElementById('navbarSearchMobile')].filter(Boolean);
        if (!inputs.length) return;

        function filter(query) {
            const q = query.toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.event-card').forEach(card => {
                const haystack = (card.getAttribute('data-search') || '').toLowerCase();
                const match = haystack.includes(q);
                const col = card.closest('[class*="col"]');
                if (col) col.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const empty = document.getElementById('searchEmpty');
            if (empty) empty.style.display = (visible === 0 && q !== '') ? 'block' : 'none';
        }

        inputs.forEach(input => {
            input.addEventListener('input', function (e) {
                if (document.querySelector('.event-card')) {
                    filter(e.target.value);
                    inputs.forEach(other => { if (other !== e.target) other.value = e.target.value; });
                }
            });
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' && !document.querySelector('.event-card')) {
                    window.location.href = '{{ route('home') }}?q=' + encodeURIComponent(this.value);
                }
            });
        });

        const q = new URLSearchParams(window.location.search).get('q');
        if (q && document.querySelector('.event-card')) {
            inputs.forEach(i => { i.value = q; });
            filter(q);
            document.getElementById('events')?.scrollIntoView({ behavior: 'smooth' });
        }
    })();
</script>
@endpush

@endsection
