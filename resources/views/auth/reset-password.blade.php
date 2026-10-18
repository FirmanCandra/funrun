@extends('layouts.app')

@section('title', 'Reset Password — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-12 sm:py-20">
    <div class="card p-7 sm:p-10 bg-white dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800/80 rounded-3xl shadow-xl dark:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.7)] backdrop-blur-xl transition-colors duration-200">
        
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">Password Baru</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">
                Buat password baru yang aman untuk akun Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                    autofocus autocomplete="username"
                    class="field bg-slate-50/70 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl focus:bg-white focus:dark:bg-slate-950 focus:border-blue-600 focus:dark:border-blue-500 focus:ring-4 focus:ring-blue-600/10 focus:dark:ring-blue-500/20">
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="label text-slate-700 dark:text-slate-300 text-xs font-semibold uppercase tracking-wider mb-1.5 block">
                    Password Baru
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
                    placeholder="Ulangi password baru">
                @error('password_confirmation')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/25 transition-all duration-200 active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Simpan Password Baru</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
