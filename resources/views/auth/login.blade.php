@extends('layouts.app')

@section('title', 'Login — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="glass-panel rounded-2xl p-8">
        <h1 class="text-3xl font-bold gradient-text mb-2">Masuk</h1>
        <p class="text-slate-400 text-sm mb-8">Masuk untuk melihat tiket dan pendaftaran event Anda.</p>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username"
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded border-white/20 bg-slate-800/60 text-sky-500 focus:ring-sky-500/50">
                    <span class="text-sm text-slate-400">Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-sky-400 hover:text-sky-300 transition-colors">Lupa password?</a>
                @endif
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold py-3 rounded-lg transition-all cursor-pointer">
                Masuk
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-400">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-sky-400 hover:text-sky-300 font-medium transition-colors">Daftar
                sekarang</a>
        </p>
    </div>
</div>
@endsection
