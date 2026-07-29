@extends('layouts.app')

@section('title', 'Konfirmasi Password — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-14 sm:py-20">
    <div class="card p-8 sm:p-10">
        <h1 class="text-3xl font-bold text-ink-900 mb-2">Konfirmasi Password</h1>
        <p class="text-ink-500 text-sm mb-8">
            Ini area yang dilindungi. Masukkan password Anda sekali lagi untuk melanjutkan.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                    class="field"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primary w-full py-3.5">
                Konfirmasi
            </button>
        </form>
    </div>
</div>
@endsection
