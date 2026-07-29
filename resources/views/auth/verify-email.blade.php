@extends('layouts.app')

@section('title', 'Verifikasi Email — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="glass-panel rounded-2xl p-8">
        <h1 class="text-3xl font-bold gradient-text mb-2">Verifikasi Email</h1>
        <p class="text-slate-400 text-sm mb-6">
            Terima kasih sudah mendaftar. Sebelum mulai, silakan buka email Anda dan klik tautan verifikasi yang baru
            saja kami kirim. Kalau emailnya belum masuk, kami bisa mengirim ulang.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">
                Tautan verifikasi baru sudah dikirim ke alamat email Anda.
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold px-6 py-3 rounded-lg transition-all cursor-pointer">
                    Kirim Ulang Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-slate-400 hover:text-white transition-colors cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
