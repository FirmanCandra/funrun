{{--
    Kartu event untuk grid — Space Dynamic style.

    Variabel: $ev (array event dari events.json), $grup (kelompok penyaring).
--}}
@php
    $sudahTerdaftar = $myEventIds->contains($ev['id']);
    $gratis = (int) ($ev['harga'] ?? 0) === 0;
    $kota = strtolower(trim(last(explode(',', $ev['lokasi'] ?? ''))));
@endphp

@php $cardIndex = isset($loop) ? $loop->index : (isset($cardIdx) ? $cardIdx : 0); @endphp
<div class="col-lg-4 col-sm-6 wow bounceInUp" data-wow-duration="1s" data-wow-delay="{{ 0.3 + ($cardIndex % 3) * 0.1 }}s">
<article class="event-card-sd event-card"
    data-search="{{ strtolower(($ev['nama'] ?? '') . ' ' . ($ev['lokasi'] ?? '')) }}"
    data-groups="{{ $grup }}{{ $gratis ? ' free' : '' }} kota:{{ $kota }}">

    <a href="{{ route('event.show', $ev['id']) }}" class="event-thumb" style="display:block;position:relative;overflow:hidden;">
        @include('partials.event-image', ['nama' => $ev['nama'], 'thumbnail' => $ev['thumbnail'] ?? null])

        {{-- Badge status --}}
        <span style="position:absolute;top:12px;left:12px;background:rgba(255,255,255,0.95);color:#2a2a2a;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;backdrop-filter:blur(4px);">
            {{ ucfirst($ev['kategori'] ?? 'Event') === 'Highlight' ? ' Pilihan' : ' Akan Datang' }}
        </span>

        @if($sudahTerdaftar)
            <span style="position:absolute;top:12px;right:12px;background:#22c55e;color:#fff;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;display:flex;align-items:center;gap:4px;">
                <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                </svg>
                Terdaftar
            </span>
        @endif

        @if($gratis)
            <span style="position:absolute;bottom:12px;left:12px;background:#22c55e;color:#fff;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;">
                GRATIS
            </span>
        @endif
    </a>

    <div class="event-body">
        <h4>
            <a href="{{ route('event.show', $ev['id']) }}">{{ $ev['nama'] }}</a>
        </h4>

        <div class="event-meta">
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

        <div class="event-footer">
            <div>
                <small style="font-size:11px;color:#6b7280;display:block;font-weight:500;">Mulai dari</small>
                <span style="font-size:17px;font-weight:700;color:{{ $gratis ? '#16a34a' : '#111827' }};">
                    {{ $gratis ? 'Gratis' : 'Rp' . number_format($ev['harga'], 0, ',', '.') }}
                </span>
            </div>
            @php $isClosed = $ev['is_closed'] ?? false; @endphp
            <a href="{{ $sudahTerdaftar ? route('dashboard') : route('event.show', $ev['id']) }}"
                class="{{ $sudahTerdaftar ? 'btn-ticket-registered' : ($isClosed ? 'btn-ticket-cta' : 'btn-ticket-cta') }}"
                @if($isClosed && !$sudahTerdaftar) style="background: #9ca3af; color: white; border-color: #9ca3af; cursor: not-allowed;" @endif>
                @if($sudahTerdaftar)
                    Lihat Pesanan
                @elseif($isClosed)
                    Ditutup
                @else
                    Pesan Tiket
                @endif
            </a>
        </div>
    </div>

</article>
</div>
