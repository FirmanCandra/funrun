@extends('layouts.app')

@section('title', 'Pesanan Saya — SeTiket')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-ink-900 dark:text-white mb-2">Pesanan Saya</h1>
            <p class="text-ink-500 dark:text-slate-400">Halo, {{ auth()->user()->name }}. Ini riwayat pemesanan tiket Anda.</p>
        </div>
        <a href="{{ route('tickets') }}" class="btn btn-primary px-5 py-2.5 text-sm self-start sm:self-auto">
            Lihat Tiket Saya
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-btn bg-green-50 dark:bg-emerald-950/50 border border-green-200 dark:border-emerald-800/60 px-5 py-4 text-sm text-green-800 dark:text-emerald-200 mb-8">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($orders as $order)
        @php
            $statusClass = match ($order->payment_status) {
                \App\Models\Order::STATUS_PAID => 'bg-green-50 dark:bg-emerald-950/60 text-green-700 dark:text-emerald-300 border border-green-200/60 dark:border-emerald-800/60',
                \App\Models\Order::STATUS_REJECTED => 'bg-red-50 dark:bg-rose-950/60 text-red-700 dark:text-rose-300 border border-red-200/60 dark:border-rose-800/60',
                default => 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60',
            };
            $dot = match ($order->payment_status) {
                \App\Models\Order::STATUS_PAID => 'bg-green-600 dark:bg-green-400',
                \App\Models\Order::STATUS_REJECTED => 'bg-red-600 dark:bg-red-400',
                default => 'bg-amber-500 dark:bg-amber-400',
            };
        @endphp

        <div class="card overflow-hidden mb-6 animate-fade-up">
            <div class="p-7 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                    <div class="min-w-0">
                        <h2 class="font-display font-bold text-xl text-ink-900 dark:text-white mb-1.5">
                            {{ $order->event->title ?? 'Event' }}
                        </h2>
                        <p class="text-sm text-ink-500 dark:text-slate-400 flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="font-mono text-gray-800 dark:text-gray-200">{{ $order->order_code }}</span>
                            <span class="text-line dark:text-slate-600">•</span>
                            <span>{{ $order->tickets->count() }} tiket</span>
                            <span class="text-line dark:text-slate-600">•</span>
                            <span>{{ $order->created_at->translatedFormat('d M Y') }}</span>
                        </p>
                    </div>
                    <span class="badge {{ $statusClass }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                        {{ $order->statusLabel() }}
                    </span>
                </div>

                @if ($order->isRejected() && $order->rejection_reason)
                    <div class="rounded-btn bg-red-50 dark:bg-rose-950/50 border border-red-200 dark:border-rose-900/60 px-4 py-3 text-sm text-red-800 dark:text-rose-200 mb-6">
                        <span class="font-semibold">Ditolak panitia:</span> {{ $order->rejection_reason }}
                    </div>
                @endif

                {{-- Daftar peserta --}}
                <div class="rounded-btn border border-line dark:border-slate-800 divide-y divide-[color:var(--color-line)] dark:divide-slate-800 bg-gray-50/50 dark:bg-slate-900/40 mb-6">
                    @foreach ($order->tickets as $ticket)
                        @php
                            $participant = $ticket->participant;
                            $category = $categories->get($order->event_id . '|' . $participant?->category);
                        @endphp
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5">
                            <div class="min-w-0">
                                <p class="font-medium text-ink-900 dark:text-white text-sm truncate">{{ $participant?->displayName() ?? '—' }}</p>
                                <p class="text-xs text-ink-500 dark:text-slate-400">
                                    {{ $category->name ?? $participant?->category }} · Jersey {{ $participant->jersey_size ?? '—' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-mono text-xs text-ink-500 dark:text-slate-400">{{ $ticket->ticket_code }}</span>
                                @if ($ticket->isIssued())
                                    <a href="{{ route('ticket.show', $ticket->ticket_code) }}"
                                        class="text-xs font-semibold text-brand-600 dark:text-blue-400 hover:text-brand-700 dark:hover:text-blue-300 transition-colors whitespace-nowrap">
                                        E-Ticket
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-ink-500 dark:text-slate-400">Total</p>
                        <p class="font-display font-bold text-xl text-ink-900 dark:text-white">
                            Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline px-5 py-2.5 text-sm">
                            Detail Pesanan
                        </a>
                        @if ($order->isPaid())
                            <a href="{{ route('tickets') }}" class="btn btn-primary px-5 py-2.5 text-sm">Lihat Tiket</a>
                        @endif
                    </div>
                </div>

                @unless ($order->isPaid())
                    <p class="text-sm text-ink-500 dark:text-slate-400 mt-5 pt-5 border-t border-line dark:border-slate-800">
                        E-ticket akan aktif setelah pembayaran Anda diverifikasi oleh panitia event ini.
                    </p>
                @endunless
            </div>
        </div>
    @empty
        <div class="card p-14 text-center">
            <span class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-slate-800 text-ink-500 dark:text-slate-400 border border-transparent dark:border-slate-700/60 flex items-center justify-center mx-auto mb-5 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.6a1 1 0 0 1 .7.3l5.4 5.4a1 1 0 0 1 .3.7V19a2 2 0 0 1-2 2Z" />
                </svg>
            </span>
            <h2 class="font-display font-semibold text-xl text-ink-900 dark:text-white mb-2">Belum ada pesanan</h2>
            <p class="text-ink-500 dark:text-slate-400 text-sm mb-7 max-w-sm mx-auto">
                Anda belum memesan tiket event apa pun dengan akun ini.
            </p>
            <a href="{{ route('home') }}#events" class="btn btn-primary px-6 py-3">Jelajahi Event</a>
        </div>
    @endforelse

    <div class="mt-10 text-center">
        <a href="{{ route('profile.edit') }}" class="text-sm text-ink-500 hover:text-ink-900 dark:text-slate-400 dark:hover:text-white transition-colors">
            Kelola profil &amp; password
        </a>
    </div>
</div>
@endsection
