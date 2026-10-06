@php
    $currentEvent = $event ?? $dbEvent ?? null;
    $eventId = $currentEvent ? $currentEvent->id : request('event_id');
    $eventTitle = $currentEvent ? $currentEvent->title : 'Event';
@endphp

@if($currentEvent)
<div class="mb-6 bg-white rounded-2xl border border-slate-100 shadow-xs p-4 sm:p-5">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.events') }}" 
               class="shrink-0 w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors"
               title="Kembali ke Manajemen Event">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Kelola Event</span>
                    <span class="text-xs text-slate-400">• ID #{{ $currentEvent->id }}</span>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 truncate mt-0.5">{{ $eventTitle }}</h2>
            </div>
        </div>

        @if(auth()->user()->isSuperAdmin() && isset($events) && count($events) > 1)
        <div class="flex items-center gap-2 shrink-0">
            <label class="text-xs font-medium text-slate-500 whitespace-nowrap">Ganti Event:</label>
            <select onchange="window.location.href = updateQueryStringParameter(window.location.href, 'event_id', this.value)"
                    class="bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ $ev->id == $currentEvent->id ? 'selected' : '' }}>
                        {{ $ev->title }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center gap-1 sm:gap-2 overflow-x-auto pt-3 text-xs sm:text-sm">
        {{-- 1. Gambar Event --}}
        <a href="{{ route('admin.event-image', ['event_id' => $eventId]) }}"
           class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ ($activeTab ?? '') === 'image' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Gambar & Poster
        </a>

        {{-- 2. Kategori Tiket --}}
        <a href="{{ route('admin.events.categories', $eventId) }}"
           class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ ($activeTab ?? '') === 'categories' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            Kategori Tiket
        </a>

        {{-- 3. Rekening Pembayaran --}}
        <a href="{{ route('admin.payment-accounts', ['event_id' => $eventId]) }}"
           class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ ($activeTab ?? '') === 'payment' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            Rekening Pembayaran
        </a>

        {{-- 4. Formulir Pendaftaran --}}
        <a href="{{ route('admin.form-fields', ['event_id' => $eventId]) }}"
           class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ ($activeTab ?? '') === 'forms' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Formulir Pendaftaran
        </a>
    </div>
</div>

<script>
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
</script>
@endif
