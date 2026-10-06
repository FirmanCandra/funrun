@extends('layouts.app')

@section('title', 'Profil Saya — SeTiket')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Profil Saya</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">
            Masuk sebagai
            <span class="text-gray-900 dark:text-white font-semibold">{{ $user->email }}</span>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 ml-2">
                {{ str_replace('_', ' ', $user->role) }}
            </span>
        </p>
    </div>

    @if ($user->isUser())
        {{-- Section Pintasan Tiket & Transaksi Pembelian (UX Premium) --}}
        <div class="mb-10">
            <h2 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
                Tiket & Transaksi Pembelian
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                {{-- Card 1: Tiket Aktif --}}
                <a href="{{ route('tickets') }}" class="group block p-5 rounded-2xl bg-gradient-to-br from-blue-600 to-[#0050ff] text-white shadow-md hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5Z"/></svg>
                        </div>
                        <span class="text-3xl font-black">{{ $ticketsCount }}</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="font-bold text-base sm:text-lg">Tiket Aktif Saya</h3>
                        <p class="text-xs text-blue-100 mt-0.5">Buka e-Ticket & scan QR Code check-in event</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between text-xs font-semibold">
                        <span>Lihat Semua Tiket Terbit</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </a>

                {{-- Card 2: Riwayat Pesanan & Pembelian --}}
                <a href="{{ route('dashboard') }}" class="group block p-5 rounded-2xl bg-white dark:bg-[#131d31] border border-gray-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.5 9.4-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $ordersCount }}</span>
                            @if($pendingOrdersCount > 0)
                                <span class="block text-[11px] font-bold text-amber-600 dark:text-amber-400">({{ $pendingOrdersCount }} proses verifikasi)</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="font-bold text-base sm:text-lg text-gray-900 dark:text-white">Pesanan & Transaksi</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Status pembayaran transfer & bukti pembayaran</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-[#0050ff] dark:text-blue-400">
                        <span>Kelola Riwayat Pesanan</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </a>

            </div>
        </div>
    @endif

    {{-- Informasi Profil --}}
    <div class="p-7 sm:p-8 mb-6 bg-white dark:bg-[#131d31] rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Informasi Akun</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Perbarui nama dan alamat email akun Anda.</p>

        @if (session('status') === 'profile-updated')
            <div class="mb-6 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 px-5 py-4 text-sm text-emerald-800 dark:text-emerald-300">
                Profil berhasil diperbarui.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    autocomplete="name"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:border-[#0050ff] focus:outline-none transition-all text-sm">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                    autocomplete="username"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:border-[#0050ff] focus:outline-none transition-all text-sm">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-[#0050ff] hover:bg-[#0043d4] text-white font-bold text-sm shadow-md transition-all">
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Ubah Password --}}
    <div class="p-7 sm:p-8 mb-6 bg-white dark:bg-[#131d31] rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Ubah Password</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Gunakan password yang panjang dan tidak dipakai di tempat lain.</p>

        @if (session('status') === 'password-updated')
            <div class="mb-6 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 px-5 py-4 text-sm text-emerald-800 dark:text-emerald-300">
                Password berhasil diubah.
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Password Saat Ini</label>
                <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:border-[#0050ff] focus:outline-none transition-all text-sm">
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Password Baru</label>
                <input id="update_password" type="password" name="password" autocomplete="new-password"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:border-[#0050ff] focus:outline-none transition-all text-sm">
                @error('password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_confirmation"
                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                <input id="update_password_confirmation" type="password" name="password_confirmation"
                    autocomplete="new-password"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:border-[#0050ff] focus:outline-none transition-all text-sm">
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-[#0050ff] hover:bg-[#0043d4] text-white font-bold text-sm shadow-md transition-all">
                Simpan Password
            </button>
        </form>
    </div>

    {{-- Hapus Akun — hanya untuk role user --}}
    @if ($user->isUser())
        <div class="p-7 sm:p-8 bg-red-50/40 dark:bg-red-950/20 rounded-2xl border border-red-200 dark:border-red-900/50 shadow-sm">
            <h2 class="text-xl font-bold text-red-700 dark:text-red-400 mb-1">Hapus Akun</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Akun dan seluruh datanya akan dihapus permanen. Pendaftaran event yang sudah dibayar ikut terhapus dan
                tidak bisa dikembalikan.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-5"
                onsubmit="return confirm('Hapus akun Anda secara permanen? Tindakan ini tidak bisa dibatalkan.');">
                @csrf
                @method('delete')

                <div>
                    <label for="delete_password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                        Konfirmasi dengan password Anda
                    </label>
                    <input id="delete_password" type="password" name="password" autocomplete="current-password"
                        class="w-full max-w-sm px-4 py-3 rounded-xl border border-red-200 dark:border-red-800 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:outline-none transition-all text-sm">
                    @error('password', 'userDeletion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-md transition-all">
                    Hapus Akun Saya
                </button>
            </form>
        </div>
    @endif

    <div class="mt-10 text-center">
        <a href="{{ auth()->user()->homeRoute() }}"
            class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">&larr; Kembali ke Beranda</a>
    </div>
</div>
</div>
@endsection
