@extends('admin.layouts.admin')

@section('header_title', 'Manajemen Event')

@section('content')

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="text-sm font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 rounded-2xl shadow-xs">
            <div class="flex items-center gap-2 font-bold text-sm mb-1 text-red-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Terjadi kesalahan validasi:
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-red-700">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Top Action Bar & Stats --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Katalog Seluruh Event</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Total terdaftar <span class="font-bold text-slate-800">{{ $events->total() }}</span> event. Kelola poster, kategori, rekening pembayaran, dan formulir pendaftaran dari satu tempat.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Bulk Delete Button --}}
            <button id="btnBulkDelete" onclick="submitBulkDelete()"
                class="hidden bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-xs flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>

            {{-- View Mode Toggle --}}
            <div class="bg-slate-100 p-1 rounded-xl flex items-center border border-slate-200">
                <button type="button" id="btnViewGrid" onclick="switchView('grid')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-blue-600 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Grid Card
                </button>
                <button type="button" id="btnViewTable" onclick="switchView('table')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 text-slate-600 hover:text-slate-900">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Tabel
                </button>
            </div>

            {{-- Tambah Event Button --}}
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all text-xs flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14" />
                </svg>
                Tambah Event Baru
            </button>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="mb-6 bg-white p-4 rounded-2xl border border-slate-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('admin.events') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full max-w-lg">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari event berdasarkan nama, lokasi, tanggal..."
                    class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-slate-50/50">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.events') }}"
                    class="text-slate-500 hover:text-slate-700 text-xs font-semibold px-2">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-400">
            Menampilkan halaman <span class="font-bold text-slate-700">{{ $events->currentPage() }}</span> dari <span class="font-bold text-slate-700">{{ $events->lastPage() }}</span>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 1. CARD GRID VIEW (Default - Tampilkan thumbnail jelas) --}}
    {{-- ======================================================== --}}
    <div id="containerGridView" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        @forelse($events as $i => $ev)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xs hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col group">
                {{-- Thumbnail 16:9 Container --}}
                <div class="relative w-full aspect-[16/9] bg-slate-100 overflow-hidden border-b border-slate-100">
                    @if(!empty($ev['thumbnail']))
                        <img src="{{ $ev['thumbnail'] }}" alt="{{ $ev['nama'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gradient-to-tr from-blue-700 via-indigo-600 to-purple-600 flex flex-col items-center justify-center text-white p-4">
                            <span class="text-3xl font-black tracking-wider opacity-80">{{ strtoupper(substr($ev['nama'], 0, 2)) }}</span>
                            <span class="text-[11px] font-semibold text-blue-100/80 mt-1">Belum ada thumbnail</span>
                        </div>
                    @endif

                    {{-- Badges on top of thumbnail --}}
                    <div class="absolute top-3 left-3 flex items-center gap-1.5 z-10">
                        @if(($ev['kategori'] ?? '') === 'upcoming')
                            <span class="bg-emerald-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-xs">
                                Upcoming
                            </span>
                        @else
                            <span class="bg-amber-500 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-xs">
                                Highlight
                            </span>
                        @endif
                    </div>

                    <div class="absolute top-3 right-3 z-10">
                        <span class="bg-slate-900/80 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs">
                            {{ ($ev['harga'] ?? 0) == 0 ? 'Gratis' : 'Rp ' . number_format($ev['harga'], 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Checkbox selection overlay at bottom-left --}}
                    <div class="absolute bottom-3 left-3 z-10">
                        <label class="cursor-pointer bg-white/90 backdrop-blur-md rounded-lg px-2 py-1 flex items-center gap-1.5 shadow-xs border border-white/50">
                            <input type="checkbox" name="ids[]" value="{{ $ev['id'] }}"
                                class="event-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5">
                            <span class="text-[10px] font-bold text-slate-700">#{{ $ev['id'] }}</span>
                        </label>
                    </div>
                </div>

                {{-- Card Content --}}
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base line-clamp-1 group-hover:text-blue-600 transition-colors" title="{{ $ev['nama'] }}">
                            {{ $ev['nama'] }}
                        </h3>

                        {{-- Metadata: Tanggal & Lokasi --}}
                        <div class="mt-3 space-y-1.5 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="font-medium text-slate-700 truncate">{{ $ev['tanggal'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="font-medium text-slate-600 truncate">{{ $ev['lokasi'] }}</span>
                            </div>
                        </div>

                        {{-- Feature Pills Status --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap gap-1.5 text-[11px]">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Poster
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                Tiket
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Rekening
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Form
                            </span>
                        </div>
                    </div>

                    {{-- Actions Hub Trigger --}}
                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center gap-2">
                        {{-- Tombol Utama: KELOLA EVENT (Jadikan satu disitu!) --}}
                        <button type="button" 
                            onclick="openKelolaHub({{ $ev['id'] }}, @js($ev['nama']), @js($ev['thumbnail'] ?? ''), @js($ev['tanggal']), @js($ev['lokasi']), {{ $ev['harga'] }}, @js($ev['kategori']), @js($ev['urlBeli'] ?? ''), @js($ev['waktu'] ?? ''), @js($ev['deskripsi'] ?? ''), @js($ev['syarat_ketentuan'] ?? ''))"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all shadow-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Kelola Event
                        </button>

                        {{-- Quick Edit --}}
                        <button type="button"
                            onclick="openEdit({{ $ev['id'] }}, @js($ev['nama']), @js($ev['lokasi']), @js($ev['tanggal']), {{ $ev['harga'] }}, @js($ev['kategori']), @js($ev['urlBeli'] ?? ''), @js($ev['thumbnail'] ?? ''), @js($ev['waktu'] ?? ''), @js($ev['deskripsi'] ?? ''), @js($ev['syarat_ketentuan'] ?? ''))"
                            class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                            title="Edit Data Event">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>

                        {{-- Quick Delete --}}
                        <form action="{{ route('admin.events.destroy', $ev['id']) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus event \'{{ addslashes($ev['nama']) }}\'? Seluruh data terkait akan dihapus.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="p-2.5 rounded-xl border border-red-100 text-red-600 hover:bg-red-50 transition-colors"
                                title="Hapus Event">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-800 text-lg mb-1">Belum Ada Event</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">Mulai tambahkan event baru agar tiket dapat dibeli peserta di SeTiket.</p>
                <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-xs">
                    + Tambah Event Pertama
                </button>
            </div>
        @endforelse
    </div>

    {{-- ======================================================== --}}
    {{-- 2. TABLE VIEW (Alternatif jika admin butuh scan data)    --}}
    {{-- ======================================================== --}}
    <div id="containerTableView" class="hidden bg-white rounded-3xl shadow-xs border border-slate-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[56rem] text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-4 text-left w-10">
                            <input type="checkbox" id="selectAll"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-5 py-4 text-left font-bold w-12">No</th>
                        <th class="px-5 py-4 text-left font-bold">Thumbnail & Nama Event</th>
                        <th class="px-5 py-4 text-left font-bold">Lokasi</th>
                        <th class="px-5 py-4 text-left font-bold">Tanggal</th>
                        <th class="px-5 py-4 text-left font-bold">Harga</th>
                        <th class="px-5 py-4 text-left font-bold">Kategori</th>
                        <th class="px-5 py-4 text-right font-bold pr-6">Pengelolaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $i => $ev)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 text-slate-400">
                                <input type="checkbox" name="ids[]" value="{{ $ev['id'] }}"
                                    class="event-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs font-semibold">
                                {{ ($events->currentPage() - 1) * $events->perPage() + ($i + 1) }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-10 rounded-lg overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                        @if(!empty($ev['thumbnail']))
                                            <img src="{{ $ev['thumbnail'] }}" alt="{{ $ev['nama'] }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-[10px]">
                                                {{ strtoupper(substr($ev['nama'], 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-[240px]">
                                        <p class="font-bold text-slate-800 text-sm truncate">{{ $ev['nama'] }}</p>
                                        <p class="text-[11px] text-slate-400">ID #{{ $ev['id'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-600 text-xs max-w-[180px] truncate">{{ $ev['lokasi'] }}</td>
                            <td class="px-5 py-4 text-slate-600 text-xs whitespace-nowrap">{{ $ev['tanggal'] }}</td>
                            <td class="px-5 py-4 font-bold text-slate-800 text-xs whitespace-nowrap">
                                {{ ($ev['harga'] ?? 0) == 0 ? 'Gratis' : 'Rp ' . number_format($ev['harga'], 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                @if(($ev['kategori'] ?? '') === 'upcoming')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700">Upcoming</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700">Highlight</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right pr-6">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                        onclick="openKelolaHub({{ $ev['id'] }}, @js($ev['nama']), @js($ev['thumbnail'] ?? ''), @js($ev['tanggal']), @js($ev['lokasi']), {{ $ev['harga'] }}, @js($ev['kategori']), @js($ev['urlBeli'] ?? ''), @js($ev['waktu'] ?? ''), @js($ev['deskripsi'] ?? ''), @js($ev['syarat_ketentuan'] ?? ''))"
                                        class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                        Kelola
                                    </button>
                                    <button type="button"
                                        onclick="openEdit({{ $ev['id'] }}, @js($ev['nama']), @js($ev['lokasi']), @js($ev['tanggal']), {{ $ev['harga'] }}, @js($ev['kategori']), @js($ev['urlBeli'] ?? ''), @js($ev['thumbnail'] ?? ''), @js($ev['waktu'] ?? ''), @js($ev['deskripsi'] ?? ''), @js($ev['syarat_ketentuan'] ?? ''))"
                                        class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                                        title="Edit Info">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.events.destroy', $ev['id']) }}" method="POST"
                                        onsubmit="return confirm('Hapus event \'{{ addslashes($ev['nama']) }}\'?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 rounded-lg border border-red-100 text-red-600 hover:bg-red-50 transition-colors"
                                            title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-slate-400">
                                Belum ada event.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($events->hasPages())
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif


    {{-- ======================================================== --}}
    {{-- 3. MODAL: HUB KELOLA EVENT (Jadikan satu seluruh fitur!) --}}
    {{-- ======================================================== --}}
    <div id="modalKelolaHub" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto border border-slate-100">
            {{-- Header with event banner / thumbnail --}}
            <div class="relative overflow-hidden bg-slate-900 rounded-t-3xl aspect-[21/9] sm:aspect-[24/9] border-b border-slate-100">
                <img id="hub_thumbnail_img" src="" alt="Poster Event" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                
                {{-- Close Button --}}
                <button onclick="document.getElementById('modalKelolaHub').classList.add('hidden')"
                    class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 backdrop-blur-md flex items-center justify-center text-white transition-colors z-20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Event Title & Info on Banner --}}
                <div class="absolute bottom-4 left-5 right-5 z-10 text-white">
                    <div class="inline-flex items-center gap-2 bg-blue-600/90 backdrop-blur-md px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1">
                        Pusat Pengelolaan Event
                    </div>
                    <h3 id="hub_title" class="text-xl sm:text-2xl font-black text-white leading-tight truncate">Nama Event</h3>
                    <p id="hub_meta" class="text-xs text-slate-300 mt-1 flex items-center gap-2 truncate">
                        Tanggal & Lokasi Event
                    </p>
                </div>
            </div>

            {{-- Hub Actions Grid --}}
            <div class="p-5 sm:p-7">
                <div class="mb-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengaturan Terpadu</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih menu konfigurasi event yang ingin Anda atur:</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- 1. Gambar & Poster Event --}}
                    <a id="hub_link_image" href="#"
                        class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-blue-500 hover:shadow-md transition-all group flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h5 class="font-extrabold text-slate-800 text-sm group-hover:text-blue-600 transition-colors flex items-center justify-between">
                                Gambar & Poster
                                <span class="text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                            </h5>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">Unggah poster 16:9 yang tampil di halaman beranda, katalog, dan tiket.</p>
                        </div>
                    </a>

                    {{-- 2. Kategori Tiket & Harga --}}
                    <a id="hub_link_categories" href="#"
                        class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-purple-500 hover:shadow-md transition-all group flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h5 class="font-extrabold text-slate-800 text-sm group-hover:text-purple-600 transition-colors flex items-center justify-between">
                                Kategori Tiket
                                <span class="text-purple-600 opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                            </h5>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">Atur jenis tiket (3K, 5K, 10K, VIP), kuota tiket, dan harga per kategori.</p>
                        </div>
                    </a>

                    {{-- 3. Rekening Pembayaran --}}
                    <a id="hub_link_payment" href="#"
                        class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-500 hover:shadow-md transition-all group flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h5 class="font-extrabold text-slate-800 text-sm group-hover:text-emerald-600 transition-colors flex items-center justify-between">
                                Rekening Pembayaran
                                <span class="text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                            </h5>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">Atur bank transfer (BCA, Mandiri, BRI, QRIS) tujuan pembayaran peserta.</p>
                        </div>
                    </a>

                    {{-- 4. Formulir Pendaftaran --}}
                    <a id="hub_link_forms" href="#"
                        class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-amber-500 hover:shadow-md transition-all group flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h5 class="font-extrabold text-slate-800 text-sm group-hover:text-amber-600 transition-colors flex items-center justify-between">
                                Formulir Pendaftaran
                                <span class="text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                            </h5>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">Atur pertanyaan formulir, data identitas, ukuran jersey, kontak darurat.</p>
                        </div>
                    </a>
                </div>

                {{-- 5. Tombol Edit Detail Lengkap --}}
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" id="btnHubTriggerEdit"
                        class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-3 px-4 rounded-2xl text-xs transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Ubah Informasi Dasar Event (Nama, Lokasi, Waktu, Deskripsi)
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ======================================================== --}}
    {{-- 4. MODAL: ADD EVENT                                      --}}
    {{-- ======================================================== --}}
    <div id="modalAdd" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-slate-100">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Tambah Event Baru</h3>
                    <p class="text-xs text-slate-500">Isi data awal event. Setelah dibuat, Anda bisa langsung mengatur rekening & tiket.</p>
                </div>
                <button onclick="document.getElementById('modalAdd').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data"
                class="px-6 py-6 space-y-4">
                @csrf
                @include('admin.partials.event-form')
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-2xl font-bold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-2xl font-bold text-xs transition-colors shadow-xs">
                        Simpan Event
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ======================================================== --}}
    {{-- 5. MODAL: EDIT EVENT                                     --}}
    {{-- ======================================================== --}}
    <div id="modalEdit" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-slate-100">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Edit Data Event</h3>
                    <p class="text-xs text-slate-500">Perbarui detail nama, lokasi, tanggal, dan poster event</p>
                </div>
                <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Event *</label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Thumbnail / Poster Event</label>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <div id="edit_thumbnail_preview" class="mt-2 text-xs text-slate-500 hidden flex items-center gap-2">
                        <span>Poster terpasang:</span>
                        <a href="#" id="edit_thumbnail_link" target="_blank"
                            class="text-blue-600 font-bold hover:underline">Lihat Gambar ↗</a>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lokasi *</label>
                    <input type="text" name="lokasi" id="edit_lokasi" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Event *</label>
                    <input type="text" name="tanggal" id="edit_tanggal" placeholder="contoh: 25 Juli 2026" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Harga Tiket Dasar (Rp) *</label>
                    <input type="number" name="harga" id="edit_harga" min="0" placeholder="0 untuk gratis" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Tampilan *</label>
                    <select name="kategori" id="edit_kategori" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="upcoming">Upcoming Events</option>
                        <option value="highlight">Highlight Events</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">URL Beli Tiket Alternatif</label>
                    <input type="text" name="urlBeli" id="edit_urlBeli"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Waktu Event</label>
                    <input type="text" name="waktu" id="edit_waktu"
                        placeholder="contoh: 16.00 - 23.00 (kosongkan untuk default)"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Event</label>
                    <textarea name="deskripsi" id="edit_deskripsi"
                        placeholder="Deskripsi lengkap event" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Syarat & Ketentuan</label>
                    <textarea name="syarat_ketentuan" id="edit_syarat_ketentuan"
                        placeholder="Syarat & ketentuan event" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"></textarea>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-2xl font-bold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-2xl font-bold text-xs transition-colors shadow-xs">
                        Perbarui Event
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- Javascript Logics --}}
    <script>
        // Switch between Grid and Table View
        function switchView(mode) {
            const grid = document.getElementById('containerGridView');
            const table = document.getElementById('containerTableView');
            const btnGrid = document.getElementById('btnViewGrid');
            const btnTable = document.getElementById('btnViewTable');

            if (mode === 'grid') {
                grid.classList.remove('hidden');
                table.classList.add('hidden');
                btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-blue-600 shadow-xs';
                btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
            } else {
                grid.classList.add('hidden');
                table.classList.remove('hidden');
                btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-blue-600 shadow-xs';
                btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
            }
        }

        // Open Kelola Event Hub Modal
        function openKelolaHub(id, nama, thumbnail, tanggal, lokasi, harga, kategori, urlBeli, waktu, deskripsi, syarat_ketentuan) {
            document.getElementById('hub_title').textContent = nama;
            document.getElementById('hub_meta').textContent = tanggal + ' • ' + lokasi;

            const img = document.getElementById('hub_thumbnail_img');
            if (thumbnail) {
                img.src = thumbnail;
                img.classList.remove('hidden');
            } else {
                img.src = '/images/setiketbg.webp';
            }

            // Set dynamic URLs for event sub-pages
            document.getElementById('hub_link_image').href = '/admin/event-image?event_id=' + id;
            document.getElementById('hub_link_categories').href = '/admin/events/' + id + '/categories';
            document.getElementById('hub_link_payment').href = '/admin/payment-accounts?event_id=' + id;
            document.getElementById('hub_link_forms').href = '/admin/form-fields?event_id=' + id;

            // Trigger edit button inside hub
            document.getElementById('btnHubTriggerEdit').onclick = function() {
                document.getElementById('modalKelolaHub').classList.add('hidden');
                openEdit(id, nama, lokasi, tanggal, harga, kategori, urlBeli, thumbnail, waktu, deskripsi, syarat_ketentuan);
            };

            document.getElementById('modalKelolaHub').classList.remove('hidden');
        }

        // Open Edit Modal
        function openEdit(id, nama, lokasi, tanggal, harga, kategori, urlBeli, thumbnail, waktu, deskripsi, syarat_ketentuan) {
            var form = document.getElementById('editForm');
            form.action = '/admin/events/' + id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_lokasi').value = lokasi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_urlBeli').value = urlBeli || '';

            var preview = document.getElementById('edit_thumbnail_preview');
            var link = document.getElementById('edit_thumbnail_link');
            if (thumbnail) {
                preview.classList.remove('hidden');
                link.href = thumbnail;
            } else {
                preview.classList.add('hidden');
            }

            document.getElementById('edit_waktu').value = waktu || '';
            document.getElementById('edit_deskripsi').value = deskripsi || '';
            document.getElementById('edit_syarat_ketentuan').value = syarat_ketentuan || '';

            document.getElementById('modalEdit').classList.remove('hidden');
        }

        // Checkboxes and Bulk delete logic
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.event-checkbox');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const selectedCount = document.getElementById('selectedCount');

            function updateBulkDeleteButton() {
                const checked = document.querySelectorAll('.event-checkbox:checked');
                selectedCount.textContent = checked.length;
                if (checked.length > 0) {
                    btnBulkDelete.classList.remove('hidden');
                } else {
                    btnBulkDelete.classList.add('hidden');
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateBulkDeleteButton();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const allChecked = document.querySelectorAll('.event-checkbox:checked').length === checkboxes.length;
                    if (selectAll) selectAll.checked = allChecked;
                    updateBulkDeleteButton();
                });
            });
        });

        function submitBulkDelete() {
            const checked = document.querySelectorAll('.event-checkbox:checked');
            if (checked.length === 0) return;
            if (confirm('Hapus ' + checked.length + ' event terpilih beserta pengaturannya?')) {
                const ids = Array.from(checked).map(cb => cb.value);

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.events.bulk-destroy") }}';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modals on backdrop click
        ['modalAdd', 'modalEdit', 'modalKelolaHub'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', function (e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            }
        });
    </script>
@endsection