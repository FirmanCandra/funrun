@extends('layouts.app')

@section('title', 'Lupa Password — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-12 sm:py-20">
    <div class="card p-7 sm:p-10 bg-white dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800/80 rounded-3xl shadow-xl dark:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.7)] backdrop-blur-xl transition-colors duration-200">
        
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">Lupa Password</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">
                Masukkan email Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi akun Anda.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 px-4 py-3 text-xs sm:text-sm text-emerald-800 dark:text-emerald-300 flex items-start gap-3">
                <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="field bg-slate-50/70 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl focus:bg-white focus:dark:bg-slate-950 focus:border-blue-600 focus:dark:border-blue-500 focus:ring-4 focus:ring-blue-600/10 focus:dark:ring-blue-500/20"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/25 transition-all duration-200 active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Kirim Tautan Reset</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <p class="mt-8 text-center text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-bold transition-colors inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke halaman masuk</span>
            </a>
        </p>
    </div>
</div>
@endsection
