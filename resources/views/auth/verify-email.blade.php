@extends('layouts.app')

@section('title', 'Verifikasi Email — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-14 sm:py-20">
    <div class="card p-8 sm:p-10">
        <h1 class="text-3xl font-bold text-ink-900 mb-2">Verifikasi Email</h1>
        <p class="text-ink-500 text-sm mb-6">
            Terima kasih sudah mendaftar. Sebelum mulai, silakan buka email Anda dan klik tautan verifikasi yang baru
            saja kami kirim. Kalau emailnya belum masuk, kami bisa mengirim ulang.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 rounded-btn bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800">
                Tautan verifikasi baru sudah dikirim ke alamat email Anda.
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="btn btn-primary px-6 py-3">
                    Kirim Ulang Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-ink-500 hover:text-ink-900 transition-colors cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
