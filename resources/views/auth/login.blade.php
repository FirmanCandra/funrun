@extends('layouts.app')

@section('title', 'Login — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-14 sm:py-20">
    <div class="card p-8 sm:p-10">
        <h1 class="text-3xl font-bold text-ink-900 mb-2">Masuk</h1>
        <p class="text-ink-500 text-sm mb-8">Masuk untuk melihat tiket dan pendaftaran event Anda.</p>

        @if (session('status'))
            <div class="mb-6 rounded-btn bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-btn bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username"
                    class="field"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="field"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded border-line text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-ink-500">Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-brand-600 hover:text-brand-700 transition-colors">Lupa password?</a>
                @endif
            </div>

            <button type="submit"
                class="btn btn-primary w-full py-3.5">
                Masuk
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-ink-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-medium transition-colors">Daftar
                sekarang</a>
        </p>
    </div>
</div>
@endsection
