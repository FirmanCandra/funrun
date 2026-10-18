@extends('layouts.app')

@section('title', 'Tiket Saya — SeTiket')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-ink-900 dark:text-white mb-2">Tiket Saya</h1>
            <p class="text-ink-500 dark:text-slate-400">
                {{ $tickets->count() }} tiket aktif. Tunjukkan QR code di gerbang masuk untuk check-in.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline px-5 py-2.5 text-sm self-start sm:self-auto">
            Lihat Semua Pesanan
        </a>
    </div>

    @forelse ($tickets as $ticket)
        @php
            $participant = $ticket->participant;
            $event = $participant->event;
            $category = $categories->get($participant->event_id . '|' . $participant->category);
            $checkedIn = $ticket->isCheckedIn();
        @endphp

        {{-- Boarding pass: badan tiket + sobekan + bagian QR --}}
        <div class="card overflow-hidden mb-7 animate-fade-up">
            <div class="flex flex-col md:flex-row">

                {{-- Badan tiket --}}
                <div class="flex-1 p-7 sm:p-8 relative">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-widest text-ink-500 dark:text-slate-400 mb-1.5">Event</p>
                            <h2 class="font-display font-bold text-xl text-ink-900 dark:text-white leading-snug">
                                {{ $event->title ?? 'Event' }}
                            </h2>
                        </div>
                        <span class="badge border border-transparent {{ $checkedIn ? 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 dark:border-slate-700' : 'bg-green-50 dark:bg-emerald-950/60 text-green-700 dark:text-emerald-300 dark:border-emerald-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $checkedIn ? 'bg-gray-400' : 'bg-green-600 dark:bg-green-400' }}"></span>
                            {{ $ticket->statusLabel() }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-7">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-ink-500 dark:text-slate-400 mb-1">Peserta</dt>
                            <dd class="font-semibold text-ink-900 dark:text-white text-sm truncate">{{ $participant->displayName() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-ink-500 dark:text-slate-400 mb-1">Kategori</dt>
                            <dd class="font-semibold text-ink-900 dark:text-white text-sm">{{ $category->name ?? $participant->category }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-ink-500 dark:text-slate-400 mb-1">Tanggal</dt>
                            <dd class="font-semibold text-ink-900 dark:text-white text-sm">
                                {{ $event?->date ? \Carbon\Carbon::parse($event->date)->translatedFormat('d M Y') : '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-ink-500 dark:text-slate-400 mb-1">Jersey</dt>
                            <dd class="font-semibold text-ink-900 dark:text-white text-sm">{{ $participant->jersey_size ?? '—' }}</dd>
                        </div>
                    </dl>

                    <div class="pt-6 border-t border-dashed border-line dark:border-slate-800">
                        <p class="text-xs uppercase tracking-wider text-ink-500 dark:text-slate-400 mb-1">Venue</p>
                        <p class="text-sm text-ink-900 dark:text-slate-200 mb-5">{{ $event->location ?? '—' }}</p>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('ticket.show', $ticket->ticket_code) }}"
                                class="btn btn-primary px-5 py-2.5 text-sm">Lihat Detail</a>
                            <a href="{{ route('ticket.pdf', $ticket->ticket_code) }}"
                                class="btn btn-outline px-5 py-2.5 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                </svg>
                                Unduh PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Sisi QR, dipisah garis sobek --}}
                <div class="md:w-64 shrink-0 bg-gray-50 dark:bg-slate-900/60 md:border-l border-t md:border-t-0 border-dashed border-line dark:border-slate-800
                            p-7 flex flex-col items-center justify-center text-center relative">
                    {{-- Lubang sobekan --}}
                    <span class="hidden md:block absolute -left-3 -top-3 w-6 h-6 rounded-full bg-canvas dark:bg-[#0b0f19] border border-line dark:border-slate-800"></span>
                    <span class="hidden md:block absolute -left-3 -bottom-3 w-6 h-6 rounded-full bg-canvas dark:bg-[#0b0f19] border border-line dark:border-slate-800"></span>

                    <div class="bg-white p-3 rounded-2xl border border-line dark:border-slate-700 mb-4 shadow-xs">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($ticket->qr_code) }}"
                            alt="QR {{ $ticket->ticket_code }}" class="w-32 h-32 object-contain">
                    </div>

                    <p class="text-[11px] uppercase tracking-wider text-ink-500 dark:text-slate-400 mb-1">Kode Tiket</p>
                    <p class="font-mono text-xs font-bold text-ink-900 dark:text-white break-all leading-relaxed">
                        {{ $ticket->ticket_code }}
                    </p>
                </div>
            </div>
        </div>
    @empty
        <div class="card p-14 text-center">
            <span class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-slate-800 text-ink-500 dark:text-slate-400 border border-transparent dark:border-slate-700/60 flex items-center justify-center mx-auto mb-5 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z" />
                </svg>
            </span>
            <h2 class="font-display font-semibold text-xl text-ink-900 dark:text-white mb-2">Belum ada tiket aktif</h2>
            <p class="text-ink-500 dark:text-slate-400 text-sm mb-7 max-w-sm mx-auto">
                Tiket muncul di sini setelah pembayaran pesanan Anda diverifikasi panitia.
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary px-6 py-3">Cek Status Pesanan</a>
        </div>
    @endforelse
</div>
@endsection
