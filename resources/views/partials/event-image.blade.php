{{--
    Gambar event dengan cadangan yang dirender sendiri.

    Kalau event punya thumbnail, tampilkan fotonya. Kalau belum, jangan pakai
    layanan gambar dari internet — gambar itu gagal saat offline dan hasilnya
    kotak abu-abu polos. Cadangannya digambar lokal: panel bertekstur dengan
    inisial event, sehingga kartu tetap terlihat rapi tanpa foto.

    Variabel: $nama, $thumbnail (boleh kosong), $class (opsional).
--}}
@php
    $kelas = $class ?? 'media-16-9';

    // Warna panel dibuat konsisten per event supaya tidak berubah-ubah tiap muat.
    $palet = [
        ['#1d4ed8', '#3b82f6'],
        ['#0f766e', '#14b8a6'],
        ['#b45309', '#f59e0b'],
        ['#9d174d', '#ec4899'],
        ['#4338ca', '#6366f1'],
        ['#166534', '#22c55e'],
    ];
    [$dari, $ke] = $palet[crc32($nama) % count($palet)];

    $inisial = collect(preg_split('/\s+/', trim($nama)))
        ->filter()
        ->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
@endphp

@if (!empty($thumbnail))
    <img src="{{ $thumbnail }}" alt="{{ $nama }}" class="{{ $kelas }}" loading="lazy">
@else
    <div class="{{ $kelas }} relative flex items-center justify-center overflow-hidden"
        style="background: linear-gradient(135deg, {{ $dari }} 0%, {{ $ke }} 100%);"
        role="img" aria-label="{{ $nama }}">

        {{-- Motif tipis supaya panel tidak terasa datar --}}
        <div class="absolute inset-0 opacity-25"
            style="background-image:
                radial-gradient(circle at 18% 22%, rgba(255,255,255,.55) 0, transparent 42%),
                radial-gradient(circle at 82% 78%, rgba(255,255,255,.35) 0, transparent 38%);"></div>

        <span class="relative font-display font-bold text-white/95 leading-none select-none"
            style="font-size: clamp(1.75rem, 6vw, 3.25rem); letter-spacing: .04em;">
            {{ $inisial }}
        </span>

        <svg class="absolute bottom-3 right-3 w-6 h-6 text-white/40" fill="none" stroke="currentColor"
            stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z" />
        </svg>
    </div>
@endif
