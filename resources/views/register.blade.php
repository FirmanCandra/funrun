@extends('layouts.app')

@section('title', 'Pendaftaran ' . $event['nama'] . ' — SeTiket')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-10">
    
    {{-- Form element wraps both columns to submit everything --}}
    <form id="regForm" action="{{ url('/register-event') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <input type="hidden" name="event_id" value="{{ $event['id'] }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- COLUMN 1 & 2: Form Fields --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Form Box --}}
                <div class="glass-panel rounded-3xl p-6 md:p-10 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-amber-500"></div>
                    
                    <div class="mb-8">
                        <h2 class="text-3xl font-extrabold tracking-tight text-white mb-2">{{ $event['nama'] }}</h2>
                        <p class="text-gray-400 text-sm">Lengkapi formulir pendaftaran di bawah ini untuk mengamankan tiket Anda.</p>
                    </div>

                    {{-- Validation Errors --}}
                    @if($errors->any())
                    <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-5 py-3 rounded-xl">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Step 1: Personal Data --}}
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-400 text-sm flex items-center justify-center font-bold">1</span>
                            Data Diri Peserta
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap (sesuai KTP) *</label>
                                <input type="text" name="fullname" required value="{{ old('fullname') }}"
                                       class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                            </div>

                            <!-- NIK -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">NIK (16 Digit) *</label>
                                <input type="text" name="nik" required minlength="16" maxlength="16" pattern="[0-9]{16}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('nik') }}" placeholder="contoh: 3578123456789012"
                                       class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Alamat Email *</label>
                                <input type="email" name="email" required value="{{ old('email') }}"
                                       class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nomor WhatsApp *</label>
                                <input type="text" name="phone" required value="{{ old('phone') }}"
                                       class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tanggal Lahir *</label>
                                <input type="date" name="dob" required value="{{ old('dob') }}"
                                       class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Jenis Kelamin *</label>
                                <select name="gender" required
                                        class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all bg-slate-950">
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <!-- Jersey Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Ukuran Jersey *</label>
                                <select name="jersey_size" required
                                        class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all bg-slate-950">
                                    <option value="S" {{ old('jersey_size') === 'S' ? 'selected' : '' }}>S</option>
                                    <option value="M" {{ old('jersey_size') === 'M' ? 'selected' : '' }}>M</option>
                                    <option value="L" {{ old('jersey_size') === 'L' ? 'selected' : '' }}>L</option>
                                    <option value="XL" {{ old('jersey_size') === 'XL' ? 'selected' : '' }}>XL</option>
                                    <option value="XXL" {{ old('jersey_size') === 'XXL' ? 'selected' : '' }}>XXL</option>
                                </select>
                            </div>

                            <!-- Emergency Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Kontak Darurat (Nama - No. Telp) *</label>
                                <input type="text" name="emergency_contact" required value="{{ old('emergency_contact') }}" placeholder="contoh: Budi - 0812345678"
                                       class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Kategori Run *</label>
                                <select name="category" id="categorySelect" required
                                        class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all bg-slate-950">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->code }}" {{ old('category') === $cat->code ? 'selected' : '' }}>{{ $cat->name }} (Rp {{ number_format($cat->price, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Alamat Lengkap *</label>
                            <textarea name="address" rows="3" required
                                      class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">{{ old('address') }}</textarea>
                        </div>

                        <!-- Asal Kota/Kabupaten -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Asal Kota/Kabupaten *</label>
                            <input type="text" name="city" required value="{{ old('city') }}" placeholder="contoh: Kota Surabaya"
                                   class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                        </div>

                        <!-- Riwayat Penyakit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Riwayat Penyakit (Jika Ada)</label>
                            <textarea name="medical_history" rows="2" placeholder="Contoh: Asma, Jantung, Diabetes, dll. Kosongkan jika tidak ada."
                                      class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">{{ old('medical_history') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Opsional — untuk keamanan dan kesiapan medis selama event</p>
                        </div>

                        <div class="pt-4 flex justify-end" id="btnNextContainer">
                            <button type="button" id="btnNext"
                                    class="w-full md:w-auto px-10 py-4 rounded-2xl font-bold text-lg transition-all transform hover:-translate-y-1 hover:shadow-2xl"
                                    style="background: linear-gradient(135deg, #F5A623, #d48f1a); color: #0a0425; box-shadow: 0 4px 20px rgba(245,166,35,0.25);">
                                Lanjutkan ke Pembayaran
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Payment Details (Hidden initially) --}}
                    <div id="paymentSection" class="hidden pt-8 border-t border-white/10 mt-8 space-y-6">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 text-sm flex items-center justify-center font-bold">2</span>
                            Konfirmasi Pembayaran
                        </h3>

                        <div class="bg-slate-900/30 p-5 rounded-2xl border border-white/5 space-y-4">
                            <div class="text-sm text-gray-400">Silakan lakukan transfer ke salah satu metode pembayaran di bawah ini sebesar jumlah yang tertera di ringkasan pembelian.</div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($paymentMethods as $index => $pm)
                                <label class="relative cursor-pointer block">
                                    <input type="radio" name="payment_method" value="{{ $pm['name'] }}" class="peer sr-only" {{ $index === 0 ? 'checked' : '' }}>
                                    <div class="p-4 rounded-xl border border-white/10 bg-slate-950/40 hover:bg-slate-900/30 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all">
                                        <div class="font-bold text-white mb-1">{{ $pm['name'] }}</div>
                                        <div class="text-xs text-amber-400 font-mono tracking-wider font-bold">
                                            @if(stripos($pm['name'], 'e-wallet') !== false || stripos($pm['name'], 'dana') !== false || stripos($pm['name'], 'gopay') !== false || stripos($pm['name'], 'ovo') !== false || stripos($pm['name'], 'linkaja') !== false)
                                                No. HP:
                                            @else
                                                No. Rek:
                                            @endif
                                            {{ $pm['account_number'] }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">a.n. {{ $pm['account_holder'] }}</div>
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
                                <div class="text-xs text-gray-400 mt-2">Wajib mengunggah gambar bukti pembayaran dalam format JPG/PNG/GIF (maksimal 2MB).</div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" id="btnSubmit" disabled
                                    class="w-full py-4 rounded-2xl font-bold text-lg flex justify-center items-center gap-2.5 transition-all opacity-50 cursor-not-allowed text-slate-800 bg-slate-600"
                                    style="box-shadow: none;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                    
                    {{-- Summary Header --}}
                    <div>
                        <h3 class="text-lg font-bold text-white">Ringkasan Pembelian</h3>
                        <div class="mt-1 h-0.5 w-12 bg-amber-500 rounded-full"></div>
                    </div>

                    {{-- Event Card Thumbnail --}}
                    <div class="flex items-center gap-4 bg-slate-950/40 p-3 rounded-2xl border border-white/5">
                        <img src="{{ $event['thumbnail'] ?: 'https://placehold.co/150x150/1a0a5e/ffffff?text=Event' }}"
                             alt="{{ $event['nama'] }}"
                             class="w-16 h-16 object-cover rounded-xl shrink-0"
                             onerror="this.src='https://placehold.co/150x150/1a0a5e/ffffff?text=Event'">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-white truncate">{{ $event['nama'] }}</div>
                            <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="truncate">{{ $event['lokasi'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Info List --}}
                    <div class="space-y-3.5 text-sm border-b border-white/5 pb-5">
                        <div class="flex justify-between">
                            <span class="text-gray-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/></svg>
                                Tanggal
                            </span>
                            <span class="font-semibold text-white text-right">{{ $event['tanggal'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                Waktu
                            </span>
                            <span class="font-semibold text-white text-right">{{ $event['waktu'] }}</span>
                        </div>
                    </div>

                    {{-- Price Calculation --}}
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-2">Tiket yang dipilih</div>
                            <div class="bg-slate-900/50 p-4 rounded-xl border border-white/5 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-white" id="summaryCategory">3K Fun Walk</span>
                                    <span class="font-bold text-amber-400" id="summaryPrice">Rp 100.000</span>
                                </div>
                                <div class="text-xs text-gray-400" id="summaryDesc">Pendaftaran Kategori 3K Walk</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-white/10">
                            <span class="text-gray-300 font-bold">Total Pembayaran</span>
                            <span class="text-2xl font-black text-white" id="summaryTotal">Rp 100.000</span>
                        </div>
                    </div>

                    {{-- Steps progress --}}
                    <div class="pt-4 border-t border-white/5 space-y-2 text-xs text-gray-500">
                        <div class="flex items-center gap-2" id="stepIndicator1">
                            <div class="w-4 h-4 rounded-full bg-blue-500 text-slate-900 flex items-center justify-center font-bold text-[10px]" id="stepIcon1">✓</div>
                            <span class="text-gray-300 font-medium">Langkah 1: Isi Data Diri</span>
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
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const categorySelect = document.getElementById('categorySelect');
    const summaryCategory = document.getElementById('summaryCategory');
    const summaryPrice = document.getElementById('summaryPrice');
    const summaryDesc = document.getElementById('summaryDesc');
    const summaryTotal = document.getElementById('summaryTotal');
    
    const btnNext = document.getElementById('btnNext');
    const btnNextContainer = document.getElementById('btnNextContainer');
    const paymentSection = document.getElementById('paymentSection');
    const regForm = document.getElementById('regForm');
    
    const proofInput = document.getElementById('proofInput');
    const btnSubmit = document.getElementById('btnSubmit');

    const stepIndicator2 = document.getElementById('stepIndicator2');
    const stepIcon2 = document.getElementById('stepIcon2');

    // Category Price mapping
    const categories = {
        @foreach($categories as $cat)
        '{{ $cat->code }}': { 
            name: '{{ $cat->name }}', 
            price: {{ $cat->price }}, 
            formatted: 'Rp {{ number_format($cat->price, 0, ",", ".") }}', 
            desc: 'Pendaftaran Kategori {{ $cat->name }}' 
        },
        @endforeach
    };

    // Update summary column based on selected category
    function updateSummary() {
        const selected = categorySelect.value;
        const firstKey = Object.keys(categories)[0];
        const info = categories[selected] || categories[firstKey];
        
        if (info) {
            summaryCategory.textContent = info.name;
            summaryPrice.textContent = info.formatted;
            summaryDesc.textContent = info.desc;
            summaryTotal.textContent = info.formatted;
        }
    }

    // Trigger update on load and change
    categorySelect.addEventListener('change', updateSummary);
    updateSummary();

    // Client-side validate and transition to payment
    btnNext.addEventListener('click', function() {
        // Find all input fields in the personal details section
        const inputs = [
            regForm.querySelector('[name="fullname"]'),
            regForm.querySelector('[name="email"]'),
            regForm.querySelector('[name="phone"]'),
            regForm.querySelector('[name="dob"]'),
            regForm.querySelector('[name="gender"]'),
            regForm.querySelector('[name="jersey_size"]'),
            regForm.querySelector('[name="emergency_contact"]'),
            regForm.querySelector('[name="address"]'),
            regForm.querySelector('[name="nik"]'),
            regForm.querySelector('[name="city"]')
        ];

        // Check if all fields are valid
        let allValid = true;
        for (const input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                allValid = false;
                break;
            }
        }

        if (allValid) {
            // Show payment section
            paymentSection.classList.remove('hidden');
            
            // Hide "Lanjutkan" button
            btnNextContainer.classList.add('hidden');
            
            // Update step indicator
            stepIndicator2.classList.remove('text-gray-500');
            stepIndicator2.classList.add('text-gray-300', 'font-medium');
            stepIcon2.textContent = '✓';
            stepIcon2.classList.remove('bg-slate-800');
            stepIcon2.classList.add('bg-amber-500', 'text-slate-900');

            // Scroll down to payment section smoothly
            paymentSection.scrollIntoView({ behavior: 'smooth' });
        }
    });

    // Enforce receipt upload file validation: enable submit button only when file is selected
    proofInput.addEventListener('change', function() {
        if (proofInput.files.length > 0) {
            // Enable button
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-slate-600', 'text-slate-800');
            btnSubmit.classList.add('bg-gradient-to-r', 'from-amber-500', 'to-orange-600', 'text-slate-950', 'hover:shadow-[0_0_20px_rgba(245,166,35,0.4)]', 'hover:-translate-y-0.5');
            btnSubmit.style.background = 'linear-gradient(135deg, #F5A623, #d48f1a)';
            btnSubmit.style.color = '#0a0425';
        } else {
            // Disable button
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed', 'bg-slate-600', 'text-slate-800');
            btnSubmit.classList.remove('bg-gradient-to-r', 'from-amber-500', 'to-orange-600', 'text-slate-950', 'hover:shadow-[0_0_20px_rgba(245,166,35,0.4)]', 'hover:-translate-y-0.5');
            btnSubmit.style.background = '';
            btnSubmit.style.color = '';
        }
    });
});
</script>
@endsection
