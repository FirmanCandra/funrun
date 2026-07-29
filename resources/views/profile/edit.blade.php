@extends('layouts.app')

@section('title', 'Profil Saya — SeTiket')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10">
        <h1 class="text-3xl sm:text-4xl font-bold gradient-text mb-2">Profil Saya</h1>
        <p class="text-slate-400">
            Masuk sebagai
            <span class="text-slate-200 font-medium">{{ $user->email }}</span>
            <span class="ml-2 text-xs px-2 py-0.5 rounded-full border border-white/10 bg-slate-800/60">
                {{ str_replace('_', ' ', $user->role) }}
            </span>
        </p>
    </div>

    {{-- Informasi Profil --}}
    <div class="glass-panel rounded-2xl p-6 sm:p-8 mb-6">
        <h2 class="text-xl font-bold text-white mb-1">Informasi Akun</h2>
        <p class="text-sm text-slate-400 mb-6">Perbarui nama dan alamat email akun Anda.</p>

        @if (session('status') === 'profile-updated')
            <div class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">
                Profil berhasil diperbarui.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    autocomplete="name"
                    class="w-full bg-slate-800/60 text-slate-100 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all">
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                    autocomplete="username"
                    class="w-full bg-slate-800/60 text-slate-100 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all">
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold px-6 py-3 rounded-lg transition-all cursor-pointer">
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Ubah Password --}}
    <div class="glass-panel rounded-2xl p-6 sm:p-8 mb-6">
        <h2 class="text-xl font-bold text-white mb-1">Ubah Password</h2>
        <p class="text-sm text-slate-400 mb-6">Gunakan password yang panjang dan tidak dipakai di tempat lain.</p>

        @if (session('status') === 'password-updated')
            <div class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">
                Password berhasil diubah.
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-300 mb-2">Password
                    Saat Ini</label>
                <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                    class="w-full bg-slate-800/60 text-slate-100 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all">
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password" class="block text-sm font-medium text-slate-300 mb-2">Password
                    Baru</label>
                <input id="update_password" type="password" name="password" autocomplete="new-password"
                    class="w-full bg-slate-800/60 text-slate-100 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all">
                @error('password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_confirmation"
                    class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password Baru</label>
                <input id="update_password_confirmation" type="password" name="password_confirmation"
                    autocomplete="new-password"
                    class="w-full bg-slate-800/60 text-slate-100 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-transparent transition-all">
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-400 hover:to-indigo-400 text-white font-semibold px-6 py-3 rounded-lg transition-all cursor-pointer">
                Simpan Password
            </button>
        </form>
    </div>

    {{-- Hapus Akun — hanya untuk role user --}}
    @if ($user->isUser())
        <div class="glass-panel rounded-2xl p-6 sm:p-8 border-red-500/20">
            <h2 class="text-xl font-bold text-white mb-1">Hapus Akun</h2>
            <p class="text-sm text-slate-400 mb-6">
                Akun dan seluruh datanya akan dihapus permanen. Pendaftaran event yang sudah dibayar ikut terhapus dan
                tidak bisa dikembalikan.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-5"
                onsubmit="return confirm('Hapus akun Anda secara permanen? Tindakan ini tidak bisa dibatalkan.');">
                @csrf
                @method('delete')

                <div>
                    <label for="delete_password" class="block text-sm font-medium text-slate-300 mb-2">
                        Konfirmasi dengan password Anda
                    </label>
                    <input id="delete_password" type="password" name="password" autocomplete="current-password"
                        class="w-full max-w-sm bg-slate-800/60 text-slate-100 rounded-lg px-4 py-3 border border-white/10 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all">
                    @error('password', 'userDeletion')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 text-red-300 font-semibold px-6 py-3 rounded-lg transition-all cursor-pointer">
                    Hapus Akun Saya
                </button>
            </form>
        </div>
    @endif

    <div class="mt-10 text-center">
        <a href="{{ auth()->user()->homeRoute() }}"
            class="text-sm text-slate-400 hover:text-white transition-colors">&larr; Kembali</a>
    </div>
</div>
@endsection
