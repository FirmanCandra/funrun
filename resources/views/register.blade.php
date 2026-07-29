@extends('layouts.app')

@section('title', 'Pendaftaran ' . $event['nama'] . ' — SeTiket')

@php
    // Baris peserta yang dirender ulang saat validasi gagal; minimal satu baris kosong.
    $rows = old('participants', [[]]);
    $maxTickets = \App\Http\Controllers\RegistrationController::MAX_TICKETS_PER_ORDER;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-10">

    <form id="regForm" action="{{ url('/register-event') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="event_id" value="{{ $event['id'] }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- COLUMN 1 & 2: Form Fields --}}
            <div class="lg:col-span-2 space-y-8">

                <div class="glass-panel rounded-3xl p-6 md:p-10 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-amber-500"></div>

                    <div class="mb-8">
                        <h2 class="text-3xl font-extrabold tracking-tight text-white mb-2">{{ $event['nama'] }}</h2>
                        <p class="text-gray-400 text-sm">
                            Lengkapi data peserta di bawah ini. Anda bisa memesan sampai {{ $maxTickets }} tiket
                            sekaligus dalam satu kali pembayaran.
                        </p>
                    </div>

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-5 py-3 rounded-xl">
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Step 1: Data Peserta --}}
                    <div class="space-y-6">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-400 text-sm flex items-center justify-center font-bold">1</span>
                                Data Peserta
                            </h3>
                            <div class="text-sm text-gray-400">
                                Akun: <span class="text-gray-200">{{ auth()->user()->email }}</span>
                            </div>
                        </div>

                        <div id="participantList" class="space-y-6">
                            @foreach($rows as $i => $row)
                                <div class="participant-card bg-slate-900/30 border border-white/10 rounded-2xl p-5 md:p-6 space-y-6">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-white flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 text-sm flex items-center justify-center font-bold participant-number">{{ $i + 1 }}</span>
                                            Peserta <span class="participant-number-text">{{ $i + 1 }}</span>
                                        </h4>
                                        <button type="button"
                                            class="btn-remove-participant text-xs font-semibold text-red-400 hover:text-red-300 transition-colors {{ count($rows) <= 1 ? 'hidden' : '' }}">
                                            Hapus peserta
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap (sesuai KTP) *</label>
                                            <input type="text" name="participants[{{ $i }}][fullname]" required
                                                value="{{ $row['fullname'] ?? ($i === 0 ? auth()->user()->name : '') }}"
                                                class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-2">NIK (16 Digit) *</label>
                                            <input type="text" name="participants[{{ $i }}][nik]" required minlength="16" maxlength="16"
                                                pattern="[0-9]{16}" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ $row['nik'] ?? '' }}" placeholder="contoh: 3578123456789012"
                                                class="nik-input w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                                            <p class="text-xs text-gray-500 mt-1">NIK tiap peserta harus berbeda.</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-2">Nomor WhatsApp *</label>
                                            <input type="text" name="participants[{{ $i }}][phone]" required value="{{ $row['phone'] ?? '' }}"
                                                class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-2">Kategori Run <span class="text-amber-400">*</span></label>
                                            <select name="participants[{{ $i }}][category]" required
                                                class="category-select w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->code }}" {{ ($row['category'] ?? '') === $cat->code ? 'selected' : '' }}>
                                                        {{ $cat->name }} (Rp {{ number_format($cat->price, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">Boleh berbeda antar peserta.</p>
                                        </div>

                                        {{-- Field selebihnya mengikuti konfigurasi formulir milik event ini,
                                             diatur admin event lewat /admin/form-fields. --}}
                                        @foreach($formFields as $field)
                                            @include('partials.form-field', ['field' => $field, 'i' => $i, 'row' => $row])
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <button type="button" id="btnAddParticipant"
                                class="px-5 py-3 rounded-xl font-semibold text-sm border border-dashed border-white/20 text-gray-300 hover:text-white hover:border-amber-500/50 hover:bg-amber-500/5 transition-all">
                                + Tambah Peserta
                            </button>
                            <p class="text-xs text-gray-500" id="participantLimitNote">
                                Maksimal {{ $maxTickets }} tiket per pesanan.
                            </p>
                        </div>

                        <div class="pt-4 flex justify-end" id="btnNextContainer">
                            <button type="button" id="btnNext"
                                class="w-full md:w-auto px-10 py-4 rounded-2xl font-bold text-lg transition-all transform hover:-translate-y-1 hover:shadow-2xl"
                                style="background: linear-gradient(135deg, #F5A623, #d48f1a); color: #0a0425; box-shadow: 0 4px 20px rgba(245,166,35,0.25);">
                                Lanjutkan ke Pembayaran
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Payment Details --}}
                    <div id="paymentSection" class="hidden pt-8 border-t border-white/10 mt-8 space-y-6">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 text-sm flex items-center justify-center font-bold">2</span>
                            Konfirmasi Pembayaran
                        </h3>

                        <div class="bg-slate-900/30 p-5 rounded-2xl border border-white/5 space-y-4">
                            <div class="text-sm text-gray-400">
                                Transfer <span class="font-bold text-amber-400" id="paymentTotalText">Rp 0</span>
                                (total seluruh tiket) ke salah satu metode di bawah ini, lalu unggah satu bukti transfer.
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($paymentMethods as $index => $pm)
                                    <label class="relative cursor-pointer block">
                                        <input type="radio" name="payment_method" value="{{ $pm['name'] }}" class="peer sr-only"
                                            {{ old('payment_method', $paymentMethods[0]['name']) === $pm['name'] ? 'checked' : '' }}>
                                        <div class="p-4 rounded-xl border border-white/10 bg-slate-950/40 hover:bg-slate-900/30 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all">
                                            <div class="font-bold text-white mb-1">{{ $pm['name'] }}</div>
                                            <div class="text-xs text-amber-400 font-mono tracking-wider font-bold">
                                                @if(stripos($pm['name'], 'e-wallet') !== false || stripos($pm['name'], 'dana') !== false || stripos($pm['name'], 'gopay') !== false || stripos($pm['name'], 'ovo') !== false || stripos($pm['name'], 'linkaja') !== false)
                                                    No. HP:
                                                @else
                                                    No. Rek:
                                                @endif
                                                {{ $pm['account_number'] ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1">a.n. {{ $pm['account_holder'] ?? '-' }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Upload Receipt File --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-300">Upload Bukti Transfer *</label>
                            <div class="relative rounded-xl border border-white/10 bg-slate-900/50 p-4 transition-all">
                                <input type="file" name="proof" id="proofInput" accept="image/*" required
                                    class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                <div class="text-xs text-gray-400 mt-2">
                                    Cukup satu bukti untuk seluruh tiket dalam pesanan ini. Format JPG/PNG/GIF, maksimal 2MB.
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" id="btnSubmit" disabled
                                class="w-full py-4 rounded-2xl font-bold text-lg flex justify-center items-center gap-2.5 transition-all opacity-50 cursor-not-allowed text-slate-800 bg-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Konfirmasi Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMN 3: Sticky Summary --}}
            <div class="lg:col-span-1">
                <div class="glass-panel rounded-3xl p-6 shadow-2xl space-y-6 md:sticky md:top-28 border border-white/10">

                    <div>
                        <h3 class="text-lg font-bold text-white">Ringkasan Pembelian</h3>
                        <div class="mt-1 h-0.5 w-12 bg-amber-500 rounded-full"></div>
                    </div>

                    <div class="flex items-center gap-4 bg-slate-950/40 p-3 rounded-2xl border border-white/5">
                        <img src="{{ $event['thumbnail'] ?: 'https://placehold.co/150x150/1a0a5e/ffffff?text=Event' }}"
                            alt="{{ $event['nama'] }}" class="w-16 h-16 object-cover rounded-xl shrink-0"
                            onerror="this.src='https://placehold.co/150x150/1a0a5e/ffffff?text=Event'">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-white truncate">{{ $event['nama'] }}</div>
                            <div class="text-xs text-gray-400 mt-0.5 truncate">{{ $event['lokasi'] }}</div>
                        </div>
                    </div>

                    <div class="space-y-3.5 text-sm border-b border-white/5 pb-5">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tanggal</span>
                            <span class="font-semibold text-white text-right">{{ $event['tanggal'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Waktu</span>
                            <span class="font-semibold text-white text-right">{{ $event['waktu'] }}</span>
                        </div>
                    </div>

                    {{-- Rincian tiket, diisi ulang oleh JS --}}
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-2">
                                Tiket (<span id="summaryCount">1</span>)
                            </div>
                            <div id="summaryLines" class="bg-slate-900/50 p-4 rounded-xl border border-white/5 space-y-3"></div>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-white/10">
                            <span class="text-gray-300 font-bold">Total Pembayaran</span>
                            <span class="text-2xl font-black text-white" id="summaryTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/5 space-y-2 text-xs text-gray-500">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-blue-500 text-slate-900 flex items-center justify-center font-bold text-[10px]">✓</div>
                            <span class="text-gray-300 font-medium">Langkah 1: Isi Data Peserta</span>
                        </div>
                        <div class="flex items-center gap-2" id="stepIndicator2">
                            <div class="w-4 h-4 rounded-full bg-slate-800 flex items-center justify-center font-bold text-[10px]" id="stepIcon2">2</div>
                            <span>Langkah 2: Konfirmasi Pembayaran</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const MAX_TICKETS = {{ $maxTickets }};

    const categories = {
        @foreach($categories as $cat)
        '{{ $cat->code }}': { name: @json($cat->name), price: {{ $cat->price }} },
        @endforeach
    };

    const list = document.getElementById('participantList');
    const btnAdd = document.getElementById('btnAddParticipant');
    const limitNote = document.getElementById('participantLimitNote');
    const summaryLines = document.getElementById('summaryLines');
    const summaryCount = document.getElementById('summaryCount');
    const summaryTotal = document.getElementById('summaryTotal');
    const paymentTotalText = document.getElementById('paymentTotalText');

    const btnNext = document.getElementById('btnNext');
    const btnNextContainer = document.getElementById('btnNextContainer');
    const paymentSection = document.getElementById('paymentSection');
    const proofInput = document.getElementById('proofInput');
    const btnSubmit = document.getElementById('btnSubmit');
    const stepIcon2 = document.getElementById('stepIcon2');

    const rupiah = n => 'Rp ' + n.toLocaleString('id-ID');

    function cards() {
        return Array.from(list.querySelectorAll('.participant-card'));
    }

    // Nomor urut dan nama field harus selalu rapat 0..n-1 setelah tambah/hapus.
    function reindex() {
        cards().forEach((card, i) => {
            card.querySelectorAll('[name^="participants["]').forEach(input => {
                input.name = input.name.replace(/^participants\[\d+\]/, 'participants[' + i + ']');
            });
            card.querySelector('.participant-number').textContent = i + 1;
            card.querySelector('.participant-number-text').textContent = i + 1;
            card.querySelector('.btn-remove-participant').classList.toggle('hidden', cards().length <= 1);
        });

        const full = cards().length >= MAX_TICKETS;
        btnAdd.disabled = full;
        btnAdd.classList.toggle('opacity-40', full);
        btnAdd.classList.toggle('cursor-not-allowed', full);
        limitNote.textContent = full
            ? 'Batas ' + MAX_TICKETS + ' tiket per pesanan tercapai.'
            : 'Maksimal ' + MAX_TICKETS + ' tiket per pesanan.';
    }

    function updateSummary() {
        let total = 0;
        summaryLines.innerHTML = '';

        cards().forEach((card, i) => {
            const code = card.querySelector('.category-select').value;
            const nameInput = card.querySelector('[name$="[fullname]"]');
            const info = categories[code];
            if (!info) return;

            total += info.price;

            const label = (nameInput.value || '').trim() || ('Peserta ' + (i + 1));
            const line = document.createElement('div');
            line.className = 'flex justify-between items-start gap-3 text-sm';
            line.innerHTML =
                '<div class="min-w-0">' +
                    '<div class="font-semibold text-white truncate"></div>' +
                    '<div class="text-xs text-gray-400"></div>' +
                '</div>' +
                '<div class="font-bold text-amber-400 whitespace-nowrap"></div>';
            line.querySelector('.font-semibold').textContent = label;
            line.querySelector('.text-xs').textContent = info.name;
            line.querySelector('.font-bold').textContent = rupiah(info.price);
            summaryLines.appendChild(line);
        });

        summaryCount.textContent = cards().length;
        summaryTotal.textContent = rupiah(total);
        paymentTotalText.textContent = rupiah(total);
    }

    // Kosongkan isian pada kartu hasil duplikasi.
    function addParticipant() {
        if (cards().length >= MAX_TICKETS) return;

        const clone = cards()[0].cloneNode(true);
        clone.querySelectorAll('input').forEach(input => {
            if (input.type === 'file') return;
            // Checkbox cukup dilepas centangnya; value-nya ('1') harus tetap.
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
                return;
            }
            // Hidden pendamping checkbox menyimpan nilai '0' — jangan dikosongkan.
            if (input.type === 'hidden') return;
            input.value = '';
        });
        clone.querySelectorAll('select').forEach(select => { select.selectedIndex = 0; });
        clone.querySelectorAll('textarea').forEach(area => { area.value = ''; });

        list.appendChild(clone);
        reindex();
        updateSummary();
        clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    btnAdd.addEventListener('click', addParticipant);

    list.addEventListener('click', function (e) {
        if (!e.target.classList.contains('btn-remove-participant')) return;
        if (cards().length <= 1) return;
        e.target.closest('.participant-card').remove();
        reindex();
        updateSummary();
    });

    list.addEventListener('input', updateSummary);
    list.addEventListener('change', updateSummary);

    // Langkah 2 baru terbuka setelah data peserta valid.
    btnNext.addEventListener('click', function () {
        const inputs = list.querySelectorAll('input[required], select[required], textarea[required]');
        for (const input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                return;
            }
        }

        const niks = Array.from(list.querySelectorAll('.nik-input')).map(i => i.value.trim());
        const duplicate = niks.find((nik, i) => niks.indexOf(nik) !== i);
        if (duplicate) {
            alert('NIK ' + duplicate + ' dipakai lebih dari satu peserta. Setiap peserta harus punya NIK berbeda.');
            return;
        }

        paymentSection.classList.remove('hidden');
        btnNextContainer.classList.add('hidden');
        stepIcon2.classList.remove('bg-slate-800');
        stepIcon2.classList.add('bg-blue-500', 'text-slate-900');
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    proofInput.addEventListener('change', function () {
        const ready = proofInput.files.length > 0;
        btnSubmit.disabled = !ready;
        btnSubmit.classList.toggle('opacity-50', !ready);
        btnSubmit.classList.toggle('cursor-not-allowed', !ready);
        btnSubmit.classList.toggle('bg-slate-600', !ready);
        btnSubmit.classList.toggle('text-slate-800', !ready);
        btnSubmit.classList.toggle('bg-emerald-500', ready);
        btnSubmit.classList.toggle('hover:bg-emerald-400', ready);
        btnSubmit.classList.toggle('text-white', ready);
    });

    reindex();
    updateSummary();

    // Kalau validasi server gagal, langkah 2 dibuka lagi supaya tidak bingung.
    @if($errors->any())
        paymentSection.classList.remove('hidden');
        btnNextContainer.classList.add('hidden');
    @endif
});
</script>
@endsection
