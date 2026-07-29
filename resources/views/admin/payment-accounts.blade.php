@extends('admin.layouts.admin')

@section('header_title', 'Rekening Pembayaran')

@php $isSuper = auth()->user()->isSuperAdmin(); @endphp

@section('content')

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Konteks event --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h3 class="font-bold text-lg text-slate-800">{{ $event->title }}</h3>
        <p class="text-xs text-slate-500 mt-1">
            Rekening tujuan transfer untuk event ini. Peserta memilih salah satunya saat memesan tiket.
        </p>
    </div>

    @if($isSuper)
        <form method="GET" action="{{ route('admin.payment-accounts') }}" class="flex items-center gap-2">
            <label class="text-sm text-slate-600 whitespace-nowrap">Event:</label>
            <select name="event_id" onchange="this.form.submit()"
                class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ $ev->id === $event->id ? 'selected' : '' }}>{{ $ev->title }}</option>
                @endforeach
            </select>
        </form>
    @else
        <span class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg font-medium whitespace-nowrap">
            Event yang Anda tangani
        </span>
    @endif
</div>

{{-- Peringatan kalau belum ada rekening aktif --}}
@if($accounts->where('is_active', true)->isEmpty())
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6 flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z"></path>
            </svg>
        </div>
        <div>
            <p class="font-bold text-amber-900">Belum ada rekening aktif — tiket event ini belum bisa dibeli</p>
            <p class="text-sm text-amber-700 mt-1">
                Halaman pembelian akan menolak pesanan selama tidak ada rekening tujuan.
                @if($isSuper)
                    Tambahkan rekening lewat formulir di bawah.
                @else
                    Hubungi Super Admin untuk menambahkannya — hanya Super Admin yang boleh mengatur rekening.
                @endif
            </p>
        </div>
    </div>
@endif

{{-- Catatan hak akses untuk admin event --}}
@unless($isSuper)
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-6 text-sm text-slate-600">
        <span class="font-semibold text-slate-800">Hanya bisa dilihat.</span>
        Nomor rekening hanya boleh diubah Super Admin, supaya aliran dana event tidak bisa dialihkan.
        Anda tetap melihatnya di sini untuk mencocokkan bukti transfer yang masuk.
    </div>
@endunless

{{-- Daftar rekening --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Daftar Rekening</h3>
        <p class="text-xs text-slate-500 mt-1">
            Rekening yang dinonaktifkan tidak lagi muncul di halaman pembelian, tapi pesanan lama yang memakainya tetap utuh.
        </p>
    </div>

    @if($accounts->isEmpty())
        <div class="p-6 text-sm text-slate-500">Belum ada rekening untuk event ini.</div>
    @else
        <div class="divide-y divide-slate-100">
            @foreach($accounts as $account)
                <div class="p-6 flex flex-col lg:flex-row lg:items-end gap-4">
                    @if($isSuper)
                        <form method="POST" action="{{ route('admin.payment-accounts.update', $account->id) }}"
                            class="flex-1 flex flex-col lg:flex-row lg:items-end gap-4">
                            @csrf
                            @method('PUT')

                            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Bank / E-Wallet</label>
                                    <input type="text" name="bank_name" value="{{ $account->bank_name }}" required
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ $account->numberLabel() }}</label>
                                    <input type="text" name="account_number" value="{{ $account->account_number }}" required
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Atas Nama</label>
                                    <input type="text" name="account_holder" value="{{ $account->account_holder }}" required
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Urutan</label>
                                    <input type="number" name="sort_order" value="{{ $account->sort_order }}" min="0"
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-4 sm:gap-5 whitespace-nowrap">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600">Aktif</span>
                                </label>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Simpan
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.payment-accounts.destroy', $account->id) }}"
                            onsubmit="return confirm('Hapus rekening {{ $account->bank_name }} {{ $account->account_number }}? Pesanan yang sudah memakainya tetap menyimpan nomor lamanya.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                                Hapus
                            </button>
                        </form>
                    @else
                        {{-- Tampilan baca saja untuk admin event --}}
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Bank / E-Wallet</p>
                                <p class="font-semibold text-slate-800">{{ $account->bank_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">{{ $account->numberLabel() }}</p>
                                <p class="font-mono font-semibold text-slate-800">{{ $account->account_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Atas Nama</p>
                                <p class="font-semibold text-slate-800">{{ $account->account_holder }}</p>
                            </div>
                        </div>
                        <span class="text-xs px-3 py-1.5 rounded-lg font-medium whitespace-nowrap
                            {{ $account->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Tambah rekening — super admin saja --}}
@if($isSuper)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-1">Tambah Rekening</h3>
        <p class="text-xs text-slate-500 mb-4">Rekening baru langsung aktif dan muncul di halaman pembelian event ini.</p>

        <form method="POST" action="{{ route('admin.payment-accounts.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Bank / E-Wallet *</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" required
                        placeholder="contoh: Transfer Bank BCA"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Nomor Rekening / HP *</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" required
                        placeholder="contoh: 1234567890"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Atas Nama *</label>
                    <input type="text" name="account_holder" value="{{ old('account_holder') }}" required
                        placeholder="contoh: Panitia VOLT RHYTHM"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Tambah Rekening
                </button>
            </div>
        </form>
    </div>
@endif
@endsection
