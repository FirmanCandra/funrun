@extends('layouts.app')

@section('title', 'Lupa Password — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="glass-panel rounded-2xl p-8">
        <h1 class="text-3xl font-bold gradient-text mb-2">Lupa Password</h1>
        <p class="text-slate-400 text-sm mb-8">
            Masukkan email Anda. Kami akan mengirim tautan untuk membuat password baru.
        </p>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-slate-800/60 text-slate-100 placeholder-slate-500 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold py-3 rounded-lg transition-all cursor-pointer">
                Kirim Tautan Reset
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-400">
            <a href="{{ route('login') }}" class="text-sky-400 hover:text-sky-300 font-medium transition-colors">Kembali
                ke halaman masuk</a>
        </p>
    </div>
</div>
@endsection
