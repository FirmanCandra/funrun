{{--
    Kartu event untuk grid.

    Variabel: $ev (array event dari events.json), $grup (kelompok penyaring).
--}}
@php
    $sudahTerdaftar = $myEventIds->contains($ev['id']);
    $gratis = (int) ($ev['harga'] ?? 0) === 0;
    $kota = strtolower(trim(last(explode(',', $ev['lokasi'] ?? ''))));
@endphp

<article class="event-card card card-hover overflow-hidden flex flex-col"
    data-search="{{ strtolower(($ev['nama'] ?? '') . ' ' . ($ev['lokasi'] ?? '')) }}"
    data-groups="{{ $grup }}{{ $gratis ? ' free' : '' }} kota:{{ $kota }}">

    <a href="{{ route('event.show', $ev['id']) }}" class="relative block overflow-hidden">
        @include('partials.event-image', ['nama' => $ev['nama'], 'thumbnail' => $ev['thumbnail'] ?? null])

        <span class="badge bg-white/95 text-ink-900 absolute top-3 left-3 backdrop-blur-sm">
            {{ ucfirst($ev['kategori'] ?? 'Event') === 'Highlight' ? 'Pilihan' : 'Akan Datang' }}
        </span>

        @if($sudahTerdaftar)
            <span class="badge bg-green-600 text-white absolute top-3 right-3">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                </svg>
                Terdaftar
            </span>
        @endif
    </a>

    <div class="p-6 flex flex-col flex-1">
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

        <div class="mt-auto pt-5 border-t border-line mt-5 flex items-end justify-between gap-4">
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
