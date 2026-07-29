@extends('layouts.app')

@section('title', 'Registrasi Berhasil — SeTiket')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Success Card --}}
        <div class="card p-8 md:p-10 text-center relative overflow-hidden">
            {{-- Gradient top accent --}}
            <div class="absolute top-0 left-0 w-full h-1.5 bg-green-600"></div>

            {{-- Checkmark Icon --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-green-50 border border-green-200 flex items-center justify-center animate-bounce-once">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            {{-- Heading --}}
            <h1 class="text-2xl sm:text-3xl font-bold text-ink-900 tracking-tight mb-3">Pesanan Berhasil Dibuat!</h1>
            <p class="text-ink-500 text-sm leading-relaxed mb-8">
                @if($order)
                    {{ $order->tickets->count() }} tiket untuk {{ $order->event->title ?? 'event ini' }} telah kami terima
                    dan akan segera diverifikasi panitia.
                @else
                    Terima kasih telah mendaftar. Data Anda telah kami terima dan akan segera diproses.
                @endif
            </p>

            {{-- Ringkasan pesanan --}}
            @if($order)
                <div class="rounded-card border border-line bg-gray-50 p-5 mb-8 text-left">
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-line">
                        <div>
                            <div class="text-xs text-ink-500 uppercase tracking-widest">Kode Pesanan</div>
                            <div class="font-mono font-bold text-ink-900">{{ $order->order_code }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-ink-500 uppercase tracking-widest">Total</div>
                            <div class="font-bold text-accent-600">
                                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @foreach($order->tickets as $ticket)
                            <div class="flex items-center justify-between text-sm gap-3">
                                <span class="text-ink-900 truncate">{{ $ticket->participant?->displayName() ?? '—' }}</span>
                                <span class="font-mono text-xs text-ink-500 whitespace-nowrap">{{ $ticket->ticket_code }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Info Cards --}}
            <div class="space-y-4 mb-8">
                {{-- Waktu Verifikasi --}}
                <div class="rounded-card border border-line bg-white p-4 flex items-start gap-4 text-left">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-ink-900 text-sm">Waktu Verifikasi</h3>
                        <p class="text-ink-500 text-xs mt-0.5 leading-relaxed">Proses verifikasi memakan waktu maksimal 2 hari kerja</p>
                    </div>
                </div>

                {{-- Konfirmasi via WhatsApp --}}
                <div class="rounded-card border border-line bg-white p-4 flex items-start gap-4 text-left">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-ink-900 text-sm">Konfirmasi via WhatsApp</h3>
                        <p class="text-ink-500 text-xs mt-0.5 leading-relaxed">Anda akan dihubungi oleh admin melalui WhatsApp untuk konfirmasi pembayaran dan pengiriman E-Ticket.</p>
                    </div>
                </div>
            </div>

            {{-- CTA Button --}}
            <a href="{{ route('dashboard') }}"
               class="btn btn-primary w-full py-3.5 text-base"
               >
                Lihat Status Pesanan Saya
            </a>
            <a href="{{ route('home') }}"
               class="btn btn-ghost w-full mt-3 py-3">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-once {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.1); }
        70% { transform: scale(0.95); }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-bounce-once {
        animation: bounce-once 0.6s ease-out;
    }
</style>
@endsection
