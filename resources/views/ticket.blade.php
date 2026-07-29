@extends('layouts.app')

@section('title', 'E-Ticket ' . $ticket->ticket_code . ' — SeTiket')

@php
    $participant = $ticket->participant;
    $event = $participant->event;
    $terbit = in_array($ticket->status, ['valid', 'checked-in'], true);
@endphp

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if(session('success'))
        <div class="rounded-btn bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800 mb-8 flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Boarding pass --}}
    <div class="card overflow-hidden animate-fade-up" id="eticket">

        <div class="bg-brand-700 px-8 py-6 text-white">
            <p class="text-xs uppercase tracking-[0.2em] text-brand-100 mb-1.5">E-Ticket</p>
            <h1 class="font-display font-bold text-2xl leading-snug">{{ $event->title ?? 'SeTiket' }}</h1>
            <p class="text-brand-100 text-sm mt-1.5">
                {{ $event?->date ? \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') : '—' }}
                &middot; {{ $event->location ?? '—' }}
            </p>
        </div>

        {{-- QR --}}
        <div class="p-8 text-center border-b border-dashed border-line relative">
            <span class="absolute -left-3 -bottom-3 w-6 h-6 rounded-full bg-canvas border border-line"></span>
            <span class="absolute -right-3 -bottom-3 w-6 h-6 rounded-full bg-canvas border border-line"></span>

            @if($terbit)
                <div class="inline-block bg-white p-4 rounded-card border border-line mb-5">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($ticket->qr_code) }}"
                        alt="QR Code" class="w-48 h-48 object-contain">
                </div>
            @else
                <div class="inline-flex w-56 h-56 items-center justify-center bg-gray-50 border-2 border-dashed border-line rounded-card mb-5">
                    <span class="text-ink-500 text-sm px-6 leading-relaxed">
                        QR code muncul setelah pembayaran diverifikasi panitia
                    </span>
                </div>
            @endif

            <p class="text-xs uppercase tracking-wider text-ink-500 mb-1">Kode Tiket</p>
            <p class="font-mono font-bold tracking-wider text-ink-900">{{ $ticket->ticket_code }}</p>
        </div>

        {{-- Detail --}}
        <div class="p-8 grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs uppercase tracking-wider text-ink-500 mb-1.5">Nama Peserta</p>
                <p class="font-semibold text-ink-900">{{ $participant->displayName() }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider text-ink-500 mb-1.5">Kategori</p>
                <p class="font-semibold text-ink-900">{{ $participant->category }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider text-ink-500 mb-1.5">Ukuran Jersey</p>
                <p class="font-semibold text-ink-900">{{ $participant->jersey_size ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider text-ink-500 mb-1.5">Status</p>
                <span class="badge {{ $terbit ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-ink-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $terbit ? 'bg-green-600' : 'bg-gray-400' }}"></span>
                    {{ $ticket->statusLabel() }}
                </span>
            </div>
        </div>

        <div class="px-8 py-5 bg-gray-50 border-t border-line text-sm text-ink-500 leading-relaxed">
            Tunjukkan QR code ini di meja registrasi untuk mengambil Race Pack dan nomor BIB Anda.
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-3 mt-8 no-print">
        <a href="{{ route('ticket.pdf', $ticket->ticket_code) }}" class="btn btn-primary px-6 py-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
            </svg>
            Unduh PDF
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline px-6 py-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2m2 4h6a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2Zm8-12V5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4h10Z" />
            </svg>
            Cetak
        </button>
        @auth
            @if(auth()->user()->isUser())
                <a href="{{ route('tickets') }}" class="btn btn-ghost px-6 py-3">Kembali ke Tiket Saya</a>
            @endif
        @endauth
    </div>
</div>

<style>
    @media print {
        header, footer, .no-print { display: none !important; }
        body { background: #fff; }
        #eticket { box-shadow: none; border: 1px solid #d1d5db; }
    }
</style>
@endsection
