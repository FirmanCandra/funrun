@extends('layouts.app')

@section('title', 'Pesanan ' . $order->order_code . ' — SeTiket')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8">
        <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
            &larr; Kembali ke pesanan saya
        </a>
        <h1 class="text-3xl font-bold gradient-text mt-3 mb-2">{{ $order->order_code }}</h1>
        <p class="text-slate-400">
            {{ $order->event->title ?? 'Event' }} &middot; dipesan {{ $order->created_at->format('d M Y, H:i') }}
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Status --}}
    <div class="glass-panel rounded-2xl p-6 sm:p-8 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500 mb-1">Status Pembayaran</p>
                <p class="text-2xl font-bold text-white">{{ $order->statusLabel() }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-white">
                    Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <dl class="grid grid-cols-2 gap-4 text-sm mt-6 pt-6 border-t border-white/5">
            <div>
                <dt class="text-slate-500 mb-1">Ditransfer ke</dt>
                <dd class="text-slate-200">{{ $order->payment_method ?? '—' }}</dd>
                @if ($order->payment_account_number)
                    <dd class="text-slate-400 font-mono text-xs mt-0.5">{{ $order->payment_account_number }}</dd>
                    <dd class="text-slate-500 text-xs">a.n. {{ $order->payment_account_holder }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-slate-500 mb-1">Jumlah Tiket</dt>
                <dd class="text-slate-200">{{ $order->tickets->count() }} tiket</dd>
            </div>
        </dl>

        @if ($order->isRejected() && $order->rejection_reason)
            <div class="mt-6 rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-300">
                <span class="font-semibold">Alasan penolakan:</span> {{ $order->rejection_reason }}
            </div>
        @endif
    </div>

    {{-- Peserta --}}
    <div class="glass-panel rounded-2xl p-6 sm:p-8 mb-6">
        <h2 class="text-xl font-bold text-white mb-6">Peserta</h2>

        <div class="space-y-4">
            @foreach ($order->tickets as $ticket)
                @php
                    $participant = $ticket->participant;
                    $category = $categories->get($participant?->category);
                @endphp
                <div class="border border-white/5 rounded-xl p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="font-semibold text-white">{{ $participant->fullname ?? '—' }}</p>
                            <p class="text-xs text-slate-500 font-mono">{{ $ticket->ticket_code }}</p>
                        </div>
                        <span class="text-xs font-medium px-3 py-1 rounded-full border whitespace-nowrap
                            {{ $ticket->isIssued() ? 'bg-sky-500/10 text-sky-300 border-sky-500/30' : 'bg-slate-500/10 text-slate-300 border-slate-500/30' }}">
                            {{ $ticket->statusLabel() }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div>
                            <dt class="text-slate-500 text-xs mb-0.5">Kategori</dt>
                            <dd class="text-slate-300">{{ $category->name ?? $participant?->category }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 text-xs mb-0.5">Jersey</dt>
                            <dd class="text-slate-300">{{ $participant->jersey_size ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 text-xs mb-0.5">Harga</dt>
                            <dd class="text-slate-300">
                                Rp {{ number_format((float) ($category->price ?? 0), 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-end">
                            @if ($ticket->isIssued())
                                <a href="{{ route('ticket.show', $ticket->ticket_code) }}"
                                    class="text-sm font-semibold text-sky-400 hover:text-sky-300 transition-colors">
                                    E-Ticket &rarr;
                                </a>
                            @endif
                        </div>
                    </dl>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Bukti pembayaran --}}
    <div class="glass-panel rounded-2xl p-6 sm:p-8">
        <h2 class="text-xl font-bold text-white mb-1">Bukti Pembayaran</h2>
        <p class="text-sm text-slate-400 mb-6">Satu bukti transfer berlaku untuk seluruh tiket dalam pesanan ini.</p>

        @if ($order->proof_of_payment)
            <img src="{{ asset('storage/' . $order->proof_of_payment) }}" alt="Bukti pembayaran"
                class="max-h-80 rounded-xl border border-white/10 mb-6">
        @else
            <p class="text-sm text-slate-500 mb-6">Belum ada bukti yang diunggah.</p>
        @endif

        @unless ($order->isPaid())
            <form method="POST" action="{{ route('orders.proof', $order) }}" enctype="multipart/form-data"
                class="space-y-4 pt-6 border-t border-white/5">
                @csrf
                <label class="block text-sm font-medium text-slate-300">
                    {{ $order->isRejected() ? 'Unggah bukti pembayaran yang benar' : 'Ganti bukti pembayaran' }}
                </label>
                <input type="file" name="proof" accept="image/*" required
                    class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-600 file:text-white hover:file:bg-sky-500 cursor-pointer">
                @error('proof')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
                <button type="submit"
                    class="bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold px-6 py-3 rounded-lg transition-all cursor-pointer">
                    Unggah Ulang
                </button>
            </form>
        @endunless
    </div>
</div>
@endsection
