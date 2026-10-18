@extends('layouts.app')

@section('title', 'Daftar Akun — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-12 sm:py-20">
    <div class="card p-7 sm:p-10 bg-white dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800/80 rounded-3xl shadow-xl dark:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.7)] backdrop-blur-xl transition-colors duration-200">
        
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">Daftar Akun</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">
                Buat akun untuk menyimpan riwayat pendaftaran dan e-ticket Anda.
            </p>
        </div>

        {{-- Tombol Daftar dengan Google --}}
        <a href="{{ route('auth.google') }}"
            class="w-full flex items-center justify-center gap-3 px-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/90 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-100 font-semibold text-sm shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 active:scale-[0.99] group">
            <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Daftar dengan Google</span>
        </a>

        {{-- Divider Pemisah --}}
        <div class="relative my-6 flex items-center justify-center">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
            </div>
            <div class="relative bg-white dark:bg-slate-900 px-3 text-[11px] uppercase tracking-wider text-slate-400 dark:text-slate-500 font-bold transition-colors">
                atau daftar dengan email
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Nama Lengkap
                </label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name"
                    class="field bg-slate-50/70 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl focus:bg-white focus:dark:bg-slate-950 focus:border-blue-600 focus:dark:border-blue-500 focus:ring-4 focus:ring-blue-600/10 focus:dark:ring-blue-500/20"
                    placeholder="Nama sesuai identitas">
                @error('name')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="field bg-slate-50/70 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl focus:bg-white focus:dark:bg-slate-950 focus:border-blue-600 focus:dark:border-blue-500 focus:ring-4 focus:ring-blue-600/10 focus:dark:ring-blue-500/20"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Password
                </label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="field bg-slate-50/70 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl focus:bg-white focus:dark:bg-slate-950 focus:border-blue-600 focus:dark:border-blue-500 focus:ring-4 focus:ring-blue-600/10 focus:dark:ring-blue-500/20"
                    placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Konfirmasi Password
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="field bg-slate-50/70 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl focus:bg-white focus:dark:bg-slate-950 focus:border-blue-600 focus:dark:border-blue-500 focus:ring-4 focus:ring-blue-600/10 focus:dark:ring-blue-500/20"
                    placeholder="Ulangi password">
                @error('password_confirmation')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/25 transition-all duration-200 active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Daftar Akun</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <p class="mt-8 text-center text-xs text-slate-500 dark:text-slate-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-bold transition-colors ml-1">
                Masuk sekarang
            </a>
        </p>
    </div>
</div>
@endsection
