@extends('layouts.app')

@section('title', 'Daftar Akun — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="glass-panel rounded-2xl p-8">
        <h1 class="text-3xl font-bold gradient-text mb-2">Daftar Akun</h1>
        <p class="text-slate-400 text-sm mb-8">Buat akun untuk menyimpan riwayat pendaftaran dan e-ticket Anda.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name"
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="Nama sesuai identitas">
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi
                    Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="Ulangi password">
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold py-3 rounded-lg transition-all cursor-pointer">
                Daftar
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-400">
            Sudah punya akun?
            <a href="{{ route('login') }}"
                class="text-sky-400 hover:text-sky-300 font-medium transition-colors">Masuk</a>
        </p>
    </div>
</div>
@endsection
