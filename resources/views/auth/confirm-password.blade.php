@extends('layouts.app')

@section('title', 'Konfirmasi Password — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="glass-panel rounded-2xl p-8">
        <h1 class="text-3xl font-bold gradient-text mb-2">Konfirmasi Password</h1>
        <p class="text-slate-400 text-sm mb-8">
            Ini area yang dilindungi. Masukkan password Anda sekali lagi untuk melanjutkan.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold py-3 rounded-lg transition-all cursor-pointer">
                Konfirmasi
            </button>
        </form>
    </div>
</div>
@endsection
