@extends('layouts.app')

@section('title', 'Login — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-12 sm:py-20">
    <div class="card p-7 sm:p-10 bg-white dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800/80 rounded-3xl shadow-xl dark:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.7)] backdrop-blur-xl transition-colors duration-200">
        
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">Masuk</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">
                Masuk untuk melihat e-tiket dan riwayat pendaftaran event Anda.
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

        @if (session('error'))
            <div class="mb-6 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 px-4 py-3 text-xs sm:text-sm text-rose-800 dark:text-rose-300 flex items-start gap-3">
                <svg class="w-5 h-5 shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Tombol Login Google --}}
        <a href="{{ route('auth.google') }}"
            class="w-full flex items-center justify-center gap-3 px-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/90 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-100 font-semibold text-sm shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 active:scale-[0.99] group">
            <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Masuk dengan Google</span>
        </a>

        {{-- Divider Pemisah --}}
        <div class="relative my-6 flex items-center justify-center">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
            </div>
            <div class="relative bg-white dark:bg-slate-900 px-3 text-[11px] uppercase tracking-wider text-slate-400 dark:text-slate-500 font-bold transition-colors">
                atau dengan email
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username"
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
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="field bg-slate-50/70 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl focus:bg-white focus:dark:bg-slate-950 focus:border-blue-600 focus:dark:border-blue-500 focus:ring-4 focus:ring-blue-600/10 focus:dark:ring-blue-500/20"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 transition-colors">
                    <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors">
                        Lupa password?
                    </a>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/25 transition-all duration-200 active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Masuk</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <p class="mt-8 text-center text-xs text-slate-500 dark:text-slate-400">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-bold transition-colors ml-1">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>
@endsection
