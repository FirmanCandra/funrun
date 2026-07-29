@extends('layouts.app')

@section('title', 'Daftar Akun — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-14 sm:py-20">
    <div class="card p-8 sm:p-10">
        <h1 class="text-3xl font-bold text-ink-900 mb-2">Daftar Akun</h1>
        <p class="text-ink-500 text-sm mb-8">Buat akun untuk menyimpan riwayat pendaftaran dan e-ticket Anda.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="label">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name"
                    class="field"
                    placeholder="Nama sesuai identitas">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="field"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="field"
                    placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label">Konfirmasi
                    Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="field"
                    placeholder="Ulangi password">
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primary w-full py-3.5">
                Daftar
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-ink-500">
            Sudah punya akun?
            <a href="{{ route('login') }}"
                class="text-brand-600 hover:text-brand-700 font-medium transition-colors">Masuk</a>
        </p>
    </div>
</div>
@endsection
