@extends('admin.layouts.admin')

@section('header_title', 'Formulir Pendaftaran')

@php use App\Models\EventFormField; @endphp

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
            Atur data apa saja yang harus diisi peserta saat membeli tiket event ini.
            Perubahan langsung berlaku di halaman pembelian.
        </p>
    </div>

    @if(auth()->user()->isSuperAdmin())
        <form method="GET" action="{{ route('admin.form-fields') }}" class="flex items-center gap-2">
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

{{-- Field terkunci --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
    <h3 class="font-bold text-slate-800 mb-1">Selalu ditanyakan</h3>
    <p class="text-xs text-slate-500 mb-4">
        Empat data ini tidak bisa dimatikan karena dipakai sistem: NIK mencegah satu orang dapat dua tiket,
        WhatsApp untuk mengirim e-ticket, kategori menentukan harga dan kode BIB.
    </p>
    <div class="flex flex-wrap gap-2">
        @foreach(['Nama Lengkap', 'NIK', 'Nomor WhatsApp', 'Kategori Lomba'] as $locked)
            <span class="bg-slate-100 text-slate-600 text-xs px-3 py-1.5 rounded-lg font-medium">
                {{ $locked }} · wajib
            </span>
        @endforeach
    </div>
</div>

{{-- Field bawaan yang bisa diatur --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Data bawaan</h3>
        <p class="text-xs text-slate-500 mt-1">
            Matikan yang tidak dibutuhkan event ini, atau ubah dari wajib jadi opsional.
            Tipe isiannya tidak bisa diubah karena kolomnya sudah tetap di database.
        </p>
    </div>

    <div class="divide-y divide-slate-100">
        @foreach($coreFields as $field)
            <form method="POST" action="{{ route('admin.form-fields.update', $field->id) }}"
                class="p-6 flex flex-col lg:flex-row lg:items-end gap-4">
                @csrf
                @method('PUT')

                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Label</label>
                        <input type="text" name="label" value="{{ $field->label }}" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400 mt-1">
                            <span class="font-mono">{{ $field->key }}</span> · {{ $field->type }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan bantu (opsional)</label>
                        <input type="text" name="help_text" value="{{ $field->help_text }}"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Urutan</label>
                        <input type="number" name="sort_order" value="{{ $field->sort_order }}" min="0"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex items-center gap-5 whitespace-nowrap">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="enabled" value="1" {{ $field->enabled ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-600">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="required" value="1" {{ $field->required ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-600">Wajib</span>
                    </label>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        @endforeach
    </div>
</div>

{{-- Field tambahan --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Pertanyaan tambahan</h3>
        <p class="text-xs text-slate-500 mt-1">
            Pertanyaan khusus event ini. Jawabannya tersimpan pada data peserta dan ikut terbawa saat export CSV.
        </p>
    </div>

    @if($customFields->isEmpty())
        <div class="p-6 text-sm text-slate-500">
            Belum ada pertanyaan tambahan. Formulir memakai data bawaan saja.
        </div>
    @else
        <div class="divide-y divide-slate-100">
            @foreach($customFields as $field)
                <div class="p-6 flex flex-col lg:flex-row lg:items-end gap-4">
                    <form method="POST" action="{{ route('admin.form-fields.update', $field->id) }}"
                        class="flex-1 flex flex-col lg:flex-row lg:items-end gap-4">
                        @csrf
                        @method('PUT')

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Pertanyaan</label>
                                <input type="text" name="label" value="{{ $field->label }}" required
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-[11px] text-slate-400 mt-1 font-mono">{{ $field->key }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Tipe</label>
                                <select name="type"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach(EventFormField::CUSTOM_TYPES as $value => $label)
                                        <option value="{{ $value }}" {{ $field->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan bantu</label>
                                <input type="text" name="help_text" value="{{ $field->help_text }}"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Urutan</label>
                                <input type="number" name="sort_order" value="{{ $field->sort_order }}" min="0"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex items-center gap-5 whitespace-nowrap">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="enabled" value="1" {{ $field->enabled ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-600">Aktif</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="required" value="1" {{ $field->required ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-600">Wajib</span>
                            </label>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.form-fields.destroy', $field->id) }}"
                        onsubmit="return confirm('Hapus pertanyaan &quot;{{ $field->label }}&quot;? Jawaban peserta yang sudah masuk tetap tersimpan, tapi tidak lagi ditanyakan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                            Hapus
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Tambah field baru --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <h3 class="font-bold text-slate-800 mb-4">Tambah pertanyaan</h3>

    <form method="POST" action="{{ route('admin.form-fields.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Pertanyaan *</label>
                <input type="text" name="label" value="{{ old('label') }}" required
                    placeholder="contoh: Nama Komunitas Lari"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tipe isian *</label>
                <select name="type" required
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(EventFormField::CUSTOM_TYPES as $value => $label)
                        <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Contoh isian (opsional)</label>
                <input type="text" name="placeholder" value="{{ old('placeholder') }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan bantu (opsional)</label>
                <input type="text" name="help_text" value="{{ old('help_text') }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 flex-wrap pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="required" value="1" {{ old('required') ? 'checked' : '' }}
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-slate-600">Wajib diisi peserta</span>
            </label>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Tambah ke Formulir
            </button>
        </div>
    </form>
</div>
@endsection
