@extends('layouts.app')

@section('title', 'Pendaftaran ' . $event['nama'] . ' — SeTiket')

@php
    // Baris peserta yang dirender ulang saat validasi gagal; minimal satu baris kosong.
    $rows = old('participants', [[]]);
    $maxTickets = \App\Http\Controllers\RegistrationController::MAX_TICKETS_PER_ORDER;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-10">

    {{-- Tanpa rekening tujuan, pesanan tidak bisa diproses — formulir tidak
         ditampilkan sama sekali daripada peserta terlanjur mengisi lalu ditolak. --}}
    @if($paymentAccounts->isEmpty())
        <div class="max-w-2xl mx-auto card p-8 md:p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-accent-50 text-accent-600 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-ink-900 mb-3">Pendaftaran belum dibuka</h1>
            <p class="text-ink-500 text-sm leading-relaxed mb-8">
                Penyelenggara <span class="text-ink-900 font-semibold">{{ $event['nama'] }}</span> belum menetapkan
                rekening tujuan pembayaran, jadi pemesanan tiket belum bisa diproses. Silakan cek kembali nanti.
            </p>
            <a href="{{ route('home') }}"
                class="btn btn-primary px-6 py-3">
                Kembali ke Beranda
            </a>
        </div>
    @else

    <form id="regForm" action="{{ url('/register-event') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="event_id" value="{{ $event['id'] }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- COLUMN 1 & 2: Form Fields --}}
            <div class="lg:col-span-2 space-y-8">

                <div class="card p-6 md:p-9 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-brand-600"></div>

                    <div class="mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-ink-900 mb-2">{{ $event['nama'] }}</h2>
                        <p class="text-ink-500 text-sm">
                            Lengkapi data peserta di bawah ini. Anda bisa memesan sampai {{ $maxTickets }} tiket
                            sekaligus dalam satu kali pembayaran.
                        </p>
                    </div>

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="mb-6 rounded-btn bg-red-50 border border-red-200 text-red-800 px-5 py-4">
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Step 1: Data Peserta --}}
                    <div class="space-y-6">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <h3 class="text-lg font-bold text-ink-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                                Data Peserta
                            </h3>
                            <div class="text-sm text-ink-500">
                                Akun: <span class="text-ink-900 font-medium">{{ auth()->user()->email }}</span>
                            </div>
                        </div>

                        <div id="participantList" class="space-y-6">
                            @foreach($rows as $i => $row)
                                <div class="participant-card border border-line rounded-card p-5 md:p-6 space-y-6 bg-white">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-ink-900 flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-700 text-sm flex items-center justify-center font-bold participant-number">{{ $i + 1 }}</span>
                                            Peserta <span class="participant-number-text">{{ $i + 1 }}</span>
                                        </h4>
                                        <button type="button"
                                            class="btn-remove-participant text-xs font-semibold text-red-600 hover:text-red-700 transition-colors {{ count($rows) <= 1 ? 'hidden' : '' }}">
                                            Hapus peserta
                                        </button>
                                    </div>

                                    {{-- Seluruh isian mengikuti konfigurasi formulir milik event ini,
                                         diatur admin lewat /admin/form-fields. Tidak ada field yang
                                         dipaksakan dari sini. --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @forelse($formFields as $field)
                                            @include('partials.form-field', [
                                                'field' => $field,
                                                'i' => $i,
                                                'row' => $row,
                                                'categories' => $categories,
                                            ])
                                        @empty
                                            <p class="md:col-span-2 text-sm text-ink-500">
                                                Penyelenggara tidak meminta data tambahan. Lanjutkan ke pembayaran.
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <button type="button" id="btnAddParticipant"
                                class="btn px-5 py-3 text-sm border border-dashed border-line text-ink-900 hover:border-brand-500 hover:bg-brand-50">
                                + Tambah Peserta
                            </button>
                            <p class="text-xs text-ink-500" id="participantLimitNote">
                                Maksimal {{ $maxTickets }} tiket per pesanan.
                            </p>
                        </div>

                        <div class="pt-4 flex justify-end" id="btnNextContainer">
                            <button type="button" id="btnNext"
                                class="btn btn-primary w-full md:w-auto px-10 py-3.5 text-base"
                                >
                                Lanjutkan ke Pembayaran
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Payment Details --}}
                    <div id="paymentSection" class="hidden pt-8 border-t border-white/10 mt-8 space-y-6">
                        <h3 class="text-lg font-bold text-ink-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                            Konfirmasi Pembayaran
                        </h3>

                        <div class="rounded-card border border-line bg-gray-50 p-5 space-y-4">
                            <div class="text-sm text-ink-500">
                                Transfer <span class="font-semibold text-accent-600" id="paymentTotalText">Rp 0</span>
                                (total seluruh tiket) ke salah satu rekening resmi event ini, lalu unggah satu bukti transfer.
                            </div>

                            {{-- Rekening milik event ini, diatur super admin --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($paymentAccounts as $index => $account)
                                    <label class="relative cursor-pointer block">
                                        <input type="radio" name="payment_account_id" value="{{ $account->id }}" class="peer sr-only"
                                            required
                                            {{ (int) old('payment_account_id', $paymentAccounts->first()->id) === $account->id ? 'checked' : '' }}>
                                        <div class="p-4 rounded-btn border border-line bg-white hover:border-gray-300 peer-checked:border-brand-600 peer-checked:bg-brand-50 transition-all">
                                            <div class="font-semibold text-ink-900 mb-1">{{ $account->bank_name }}</div>
                                            <div class="text-xs text-ink-900 font-mono tracking-wider font-semibold">
                                                {{ $account->numberLabel() }}: {{ $account->account_number }}
                                            </div>
                                            <div class="text-xs text-ink-500 mt-1">a.n. {{ $account->account_holder }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <p class="text-xs text-ink-500">
                                Rekening di atas adalah rekening resmi <span class="text-ink-900">{{ $event['nama'] }}</span>.
                                Jangan mentransfer ke nomor lain.
                            </p>
                        </div>

                        {{-- Upload Receipt File --}}
                        <div class="space-y-2">
                            <label class="label">Upload Bukti Transfer *</label>
                            <div class="rounded-btn border border-line bg-gray-50 p-4">
                                <input type="file" name="proof" id="proofInput" accept="image/*" required
                                    class="block w-full text-sm text-ink-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer">
                                <div class="text-xs text-ink-500 mt-2">
                                    Cukup satu bukti untuk seluruh tiket dalam pesanan ini. Format JPG/PNG/GIF, maksimal 2MB.
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" id="btnSubmit" disabled
                                class="btn w-full py-4 text-base opacity-50 cursor-not-allowed bg-gray-300 text-gray-600">
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
                <div class="card p-6 space-y-6 lg:sticky lg:top-24">

                    <div>
                        <h3 class="text-lg font-bold text-ink-900">Ringkasan Pembelian</h3>
                        <div class="mt-1 h-0.5 w-12 bg-accent-500 rounded-full"></div>
                    </div>

                    <div class="flex items-center gap-4 rounded-btn border border-line p-3 bg-gray-50">
                        <div class="w-16 h-16 shrink-0 rounded-xl overflow-hidden">
                            @include('partials.event-image', [
                                'nama' => $event['nama'],
                                'thumbnail' => $event['thumbnail'] ?? null,
                                'class' => 'w-16 h-16 object-cover',
                            ])
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-ink-900 truncate">{{ $event['nama'] }}</div>
                            <div class="text-xs text-ink-500 mt-0.5 truncate">{{ $event['lokasi'] }}</div>
                        </div>
                    </div>

                    <div class="space-y-3.5 text-sm border-b border-line pb-5">
                        <div class="flex justify-between">
                            <span class="text-ink-500">Tanggal</span>
                            <span class="font-semibold text-ink-900 text-right">{{ $event['tanggal'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-500">Waktu</span>
                            <span class="font-semibold text-ink-900 text-right">{{ $event['waktu'] }}</span>
                        </div>
                    </div>

                    {{-- Rincian tiket, diisi ulang oleh JS --}}
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-ink-500 uppercase tracking-widest font-semibold mb-2">
                                Tiket (<span id="summaryCount">1</span>)
                            </div>
                            <div id="summaryLines" class="rounded-btn border border-line bg-gray-50 p-4 space-y-3"></div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-line">
                            <span class="text-ink-900 font-bold">Total Pembayaran</span>
                            <span class="font-display text-2xl font-bold text-accent-600" id="summaryTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-line space-y-2 text-xs text-ink-500">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-[10px]">✓</div>
                            <span class="text-ink-900 font-medium">Langkah 1: Isi Data Peserta</span>
                        </div>
                        <div class="flex items-center gap-2" id="stepIndicator2">
                            <div class="w-4 h-4 rounded-full bg-gray-200 text-ink-500 flex items-center justify-center font-bold text-[10px]" id="stepIcon2">2</div>
                            <span>Langkah 2: Konfirmasi Pembayaran</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    @endif
</div>

@if($paymentAccounts->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function () {
    const MAX_TICKETS = {{ $maxTickets }};

    const categories = {
        @foreach($categories as $cat)
        '{{ $cat->code }}': { name: @json($cat->name), price: {{ $cat->price }} },
        @endforeach
    };

    // Kalau admin mematikan pilihan kategori, seluruh peserta memakai kategori
    // pertama event ini — sama seperti yang dihitung di sisi server.
    const kategoriCadangan = @json(optional($categories->first())->code);

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
            const select = card.querySelector('.category-select');
            const code = select ? select.value : kategoriCadangan;
            const nameInput = card.querySelector('[name$="[fullname]"]');
            const info = categories[code];
            if (!info) return;

            total += info.price;

            const label = (nameInput?.value || '').trim() || ('Peserta ' + (i + 1));
            const line = document.createElement('div');
            line.className = 'flex justify-between items-start gap-3 text-sm';
            line.innerHTML =
                '<div class="min-w-0">' +
                    '<div class="font-semibold text-ink-900 truncate"></div>' +
                    '<div class="text-xs text-ink-500"></div>' +
                '</div>' +
                '<div class="font-semibold text-accent-600 whitespace-nowrap"></div>';
            line.querySelector('.font-semibold').textContent = label;
            line.querySelector('.text-xs').textContent = info.name;
            line.querySelector('.font-semibold').textContent = rupiah(info.price);
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
        stepIcon2.classList.remove('bg-gray-200', 'text-ink-500');
        stepIcon2.classList.add('bg-brand-600', 'text-white');
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    proofInput.addEventListener('change', function () {
        const ready = proofInput.files.length > 0;
        btnSubmit.disabled = !ready;
        btnSubmit.classList.toggle('opacity-50', !ready);
        btnSubmit.classList.toggle('cursor-not-allowed', !ready);
        btnSubmit.classList.toggle('bg-gray-300', !ready);
        btnSubmit.classList.toggle('text-gray-600', !ready);
        btnSubmit.classList.toggle('btn-primary', ready);
        
        
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
@endif
@endsection
