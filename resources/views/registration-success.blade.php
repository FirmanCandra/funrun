@extends('layouts.app')

@section('title', 'Registrasi Berhasil — SeTiket')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Success Card --}}
        <div class="glass-panel rounded-3xl p-8 md:p-10 shadow-2xl text-center relative overflow-hidden">
            {{-- Gradient top accent --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-400 via-orange-500 to-amber-500"></div>

            {{-- Checkmark Icon --}}
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/30 animate-bounce-once">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            {{-- Heading --}}
            <h1 class="text-3xl font-extrabold text-white tracking-tight mb-3">Registrasi Berhasil!</h1>
            <p class="text-gray-400 text-sm leading-relaxed mb-8">
                Terima kasih telah mendaftar. Data Anda telah kami terima dan akan segera diproses.
            </p>

            {{-- Info Cards --}}
            <div class="space-y-4 mb-8">
                {{-- Waktu Verifikasi --}}
                <div class="bg-slate-800/60 border border-white/10 rounded-2xl p-4 flex items-start gap-4 text-left">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm">Waktu Verifikasi</h3>
                        <p class="text-gray-400 text-xs mt-0.5 leading-relaxed">Proses verifikasi memakan waktu maksimal 2 hari kerja</p>
                    </div>
                </div>

                {{-- Konfirmasi via WhatsApp --}}
                <div class="bg-slate-800/60 border border-white/10 rounded-2xl p-4 flex items-start gap-4 text-left">
                    <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm">Konfirmasi via WhatsApp</h3>
                        <p class="text-gray-400 text-xs mt-0.5 leading-relaxed">Anda akan dihubungi oleh admin melalui WhatsApp untuk konfirmasi pembayaran dan pengiriman E-Ticket.</p>
                    </div>
                </div>
            </div>

            {{-- CTA Button --}}
            <a href="{{ route('home') }}"
               class="block w-full py-4 rounded-2xl font-bold text-lg text-center transition-all transform hover:-translate-y-1 hover:shadow-2xl"
               style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #f8fafc; border: 1px solid rgba(255,255,255,0.1);">
                OK, Saya Mengerti
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
