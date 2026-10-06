@extends('admin.layouts.admin')

@section('header_title', 'Dashboard Overview')

@section('content')

{{-- Top Toolbar: Filter Event & Quick Actions (Clean, Non-Slop, High Usability) --}}
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 mb-6 shadow-xs">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        {{-- Context & Event Selector --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3.5 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 border border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <label for="eventSelector" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                    Filter Data Event
                </label>
                @if(auth()->user()->isSuperAdmin())
                    <div class="flex items-center gap-2">
                        <select id="eventSelector" onchange="window.location.href = this.value"
                            class="bg-slate-50 border border-slate-300 text-slate-800 text-sm font-semibold rounded-xl px-3.5 py-2 outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition w-full sm:w-80 cursor-pointer">
                            <option value="{{ route('admin.dashboard') }}" {{ empty($selectedEventId) ? 'selected' : '' }}>
                                Semua Event (Akumulatif Global)
                            </option>
                            <optgroup label="Pilih Event Spesifik:">
                                @foreach($eventsList as $ev)
                                    <option value="{{ route('admin.dashboard', ['event_id' => $ev['id']]) }}" {{ ($selectedEventId ?? null) == $ev['id'] ? 'selected' : '' }}>
                                        {{ $ev['nama'] }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        @if(!empty($selectedEventId))
                            <a href="{{ route('admin.dashboard') }}" 
                               class="text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition-colors whitespace-nowrap"
                               title="Tampilkan data seluruh event">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span>{{ auth()->user()->event->title ?? 'Event Terdaftar' }}</span>
                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                            Event Anda
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Direct Actions --}}
        <div class="flex flex-wrap items-center gap-2.5 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.events') }}" 
                   class="bg-slate-900 hover:bg-slate-800 text-white font-semibold px-4 py-2 rounded-xl text-xs transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Manajemen Event
                </a>
            @endif
            <a href="{{ route('admin.scanner') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl text-xs transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Scanner QR
            </a>
        </div>
    </div>

    {{-- Event Specific Summary Strip (if filtered) --}}
    @if(!empty($selectedEvent))
        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-blue-50/50 p-3.5 rounded-xl border border-blue-100">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-8 rounded-lg overflow-hidden bg-slate-200 shrink-0 border border-slate-300">
                    @if(!empty($selectedEvent['thumbnail']))
                        <img src="{{ $selectedEvent['thumbnail'] }}" alt="{{ $selectedEvent['nama'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-700 text-white flex items-center justify-center font-bold text-[10px]">
                            {{ strtoupper(substr($selectedEvent['nama'], 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900 truncate">{{ $selectedEvent['nama'] }}</span>
                        <span class="text-[10px] font-semibold text-slate-500">• {{ $selectedEvent['tanggal'] }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $selectedEvent['lokasi'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.event-image', ['event_id' => $selectedEvent['id']]) }}"
                   class="text-xs font-bold text-blue-700 hover:text-blue-800 bg-white border border-blue-200 px-3 py-1.5 rounded-lg transition-colors">
                    Kelola Pengaturan Event Ini →
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Pending Orders Alert (Clean, No Emojis) --}}
@if($pendingOrders > 0)
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 sm:p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <h4 class="font-bold text-amber-900 text-sm">
                {{ $pendingOrders }} Pesanan Menunggu Verifikasi
            </h4>
            <p class="text-xs text-amber-700 mt-0.5">
                @if(!empty($selectedEvent))
                    Pesanan masuk khusus untuk event {{ $selectedEvent['nama'] }}.
                @else
                    Terdapat pesanan masuk yang menunggu konfirmasi bukti transfer pembayaran.
                @endif
            </p>
        </div>
    </div>
    <a href="{{ route('admin.orders') }}"
        class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition-colors whitespace-nowrap text-center">
        Verifikasi Pesanan →
    </a>
</div>
@endif

{{-- Stat KPI Metrics Cards (Clean, Professional, Direct Numbers, No AI-Slop Pills) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
    {{-- Metric 1: Total Participants --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Peserta</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            {{ number_format($totalParticipants) }}
        </div>
        <p class="text-xs text-slate-500 mt-1.5">
            {{ !empty($selectedEvent) ? 'Peserta terdaftar di event ini' : 'Akumulasi dari seluruh event' }}
        </p>
    </div>

    {{-- Metric 2: Total Revenue --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Pendapatan</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight truncate">
            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
        </div>
        <p class="text-xs text-slate-500 mt-1.5">
            {{ !empty($selectedEvent) ? 'Pembayaran lunas event ini' : 'Pembayaran lunas terverifikasi' }}
        </p>
    </div>

    {{-- Metric 3: Tickets Sold --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tiket Terjual</span>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            {{ number_format($ticketsSold) }}
        </div>
        <p class="text-xs text-slate-500 mt-1.5">
            {{ !empty($selectedEvent) ? 'Tiket valid event ini' : 'Tiket aktif siap check-in' }}
        </p>
    </div>

    {{-- Metric 4: Checked-In --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Kehadiran (Check-In)</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            {{ number_format($checkedIn) }}
        </div>
        @php($checkinRate = $ticketsSold > 0 ? round(($checkedIn / $ticketsSold) * 100) : 0)
        <p class="text-xs text-slate-500 mt-1.5">
            Tingkat kehadiran: <span class="font-bold text-slate-800">{{ $checkinRate }}%</span>
        </p>
    </div>
</div>

{{-- Quick Actions Navigation --}}
<div class="bg-white rounded-2xl border border-slate-200/80 p-5 mb-6 shadow-xs">
    <div class="mb-4">
        <h3 class="text-sm font-bold text-slate-900">Aksi Cepat Operasional</h3>
        <p class="text-xs text-slate-500">Pintasan menu untuk tugas administrasi harian</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.events') }}" 
           class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-blue-500 hover:shadow-xs transition-all flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-bold text-slate-800 text-xs truncate">Manajemen Event</div>
                <div class="text-[11px] text-slate-500 truncate">Katalog & kelola event</div>
            </div>
        </a>
        @endif

        <a href="{{ route('admin.orders') }}" 
           class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-amber-500 hover:shadow-xs transition-all flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-bold text-slate-800 text-xs truncate">Pesanan Masuk</div>
                <div class="text-[11px] text-slate-500 truncate">Verifikasi transfer tiket</div>
            </div>
        </a>

        <a href="{{ route('admin.scanner') }}" 
           class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-indigo-500 hover:shadow-xs transition-all flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-bold text-slate-800 text-xs truncate">QR Scanner</div>
                <div class="text-[11px] text-slate-500 truncate">Validasi e-tiket peserta</div>
            </div>
        </a>

        <a href="{{ route('admin.participants') }}" 
           class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-500 hover:shadow-xs transition-all flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-bold text-slate-800 text-xs truncate">Daftar Peserta</div>
                <div class="text-[11px] text-slate-500 truncate">Lihat & ekspor data CSV</div>
            </div>
        </a>
    </div>
</div>

{{-- Two-Column Data Overview --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Column 1: Event List / Selected Event Settings --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">
                    {{ !empty($selectedEvent) ? 'Modul Pengaturan Event' : 'Katalog Event Aktif' }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ !empty($selectedEvent) ? 'Pengaturan terpadu untuk event ' . $selectedEvent['nama'] : 'Event yang sedang dibuka di platform' }}
                </p>
            </div>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.events') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    Semua Event →
                </a>
            @endif
        </div>

        @if(!empty($selectedEvent))
            {{-- Quick Setting Tiles for the Selected Event --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('admin.event-image', ['event_id' => $selectedEvent['id']]) }}"
                   class="p-3.5 rounded-xl border border-slate-200 hover:border-blue-500 bg-slate-50/50 hover:bg-white transition-all flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">Poster & Banner</div>
                        <div class="text-[11px] text-slate-500">Atur gambar 16:9</div>
                    </div>
                </a>

                <a href="{{ route('admin.events.categories', $selectedEvent['id']) }}"
                   class="p-3.5 rounded-xl border border-slate-200 hover:border-purple-500 bg-slate-50/50 hover:bg-white transition-all flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">Kategori Tiket</div>
                        <div class="text-[11px] text-slate-500">Kuota & harga tiket</div>
                    </div>
                </a>

                <a href="{{ route('admin.payment-accounts', ['event_id' => $selectedEvent['id']]) }}"
                   class="p-3.5 rounded-xl border border-slate-200 hover:border-emerald-500 bg-slate-50/50 hover:bg-white transition-all flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">Rekening Transfer</div>
                        <div class="text-[11px] text-slate-500">Bank & QRIS pembayaran</div>
                    </div>
                </a>

                <a href="{{ route('admin.form-fields', ['event_id' => $selectedEvent['id']]) }}"
                   class="p-3.5 rounded-xl border border-slate-200 hover:border-amber-500 bg-slate-50/50 hover:bg-white transition-all flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">Formulir Peserta</div>
                        <div class="text-[11px] text-slate-500">Kolom isian pendaftaran</div>
                    </div>
                </a>
            </div>
        @else
            {{-- List of Events with Thumbnails --}}
            <div class="space-y-3">
                @forelse($recentEvents as $ev)
                    <div class="p-3 rounded-xl border border-slate-200/80 bg-slate-50/40 hover:bg-white transition-colors flex items-center gap-3.5">
                        <div class="w-16 h-11 rounded-lg overflow-hidden bg-slate-200 shrink-0 border border-slate-300">
                            @if(!empty($ev['thumbnail']))
                                <img src="{{ $ev['thumbnail'] }}" alt="{{ $ev['nama'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-700 text-white flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($ev['nama'], 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-xs font-bold text-slate-900 truncate">{{ $ev['nama'] }}</span>
                                @if(($ev['kategori'] ?? '') === 'upcoming')
                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Upcoming</span>
                                @else
                                    <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Highlight</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 truncate">{{ $ev['tanggal'] }} • {{ $ev['lokasi'] }}</p>
                        </div>
                        <a href="{{ route('admin.dashboard', ['event_id' => $ev['id']]) }}"
                           class="shrink-0 text-xs font-semibold text-blue-700 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1.5 rounded-lg transition-colors">
                            Filter Event
                        </a>
                    </div>
                @empty
                    <div class="py-10 text-center text-xs text-slate-400">
                        Belum ada event terdaftar.
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    {{-- Column 2: Recent Orders Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">
                    {{ !empty($selectedEvent) ? 'Pesanan Event Ini' : 'Pesanan Masuk Terbaru' }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Transaksi pembelian tiket yang baru masuk</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                Semua Pesanan →
            </a>
        </div>

        <div class="space-y-3">
            @forelse($recentOrders as $ord)
                <div class="p-3 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between gap-3 text-xs">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono font-bold text-slate-800">{{ $ord->order_code }}</span>
                            @if($ord->payment_status === \App\Models\Order::STATUS_PAID)
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Lunas</span>
                            @elseif($ord->payment_status === \App\Models\Order::STATUS_WAITING)
                                <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Menunggu</span>
                            @elseif($ord->payment_status === \App\Models\Order::STATUS_REJECTED)
                                <span class="text-[9px] font-bold text-red-700 bg-red-50 px-1.5 py-0.5 rounded border border-red-200">Ditolak</span>
                            @else
                                <span class="text-[9px] font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">{{ $ord->payment_status }}</span>
                            @endif
                        </div>
                        <p class="font-semibold text-slate-700 truncate">
                            {{ $ord->user->name ?? 'Pembeli' }} • {{ $ord->event->title ?? 'Event' }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <div class="font-bold text-slate-900">
                            Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                        </div>
                        <a href="{{ route('admin.orders') }}" class="text-[11px] text-blue-600 hover:underline">
                            Periksa
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-xs text-slate-400">
                    Belum ada pesanan masuk.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
