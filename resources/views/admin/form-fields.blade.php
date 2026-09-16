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

    <div class="flex items-center gap-4">
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
        
        <button type="submit" form="bulk-update-form" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            Simpan Semua Perubahan
        </button>
    </div>
</div>

{{-- Catatan: seluruh field boleh dimatikan, tapi empat di antaranya punya efek samping --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
    <h3 class="font-bold text-slate-800 mb-1">Semua isian ada di tangan Anda</h3>
    <p class="text-xs text-slate-500 mb-4">
        Tidak ada field yang dipaksakan. Matikan yang tidak dibutuhkan event ini, atau ubah dari wajib jadi
        opsional. Empat field berikut tetap boleh dimatikan, hanya perlu Anda tahu efeknya:
    </p>
    <ul class="space-y-2">
        @foreach(EventFormField::CORE_FIELD_NOTES as $key => $catatan)
            <li class="flex items-start gap-2.5 text-xs text-slate-600">
                <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded font-semibold whitespace-nowrap mt-px">
                    {{ EventFormField::CORE_FIELDS[$key]['label'] }}
                </span>
                <span class="leading-relaxed">{{ $catatan }}</span>
            </li>
        @endforeach
    </ul>
</div>

<form id="bulk-update-form" method="POST" action="{{ route('admin.form-fields.bulk-update') }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="event_id" value="{{ $event->id }}">

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
            <div class="p-6 flex flex-col lg:flex-row lg:items-end gap-4">

                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Label</label>
                        <input type="text" name="fields[{{ $field->id }}][label]" value="{{ $field->label }}" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400 mt-1">
                            <span class="font-mono">{{ $field->key }}</span> · {{ $field->type }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan bantu (opsional)</label>
                        <input type="text" name="fields[{{ $field->id }}][help_text]" value="{{ $field->help_text }}"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Urutan</label>
                        <input type="number" name="fields[{{ $field->id }}][sort_order]" value="{{ $field->sort_order }}" min="0"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 sm:gap-5 whitespace-nowrap lg:pb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="fields[{{ $field->id }}][enabled]" value="1" {{ $field->enabled ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-600">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="fields[{{ $field->id }}][required]" value="1" {{ $field->required ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-600">Wajib</span>
                    </label>
                </div>
            </div>
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
                    <div class="flex-1 flex flex-col lg:flex-row lg:items-end gap-4 w-full">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Pertanyaan</label>
                                <input type="text" name="fields[{{ $field->id }}][label]" value="{{ $field->label }}" required
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-[11px] text-slate-400 mt-1 font-mono">{{ $field->key }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Tipe</label>
                                <select name="fields[{{ $field->id }}][type]"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach(EventFormField::CUSTOM_TYPES as $value => $label)
                                        <option value="{{ $value }}" {{ $field->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan bantu</label>
                                <input type="text" name="fields[{{ $field->id }}][help_text]" value="{{ $field->help_text }}"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Urutan</label>
                                <input type="number" name="fields[{{ $field->id }}][sort_order]" value="{{ $field->sort_order }}" min="0"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 sm:gap-5 whitespace-nowrap lg:pb-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="fields[{{ $field->id }}][enabled]" value="1" {{ $field->enabled ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-600">Aktif</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="fields[{{ $field->id }}][required]" value="1" {{ $field->required ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-600">Wajib</span>
                            </label>
                            <button type="submit" form="delete-form-{{ $field->id }}" onclick="return confirm('Hapus pertanyaan ini? Data peserta yang sudah tersimpan tidak akan hilang.')"
                                class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus field">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</form>

{{-- Form Hapus (Terpisah dari Bulk Form) --}}
@foreach($customFields as $field)
    <form id="delete-form-{{ $field->id }}" method="POST" action="{{ route('admin.form-fields.destroy', $field->id) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

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
