@extends('layouts.app')

@section('title', 'Pesanan Saya — SeTiket')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold gradient-text mb-2">Pesanan Saya</h1>
            <p class="text-slate-400">Halo, {{ auth()->user()->name }}. Ini riwayat pemesanan tiket Anda.</p>
        </div>
        <a href="{{ route('tickets') }}"
            class="bg-slate-800/60 hover:bg-slate-700/60 border border-white/10 text-slate-200 text-sm font-semibold px-5 py-2.5 rounded-lg transition-all whitespace-nowrap text-center">
            Lihat Tiket Saya
        </a>
    </div>

    @if (session('success'))
        <div class="mb-8 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($orders as $order)
        @php
            $statusClass = match ($order->payment_status) {
                \App\Models\Order::STATUS_PAID => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
                \App\Models\Order::STATUS_REJECTED => 'bg-red-500/10 text-red-300 border-red-500/30',
                default => 'bg-amber-500/10 text-amber-300 border-amber-500/30',
            };
        @endphp

        <div class="glass-panel rounded-2xl p-6 sm:p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white mb-1">{{ $order->event->title ?? 'Event' }}</h2>
                    <p class="text-sm text-slate-400">
                        <span class="font-mono">{{ $order->order_code }}</span>
                        &middot; {{ $order->tickets->count() }} tiket
                        &middot; {{ $order->created_at->format('d M Y') }}
                    </p>
                </div>
                <span class="text-xs font-medium px-3 py-1 rounded-full border whitespace-nowrap {{ $statusClass }}">
                    {{ $order->statusLabel() }}
                </span>
            </div>

            @if ($order->isRejected() && $order->rejection_reason)
                <div class="mb-6 rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-300">
                    <span class="font-semibold">Ditolak panitia:</span> {{ $order->rejection_reason }}
                </div>
            @endif

            {{-- Daftar peserta dalam pesanan --}}
            <div class="border border-white/5 rounded-xl divide-y divide-white/5 mb-6">
                @foreach ($order->tickets as $ticket)
                    @php
                        $participant = $ticket->participant;
                        $category = $categories->get($order->event_id . '|' . $participant?->category);
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 text-sm">
                        <div class="min-w-0">
                            <div class="text-slate-200 font-medium truncate">{{ $participant->fullname ?? '—' }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $category->name ?? $participant?->category }}
                                &middot; Jersey {{ $participant->jersey_size ?? '—' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs text-slate-400">{{ $ticket->ticket_code }}</span>
                            @if ($ticket->isIssued())
                                <a href="{{ route('ticket.show', $ticket->ticket_code) }}"
                                    class="text-xs font-semibold text-sky-400 hover:text-sky-300 transition-colors whitespace-nowrap">
                                    E-Ticket
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm">
                    <span class="text-slate-500">Total</span>
                    <span class="text-lg font-bold text-white ml-2">
                        Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('orders.show', $order) }}"
                        class="bg-slate-800/60 hover:bg-slate-700/60 border border-white/10 text-slate-200 text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">
                        Detail Pesanan
                    </a>
                    @if ($order->isPaid())
                        <a href="{{ route('tickets') }}"
                            class="bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">
                            Lihat Tiket
                        </a>
                    @endif
                </div>
            </div>

            @unless ($order->isPaid())
                <p class="text-sm text-slate-500 mt-4">
                    E-ticket akan aktif setelah pembayaran Anda diverifikasi oleh panitia event ini.
                </p>
            @endunless
        </div>
    @empty
        <div class="glass-panel rounded-2xl p-12 text-center">
            <p class="text-slate-300 mb-2 text-lg font-medium">Belum ada pesanan</p>
            <p class="text-slate-500 text-sm mb-6">Anda belum memesan tiket event apa pun dengan akun ini.</p>
            <a href="{{ route('home') }}#upcoming-events"
                class="inline-block bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold px-6 py-3 rounded-lg transition-all">
                Jelajahi Event
            </a>
        </div>
    @endforelse

    <div class="mt-10 text-center">
        <a href="{{ route('profile.edit') }}"
            class="text-sm text-slate-400 hover:text-white transition-colors">Kelola profil &amp; password</a>
    </div>
</div>
@endsection
