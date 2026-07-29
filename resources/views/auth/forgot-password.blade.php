@extends('layouts.app')

@section('title', 'Lupa Password — SeTiket')

@section('content')
<div class="max-w-md mx-auto px-4 py-14 sm:py-20">
    <div class="card p-8 sm:p-10">
        <h1 class="text-3xl font-bold text-ink-900 mb-2">Lupa Password</h1>
        <p class="text-ink-500 text-sm mb-8">
            Masukkan email Anda. Kami akan mengirim tautan untuk membuat password baru.
        </p>

        @if (session('status'))
            <div class="mb-6 rounded-btn bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="field"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primary w-full py-3.5">
                Kirim Tautan Reset
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-ink-500">
            <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-medium transition-colors">Kembali
                ke halaman masuk</a>
        </p>
    </div>
</div>
@endsection
