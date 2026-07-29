@extends('layouts.app')

@section('title', 'Reset Password — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-14 sm:py-20">
    <div class="card p-8 sm:p-10">
        <h1 class="text-3xl font-bold text-ink-900 mb-2">Password Baru</h1>
        <p class="text-ink-500 text-sm mb-8">Buat password baru untuk akun Anda.</p>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                    autofocus autocomplete="username"
                    class="field">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="label">Password Baru</label>
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
                    placeholder="Ulangi password baru">
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primary w-full py-3.5">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>
@endsection
