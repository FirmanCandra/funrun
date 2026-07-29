@extends('layouts.app')

@section('title', 'SeTiket — Temukan & Pesan Tiket Event')

@php
    /**
     * Susun daftar event sekali supaya kartu di seluruh halaman memakai bentuk
     * data yang sama. Tidak ada query baru — semuanya dari variabel controller.
     */
    $allEvents = collect($highlightEvents)->concat($upcomingEvents);

    // Chip penyaring dibangun dari data yang benar-benar ada.
    $kotaTeratas = $allEvents
        ->map(fn ($e) => trim(last(explode(',', $e['lokasi'] ?? ''))))
        ->filter()
        ->countBy()
        ->sortDesc()
        ->keys()
        ->take(4);
@endphp

@section('content')

{{-- ===== HERO ===== --}}
<section class="bg-white border-b border-line">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center py-16 lg:py-0 lg:min-h-[550px]">

            <div class="animate-fade-up">
                <span class="badge bg-brand-50 text-brand-700 mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                    Pendaftaran sedang dibuka
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-[3.4rem] font-bold leading-[1.08] text-ink-900 mb-5">
                    Temukan Event<br class="hidden sm:block"> Seru di Sekitar Anda
                </h1>

                <p class="text-lg text-ink-500 leading-relaxed mb-8 max-w-lg">
                    Pesan tiket fun run, festival, seminar, olahraga, dan pameran dengan mudah —
                    e-ticket langsung tersimpan di akun Anda.
                </p>

                {{-- Pencarian utama --}}
                <form action="{{ route('home') }}" method="GET" class="max-w-lg">
                    <div class="search-pill flex items-center gap-3 pl-5 pr-2 py-2">
                        <svg class="w-5 h-5 text-ink-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari nama event atau kota…"
                            class="w-full bg-transparent py-2 text-[15px] text-ink-900 placeholder-gray-400 focus:outline-none">
                        <button type="submit" class="btn btn-primary px-6 py-2.5 text-sm shrink-0">Cari</button>
                    </div>
                </form>

                <div class="flex flex-wrap items-center gap-x-8 gap-y-3 mt-8 text-sm text-ink-500">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                        E-ticket dengan QR code
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                        Verifikasi oleh panitia resmi
                    </span>
                </div>
            </div>

            {{-- Kartu event unggulan di kanan --}}
            @php $sorot = $allEvents->first(); @endphp
            <div class="hidden lg:block animate-fade-in">
                @if($sorot)
                    <a href="{{ route('event.show', $sorot['id']) }}" class="block card card-hover overflow-hidden">
                        <div class="relative">
                            @include('partials.event-image', [
                                'nama' => $sorot['nama'],
                                'thumbnail' => $sorot['thumbnail'] ?? null,
                            ])
                            <span class="badge bg-accent-500 text-white absolute top-4 left-4">Event Pilihan</span>
                        </div>

                        {{-- Keterangan diletakkan di bawah gambar, bukan menimpanya:
                             tanpa foto asli, teks putih di atas gambar jadi tidak terbaca. --}}
                        <div class="p-6">
                            <p class="font-display font-semibold text-xl text-ink-900 leading-snug clamp-2">
                                {{ $sorot['nama'] }}
                            </p>
                            <p class="text-ink-500 text-sm mt-2 flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7 13.4 21a2 2 0 0 1-2.8 0l-4.3-4.3a8 8 0 1 1 11.4 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                {{ $sorot['lokasi'] }}
                            </p>

                            <div class="flex items-center justify-between gap-4 mt-5 pt-5 border-t border-line">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-ink-900">{{ $allEvents->count() }} event tersedia</p>
                                        <p class="text-xs text-ink-500">Siap dipesan hari ini</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-brand-600 whitespace-nowrap">Lihat detail &rarr;</span>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ===== SAPAAN RINGKAS UNTUK PESERTA YANG SUDAH LOGIN ===== --}}
@auth
    @if(auth()->user()->isUser() && ($myTicketCount > 0 || $myPendingOrders > 0))
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-px">
            <div class="card p-5 mt-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-semibold text-ink-900">
                            Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}
                        </p>
                        <p class="text-sm text-ink-500">
                            @if($myTicketCount > 0)
                                {{ $myTicketCount }} tiket aktif siap dipakai check-in.
                            @endif
                            @if($myPendingOrders > 0)
                                {{ $myPendingOrders }} pesanan menunggu verifikasi panitia.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    @if($myTicketCount > 0)
                        <a href="{{ route('tickets') }}" class="btn btn-primary px-5 py-2.5 text-sm">Lihat Tiket</a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn btn-outline px-5 py-2.5 text-sm">Pesanan Saya</a>
                </div>
            </div>
        </section>
    @endif
@endauth

{{-- ===== KATEGORI / PENYARING ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16">
    <h2 class="text-xl font-bold text-ink-900 mb-1">Jelajahi berdasarkan</h2>
    <p class="text-sm text-ink-500 mb-6">Saring event sesuai yang Anda cari.</p>

    <div class="flex md:flex-wrap gap-3 overflow-x-auto md:overflow-visible pb-2 -mx-1 px-1">
        @php
            $chips = collect([
                ['key' => 'all', 'label' => 'Semua Event', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
                ['key' => 'featured', 'label' => 'Pilihan', 'icon' => 'm11.5 3.5 2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.5-4.8 2.5.9-5.4L3.7 9.2l5.4-.8 2.4-4.9Z'],
                ['key' => 'upcoming', 'label' => 'Akan Datang', 'icon' => 'M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z'],
                ['key' => 'free', 'label' => 'Gratis', 'icon' => 'M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8c1.1 0 2.1.4 2.6 1M12 8V7m0 1v8m0 0v1m9-5a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            ]);
        @endphp

        @foreach($chips as $chip)
            <button type="button" data-filter="{{ $chip['key'] }}"
                class="filter-chip card card-hover shrink-0 flex items-center gap-3 px-5 py-4 text-left cursor-pointer {{ $loop->first ? 'is-active' : '' }}">
                <span class="chip-icon w-10 h-10 rounded-xl bg-gray-100 text-ink-500 flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $chip['icon'] }}" />
                    </svg>
                </span>
                <span class="font-semibold text-sm text-ink-900 whitespace-nowrap">{{ $chip['label'] }}</span>
            </button>
        @endforeach

        @foreach($kotaTeratas as $kota)
            <button type="button" data-filter="kota:{{ strtolower($kota) }}"
                class="filter-chip card card-hover shrink-0 flex items-center gap-3 px-5 py-4 text-left cursor-pointer">
                <span class="chip-icon w-10 h-10 rounded-xl bg-gray-100 text-ink-500 flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7 13.4 21a2 2 0 0 1-2.8 0l-4.3-4.3a8 8 0 1 1 11.4 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </span>
                <span class="font-semibold text-sm text-ink-900 whitespace-nowrap">{{ $kota }}</span>
            </button>
        @endforeach
    </div>
</section>

{{-- ===== EVENT PILIHAN ===== --}}
<section id="featured" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16">
    <div class="flex items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-ink-900">Event Pilihan</h2>
            <p class="text-ink-500 mt-2">Yang paling banyak dicari peserta bulan ini.</p>
        </div>
        <a href="#events" class="hidden sm:inline-flex btn btn-ghost text-sm">
            Lihat semua
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <div id="events" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
        @forelse($highlightEvents as $ev)
            @include('partials.event-card', ['ev' => $ev, 'grup' => 'featured'])
        @empty
            <p class="col-span-full text-ink-500 py-8">Belum ada event pilihan.</p>
        @endforelse
    </div>
</section>

{{-- ===== EVENT MENDATANG (tata letak mendatar) ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
    <div class="mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-ink-900">Akan Datang</h2>
        <p class="text-ink-500 mt-2">Jadwal terdekat yang masih membuka pendaftaran.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($upcomingEvents as $ev)
            @php
                $sudahTerdaftar = $myEventIds->contains($ev['id']);
                $gratis = (int) ($ev['harga'] ?? 0) === 0;
            @endphp
            <article class="event-card card card-hover overflow-hidden flex flex-col sm:flex-row"
                data-search="{{ strtolower(($ev['nama'] ?? '') . ' ' . ($ev['lokasi'] ?? '')) }}"
                data-groups="upcoming{{ $gratis ? ' free' : '' }} kota:{{ strtolower(trim(last(explode(',', $ev['lokasi'] ?? '')))) }}">

                <a href="{{ route('event.show', $ev['id']) }}" class="sm:w-56 shrink-0 relative overflow-hidden">
                    @include('partials.event-image', [
                        'nama' => $ev['nama'],
                        'thumbnail' => $ev['thumbnail'] ?? null,
                        'class' => 'media-16-9 sm:h-full sm:aspect-auto',
                    ])
                    @if($sudahTerdaftar)
                        <span class="badge bg-green-600 text-white absolute top-3 left-3">Sudah Terdaftar</span>
                    @endif
                </a>

                <div class="p-6 flex flex-col flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge bg-brand-50 text-brand-700">Akan Datang</span>
                        @if($gratis)<span class="badge bg-green-50 text-green-700">Gratis</span>@endif
                    </div>

                    <a href="{{ route('event.show', $ev['id']) }}"
                        class="font-display font-semibold text-lg text-ink-900 leading-snug clamp-2 hover:text-brand-600 transition-colors">
                        {{ $ev['nama'] }}
                    </a>

                    <div class="mt-3 space-y-1.5 text-sm text-ink-500">
                        <p class="flex items-center gap-2 truncate">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7 13.4 21a2 2 0 0 1-2.8 0l-4.3-4.3a8 8 0 1 1 11.4 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ $ev['lokasi'] }}
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                            </svg>
                            {{ $ev['tanggal'] }}
                        </p>
                    </div>

                    <div class="mt-auto pt-5 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs text-ink-500">Mulai dari</p>
                            <p class="font-display font-bold text-lg text-accent-600">
                                {{ $gratis ? 'Gratis' : 'Rp' . number_format($ev['harga'], 0, ',', '.') }}
                            </p>
                        </div>
                        <a href="{{ $sudahTerdaftar ? route('dashboard') : route('event.show', $ev['id']) }}"
                            class="btn {{ $sudahTerdaftar ? 'btn-outline' : 'btn-primary' }} px-5 py-2.5 text-sm">
                            {{ $sudahTerdaftar ? 'Lihat Pesanan' : 'Pesan Tiket' }}
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <p class="col-span-full text-ink-500 py-8">Belum ada event mendatang.</p>
        @endforelse
    </div>

    <p id="searchEmpty" class="hidden text-center text-ink-500 py-16">
        Tidak ada event yang cocok dengan pencarian Anda.
    </p>
</section>

{{-- ===== CTA ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
    <div class="rounded-card overflow-hidden bg-brand-700 px-8 sm:px-14 py-14 sm:py-16 relative">
        <div class="absolute inset-0 opacity-20"
            style="background-image: radial-gradient(circle at 20% 20%, #fff 0, transparent 45%), radial-gradient(circle at 85% 70%, #fff 0, transparent 40%);"></div>

        <div class="relative max-w-2xl">
            <h2 class="text-3xl sm:text-4xl font-bold text-white leading-tight mb-4">
                Jangan sampai kehabisan tiket
            </h2>
            <p class="text-brand-100 text-lg leading-relaxed mb-8">
                Kuota event favorit biasanya habis jauh sebelum hari-H. Amankan tempat Anda sekarang.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#events" class="btn bg-white text-brand-700 hover:bg-brand-50 px-7 py-3.5">Jelajahi Event</a>
                @guest
                    <a href="{{ route('register') }}"
                        class="btn border border-white/40 text-white hover:bg-white/10 px-7 py-3.5">Buat Akun Gratis</a>
                @endguest
            </div>
        </div>
    </div>
</section>

@push('scripts')
<style>
    .filter-chip.is-active {
        border-color: var(--color-brand-600);
        box-shadow: 0 0 0 3px rgb(37 99 235 / 0.12);
    }
    .filter-chip.is-active .chip-icon {
        background: var(--color-brand-600);
        color: #fff;
    }
</style>
<script>
    // Penyaring kategori — bekerja atas kartu yang sudah ada di halaman.
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
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            if (empty) empty.classList.toggle('hidden', visible > 0);
            document.getElementById('featured')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }));
    })();
</script>
@endpush
@endsection
