@extends('layouts.app')

@section('title', 'Tiket Saya — SeTiket')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold gradient-text mb-2">Tiket Saya</h1>
            <p class="text-slate-400">
                {{ $tickets->count() }} tiket aktif. Tunjukkan QR di gerbang masuk untuk check-in.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
            Lihat semua pesanan &rarr;
        </a>
    </div>

    @forelse ($tickets as $ticket)
        @php
            $participant = $ticket->participant;
            $event = $participant->event;
            $category = $categories->get($participant->event_id . '|' . $participant->category);
        @endphp

        <div class="glass-panel rounded-2xl overflow-hidden mb-6">
            <div class="flex flex-col sm:flex-row">

                {{-- QR --}}
                <div class="bg-white p-6 flex flex-col items-center justify-center sm:w-56 shrink-0">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($ticket->qr_code) }}"
                        alt="QR {{ $ticket->ticket_code }}" class="w-36 h-36 object-contain">
                    <p class="mt-3 font-mono text-xs text-slate-700 font-bold tracking-wider text-center break-all">
                        {{ $ticket->ticket_code }}
                    </p>
                </div>

                {{-- Detail --}}
                <div class="flex-1 p-6 flex flex-col justify-between gap-4">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h2 class="text-xl font-bold text-white">{{ $event->title ?? 'Event' }}</h2>
                            <span
                                class="text-xs font-medium px-3 py-1 rounded-full border whitespace-nowrap {{ $ticket->isCheckedIn() ? 'bg-indigo-500/10 text-indigo-300 border-indigo-500/30' : 'bg-sky-500/10 text-sky-300 border-sky-500/30' }}">
                                {{ $ticket->statusLabel() }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-slate-500 mb-1">Peserta</dt>
                                <dd class="text-slate-200">{{ $participant->fullname }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500 mb-1">Kategori</dt>
                                <dd class="text-slate-200">{{ $category->name ?? $participant->category }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500 mb-1">Jersey</dt>
                                <dd class="text-slate-200">{{ $participant->jersey_size ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500 mb-1">Lokasi</dt>
                                <dd class="text-slate-200">{{ $event->location ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('ticket.show', $ticket->ticket_code) }}"
                            class="bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">
                            Buka E-Ticket
                        </a>
                        <a href="{{ route('ticket.pdf', $ticket->ticket_code) }}"
                            class="bg-slate-800/60 hover:bg-slate-700/60 border border-white/10 text-slate-200 text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">
                            Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="glass-panel rounded-2xl p-12 text-center">
            <p class="text-slate-300 mb-2 text-lg font-medium">Belum ada tiket aktif</p>
            <p class="text-slate-500 text-sm mb-6">
                Tiket muncul di sini setelah pembayaran pesanan Anda diverifikasi panitia.
            </p>
            <a href="{{ route('dashboard') }}"
                class="inline-block bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold px-6 py-3 rounded-lg transition-all">
                Cek Status Pesanan
            </a>
        </div>
    @endforelse
</div>
@endsection
