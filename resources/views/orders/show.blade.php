@extends('layouts.app')

@section('title', 'Pesanan ' . $order->order_code . ' — SeTiket')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <a href="{{ route('dashboard') }}"
        class="inline-flex items-center gap-2 text-sm text-ink-500 hover:text-ink-900 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke pesanan saya
    </a>

    <div class="mb-9">
        <h1 class="font-mono text-2xl sm:text-3xl font-bold text-ink-900 mb-2">{{ $order->order_code }}</h1>
        <p class="text-ink-500">
            {{ $order->event->title ?? 'Event' }} &middot; dipesan {{ $order->created_at->translatedFormat('d M Y, H:i') }}
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-btn bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800 mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-btn bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-800 mb-6">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ringkasan pembayaran --}}
    <div class="card p-7 sm:p-8 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-6">
            <div>
                <p class="text-sm text-ink-500 mb-1">Status Pembayaran</p>
                <p class="font-display text-2xl font-bold text-ink-900">{{ $order->statusLabel() }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-sm text-ink-500 mb-1">Total</p>
                <p class="font-display text-2xl font-bold text-accent-600">
                    Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <dl class="grid sm:grid-cols-2 gap-6 mt-7 pt-7 border-t border-line text-sm">
            <div>
                <dt class="text-ink-500 mb-1.5">Ditransfer ke</dt>
                <dd class="font-semibold text-ink-900">{{ $order->payment_method ?? '—' }}</dd>
                @if ($order->payment_account_number)
                    <dd class="font-mono text-ink-900 mt-0.5">{{ $order->payment_account_number }}</dd>
                    <dd class="text-ink-500 text-xs mt-0.5">a.n. {{ $order->payment_account_holder }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-ink-500 mb-1.5">Jumlah Tiket</dt>
                <dd class="font-semibold text-ink-900">{{ $order->tickets->count() }} tiket</dd>
            </div>
        </dl>

        @if ($order->isRejected() && $order->rejection_reason)
            <div class="rounded-btn bg-red-50 border border-red-200 px-4 py-3.5 text-sm text-red-800 mt-6">
                <span class="font-semibold">Alasan penolakan:</span> {{ $order->rejection_reason }}
            </div>
        @endif
    </div>

    {{-- Peserta --}}
    <div class="card p-7 sm:p-8 mb-6">
        <h2 class="text-xl font-bold text-ink-900 mb-6">Peserta</h2>

        <div class="space-y-4">
            @foreach ($order->tickets as $ticket)
                @php
                    $participant = $ticket->participant;
                    $category = $categories->get($participant?->category);
                @endphp
                <div class="rounded-btn border border-line p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="font-semibold text-ink-900">{{ $participant?->displayName() ?? '—' }}</p>
                            <p class="text-xs text-ink-500 font-mono mt-0.5">{{ $ticket->ticket_code }}</p>
                        </div>
                        <span class="badge {{ $ticket->isIssued() ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-ink-500' }}">
                            {{ $ticket->statusLabel() }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <dt class="text-ink-500 text-xs mb-1">Kategori</dt>
                            <dd class="text-ink-900 font-medium">{{ $category->name ?? $participant?->category }}</dd>
                        </div>
                        <div>
                            <dt class="text-ink-500 text-xs mb-1">Jersey</dt>
                            <dd class="text-ink-900 font-medium">{{ $participant->jersey_size ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-ink-500 text-xs mb-1">Harga</dt>
                            <dd class="text-ink-900 font-medium">
                                Rp {{ number_format((float) ($category->price ?? 0), 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-end">
                            @if ($ticket->isIssued())
                                <a href="{{ route('ticket.show', $ticket->ticket_code) }}"
                                    class="text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">
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
    <div class="card p-7 sm:p-8">
        <h2 class="text-xl font-bold text-ink-900 mb-1.5">Bukti Pembayaran</h2>
        <p class="text-sm text-ink-500 mb-6">Satu bukti transfer berlaku untuk seluruh tiket dalam pesanan ini.</p>

        @if ($order->proof_of_payment)
            @php $proofUrl = $order->proofUrl(); @endphp
            <div class="mb-6 space-y-3">
                <div class="relative inline-block max-w-full">
                    <img src="{{ $proofUrl }}" alt="Bukti pembayaran"
                        class="max-h-80 w-auto rounded-card border border-line shadow-xs object-contain bg-slate-50"
                        loading="lazy"
                        onerror="this.onerror=null; this.classList.add('hidden'); document.getElementById('proof-fallback-msg').classList.remove('hidden');">
                    <div id="proof-fallback-msg" class="hidden p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Gambar bukti transfer sedang disiapkan server. Klik tautan di bawah untuk melihat berkas atau unggah ulang bukti jika diperlukan.</span>
                    </div>
                </div>
                <div>
                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Buka Bukti Transfer di Tab Baru</span>
                    </a>
                </div>
            </div>
        @else
            <p class="text-sm text-ink-500 mb-6">Belum ada bukti yang diunggah.</p>
        @endif

        @unless ($order->isPaid())
            <form method="POST" action="{{ route('orders.proof', $order) }}" enctype="multipart/form-data"
                class="space-y-4 pt-6 border-t border-line">
                @csrf
                <label class="label">
                    {{ $order->isRejected() ? 'Unggah bukti pembayaran yang benar' : 'Ganti bukti pembayaran' }}
                </label>
                <input type="file" name="proof" accept="image/*" required
                    class="block w-full text-sm text-ink-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-btn file:border-0 file:text-sm file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer">
                @error('proof')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                <button type="submit" class="btn btn-primary px-6 py-3">Unggah Ulang</button>
            </form>
        @endunless
    </div>
</div>
@endsection
