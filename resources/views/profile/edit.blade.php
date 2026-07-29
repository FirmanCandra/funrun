@extends('layouts.app')

@section('title', 'Profil Saya — SeTiket')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10">
        <h1 class="text-3xl font-bold text-ink-900 mb-2">Profil Saya</h1>
        <p class="text-ink-500">
            Masuk sebagai
            <span class="text-ink-900 font-semibold">{{ $user->email }}</span>
            <span class="badge bg-brand-50 text-brand-700 ml-2">
                {{ str_replace('_', ' ', $user->role) }}
            </span>
        </p>
    </div>

    {{-- Informasi Profil --}}
    <div class="card p-7 sm:p-8 mb-6">
        <h2 class="text-xl font-bold text-ink-900 mb-1">Informasi Akun</h2>
        <p class="text-sm text-ink-500 mb-6">Perbarui nama dan alamat email akun Anda.</p>

        @if (session('status') === 'profile-updated')
            <div class="mb-6 rounded-btn bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800">
                Profil berhasil diperbarui.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="label">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    autocomplete="name"
                    class="field">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                    autocomplete="username"
                    class="field">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primary px-6 py-3">
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Ubah Password --}}
    <div class="card p-7 sm:p-8 mb-6">
        <h2 class="text-xl font-bold text-ink-900 mb-1">Ubah Password</h2>
        <p class="text-sm text-ink-500 mb-6">Gunakan password yang panjang dan tidak dipakai di tempat lain.</p>

        @if (session('status') === 'password-updated')
            <div class="mb-6 rounded-btn bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800">
                Password berhasil diubah.
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="label">Password
                    Saat Ini</label>
                <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                    class="field">
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password" class="label">Password
                    Baru</label>
                <input id="update_password" type="password" name="password" autocomplete="new-password"
                    class="field">
                @error('password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_confirmation"
                    class="label">Konfirmasi Password Baru</label>
                <input id="update_password_confirmation" type="password" name="password_confirmation"
                    autocomplete="new-password"
                    class="field">
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primary px-6 py-3">
                Simpan Password
            </button>
        </form>
    </div>

    {{-- Hapus Akun — hanya untuk role user --}}
    @if ($user->isUser())
        <div class="card p-7 sm:p-8 border-red-500/20">
            <h2 class="text-xl font-bold text-ink-900 mb-1">Hapus Akun</h2>
            <p class="text-sm text-ink-500 mb-6">
                Akun dan seluruh datanya akan dihapus permanen. Pendaftaran event yang sudah dibayar ikut terhapus dan
                tidak bisa dikembalikan.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-5"
                onsubmit="return confirm('Hapus akun Anda secara permanen? Tindakan ini tidak bisa dibatalkan.');">
                @csrf
                @method('delete')

                <div>
                    <label for="delete_password" class="label">
                        Konfirmasi dengan password Anda
                    </label>
                    <input id="delete_password" type="password" name="password" autocomplete="current-password"
                        class="field max-w-sm">
                    @error('password', 'userDeletion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="btn bg-red-600 hover:bg-red-700 text-white px-6 py-3">
                    Hapus Akun Saya
                </button>
            </form>
        </div>
    @endif

    <div class="mt-10 text-center">
        <a href="{{ auth()->user()->homeRoute() }}"
            class="text-sm text-ink-500 hover:text-ink-900 transition-colors">&larr; Kembali</a>
    </div>
</div>
@endsection
