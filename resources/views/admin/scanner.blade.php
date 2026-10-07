@extends('admin.layouts.admin')

@section('header_title', 'QR Code Scanner')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Check-in Scanner E-Ticket
                </h2>
                <p class="text-slate-500 text-sm mt-1">Scan kode QR tiket peserta atau unggah foto untuk verifikasi otomatis.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span id="scanner-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    Kamera Siap
                </span>
            </div>
        </div>

        <!-- Insecure context (HTTP) Notice Banner (Only shown if getUserMedia is blocked) -->
        <div id="http-notice" class="hidden mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="space-y-1">
                    <div class="font-semibold text-amber-900">Perhatian Browser Mobile / Non-HTTPS</div>
                    <div class="text-xs sm:text-sm text-amber-700 leading-relaxed">
                        Jika browser mobile membatasi izin kamera langsung karena koneksi HTTP (bukan HTTPS), gunakan tombol <strong class="text-amber-900">"Foto / Unggah QR"</strong> di bawah untuk membuka kamera bawaan ponsel secara instan.
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner Viewport Area -->
        <div class="mt-6">
            <div class="relative mx-auto w-full max-w-md bg-slate-950 rounded-2xl overflow-hidden shadow-lg border border-slate-800">
                <!-- Video Container -->
                <div id="reader" class="w-full bg-slate-950 min-h-[320px] flex items-center justify-center relative overflow-hidden">
                    <!-- Placeholder when camera is off -->
                    <div id="camera-placeholder" class="py-12 px-6 flex flex-col items-center justify-center text-center text-slate-400">
                        <div class="w-20 h-20 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center mb-4 text-slate-500 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-200 text-base mb-1">Kamera Belum Aktif</p>
                        <p class="text-xs text-slate-400 max-w-xs mb-5">Pilih kamera lalu tekan tombol di bawah untuk mulai memindai QR code.</p>
                        <button type="button" id="btn-start-camera" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Mulai Kamera
                        </button>
                    </div>
                </div>

                <!-- Scanning HUD Overlay (Shown when camera is active) -->
                <div id="scanner-hud" class="hidden absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-6">
                    <div class="relative w-64 h-64 border-2 border-blue-500/40 rounded-2xl flex items-center justify-center">
                        <!-- Corner brackets -->
                        <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-blue-500 rounded-tl-lg"></div>
                        <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-blue-500 rounded-tr-lg"></div>
                        <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-blue-500 rounded-bl-lg"></div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-blue-500 rounded-br-lg"></div>
                        <!-- Laser line animation -->
                        <div class="scanner-laser absolute left-2 right-2 h-0.5 bg-blue-400 shadow-[0_0_8px_#38bdf8]"></div>
                    </div>
                    <span class="mt-4 px-3 py-1 rounded-full text-[11px] font-medium tracking-wide uppercase bg-slate-900/80 text-blue-300 backdrop-blur-xs border border-blue-500/30">
                        Arahkan ke QR Code Tiket
                    </span>
                </div>
            </div>

            <!-- Camera Controls & Device Picker -->
            <div class="mt-4 max-w-md mx-auto space-y-3">
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <div class="relative flex-1 w-full">
                        <select id="camera-select" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none pr-8">
                            <option value="environment" selected>📷 Kamera Belakang (Utama)</option>
                            <option value="user">🤳 Kamera Depan (Selfie)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" id="btn-switch-camera" title="Balik Kamera Depan / Belakang" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 transition-all">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span id="btn-switch-label">Balik (🔄)</span>
                        </button>
                        <button type="button" id="btn-toggle-camera" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="btn-toggle-label">Nyalakan Kamera</span>
                        </button>
                    </div>
                </div>

                <!-- Secondary Option: Take Photo / File Upload (Guaranteed fallback on HTTP/iOS/Android) -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 bg-blue-50/70 border border-blue-100 rounded-xl p-3">
                    <div class="flex items-center gap-2 text-blue-900 text-xs sm:text-sm">
                        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Alternatif: Foto / Scan dari Galeri HP</span>
                    </div>
                    <div>
                        <input type="file" id="qr-file-input" accept="image/*" capture="environment" class="hidden">
                        <button type="button" id="btn-trigger-file" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-xs transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            </svg>
                            Ambil Foto / Pilih File
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Code Entry Fallback -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Input Manual Kode Tiket
            </h4>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" id="manual-qr" placeholder="Ketik kode tiket (contoh: ST-1-5K-NR-0001)..." class="flex-1 min-w-0 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm uppercase font-mono tracking-wide focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                <button type="button" id="manual-submit" class="shrink-0 inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-black text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Verifikasi Tiket
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Check-ins Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat Check-in Terbaru
            </h3>
            <span class="text-xs text-slate-400 font-medium">Auto-update langsung di halaman ini</span>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-xs uppercase tracking-wider text-slate-400 font-semibold bg-slate-50/50">
                        <th class="py-3 px-4 rounded-l-lg">Peserta</th>
                        <th class="py-3 px-4">Kode Tiket</th>
                        <th class="py-3 px-4">Kategori / BIB</th>
                        <th class="py-3 px-4">Event</th>
                        <th class="py-3 px-4 text-right rounded-r-lg">Waktu</th>
                    </tr>
                </thead>
                <tbody id="recent-checkins-body" class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($recentCheckins ?? [] as $recent)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-4 font-semibold text-slate-900">
                            {{ $recent->participant ? $recent->participant->displayName() : 'Peserta' }}
                        </td>
                        <td class="py-3 px-4 font-mono text-xs text-blue-600">
                            {{ $recent->ticket_code }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600">
                            {{ $recent->ticket_category ?? 'Tiket Masuk' }}
                            @if($recent->bib_number)
                                <span class="ml-1 inline-block px-1.5 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-800">BIB: {{ $recent->bib_number }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-500 max-w-[180px] truncate">
                            {{ $recent->participant && $recent->participant->event ? $recent->participant->event->title : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right text-xs text-slate-400">
                            {{ $recent->updated_at ? $recent->updated_at->format('H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-checkins-row">
                        <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                            Belum ada riwayat check-in pada sesi ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const readerDivId = "reader";
    const cameraSelect = document.getElementById('camera-select');
    const btnToggle = document.getElementById('btn-toggle-camera');
    const btnToggleLabel = document.getElementById('btn-toggle-label');
    const btnSwitchCamera = document.getElementById('btn-switch-camera');
    const btnStartCamera = document.getElementById('btn-start-camera');
    const placeholder = document.getElementById('camera-placeholder');
    const scannerHud = document.getElementById('scanner-hud');
    const statusBadge = document.getElementById('scanner-status-badge');
    const httpNotice = document.getElementById('http-notice');
    const fileInput = document.getElementById('qr-file-input');
    const btnTriggerFile = document.getElementById('btn-trigger-file');
    const manualInput = document.getElementById('manual-qr');
    const manualSubmit = document.getElementById('manual-submit');
    const recentBody = document.getElementById('recent-checkins-body');

    let html5QrCode = null;
    let isScanning = false;
    let isProcessing = false;
    let isSwitchingCamera = false;
    let audioCtx = null;

    // Check secure context
    if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
        if (httpNotice) httpNotice.classList.remove('hidden');
    }

    // Audio Feedback using Web Audio API (Zero external audio files needed)
    function playBeep(success = true) {
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            if (success) {
                // Happy double beep (D5 -> A5)
                const now = audioCtx.currentTime;
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0.2, now);
                gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.12);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.12);

                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.00, now + 0.14);
                gain2.gain.setValueAtTime(0.25, now + 0.14);
                gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.32);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.14);
                osc2.stop(now + 0.32);
            } else {
                // Low buzz tone (200Hz)
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(200, now);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.35);
            }
        } catch (e) {
            console.log('Audio feedback skipped:', e);
        }
    }

    function addRecentCheckinRow(data) {
        const emptyRow = document.getElementById('empty-checkins-row');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-blue-50/50 transition-colors bg-emerald-50/40 animate-pulse';
        tr.innerHTML = `
            <td class="py-3 px-4 font-semibold text-slate-900">${data.participant || 'Peserta'}</td>
            <td class="py-3 px-4 font-mono text-xs text-blue-600">${data.ticket_code || '-'}</td>
            <td class="py-3 px-4 text-xs text-slate-600">${data.category || 'Tiket Masuk'}</td>
            <td class="py-3 px-4 text-xs text-slate-500 max-w-[180px] truncate">${data.event || '-'}</td>
            <td class="py-3 px-4 text-right text-xs text-emerald-700 font-semibold">${data.checked_in_at || 'Baru Saja'}</td>
        `;

        recentBody.insertBefore(tr, recentBody.firstChild);
        setTimeout(() => {
            tr.classList.remove('bg-emerald-50/40', 'animate-pulse');
        }, 1500);
    }

    // Process scanned ticket code via backend API
    function processQR(qrCodeMessage) {
        if (isProcessing) return;
        isProcessing = true;

        // Visual feedback
        statusBadge.innerHTML = `
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            Memverifikasi...
        `;
        statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200';

        fetch('{{ route("admin.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_code: qrCodeMessage })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                playBeep(true);
                addRecentCheckinRow(data);

                Swal.fire({
                    icon: 'success',
                    title: 'Check-in Berhasil!',
                    html: `
                        <div class="space-y-2 mt-2">
                            <div class="text-xl font-extrabold text-slate-900">${data.participant}</div>
                            <div class="inline-block px-3 py-1 bg-blue-50 text-blue-700 font-mono text-xs font-bold rounded-lg border border-blue-200">${data.ticket_code}</div>
                            <div class="text-sm font-medium text-slate-600">${data.category}</div>
                            <div class="text-xs text-slate-400">${data.event}</div>
                        </div>
                    `,
                    showConfirmButton: false,
                    timer: 2300,
                    timerProgressBar: true,
                    backdrop: `rgba(15, 23, 42, 0.5)`
                });
            } else {
                playBeep(false);
                Swal.fire({
                    icon: 'error',
                    title: 'Verifikasi Gagal!',
                    html: `
                        <div class="space-y-2 mt-2">
                            <div class="text-sm text-rose-700 font-medium">${data.message}</div>
                            ${data.participant ? `<div class="text-xs text-slate-500 font-medium">Atas Nama: <strong class="text-slate-700">${data.participant}</strong></div>` : ''}
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#0f172a'
                });
            }
        })
        .catch(error => {
            console.error('Scan API Error:', error);
            playBeep(false);
            Swal.fire({
                icon: 'warning',
                title: 'Gangguan Koneksi',
                text: 'Gagal menghubungi server. Silakan coba lagi.',
                confirmButtonColor: '#0f172a'
            });
        })
        .finally(() => {
            setTimeout(() => {
                isProcessing = false;
                if (isScanning) {
                    statusBadge.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Scanning Aktif
                    `;
                    statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200';
                } else {
                    statusBadge.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        Kamera Siap
                    `;
                    statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200';
                }
            }, 2400);
        });
    }

    // Helper to ensure html5-qrcode library is ready (supports dynamic load fallback from CDN)
    function ensureScannerLibrary() {
        if (typeof Html5Qrcode !== 'undefined') {
            return Promise.resolve(true);
        }

        return new Promise((resolve) => {
            const cdns = [
                'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js'
            ];
            let idx = 0;

            function tryLoad() {
                if (idx >= cdns.length) {
                    resolve(typeof Html5Qrcode !== 'undefined');
                    return;
                }
                const script = document.createElement('script');
                script.src = cdns[idx++];
                script.onload = () => {
                    resolve(typeof Html5Qrcode !== 'undefined');
                };
                script.onerror = () => tryLoad();
                document.head.appendChild(script);
            }

            tryLoad();
        });
    }

    // Explicitly release any active video streams to prevent hardware lock (NotReadableError)
    function cleanupVideoTracks() {
        try {
            const readerElem = document.getElementById(readerDivId);
            if (readerElem) {
                const videos = readerElem.querySelectorAll('video');
                videos.forEach(v => {
                    if (v.srcObject && v.srcObject.getTracks) {
                        v.srcObject.getTracks().forEach(track => {
                            try { track.stop(); } catch (e) {}
                        });
                    }
                    v.srcObject = null;
                });
            }
        } catch (e) {
            console.warn('cleanupVideoTracks error:', e);
        }
    }

    // Populate camera select
    async function loadCameras() {
        const isLoaded = await ensureScannerLibrary();
        if (!isLoaded) {
            console.warn('Html5Qrcode library not loaded.');
            return;
        }

        try {
            const devices = await Html5Qrcode.getCameras();
            const currentVal = cameraSelect.value || 'environment';

            let html = `
                <option value="environment" ${currentVal === 'environment' ? 'selected' : ''}>📷 Kamera Belakang (Utama)</option>
                <option value="user" ${currentVal === 'user' ? 'selected' : ''}>🤳 Kamera Depan (Selfie)</option>
            `;

            if (devices && devices.length > 0) {
                const labeledDevices = devices.filter(d => d.label && d.label.trim() !== '');
                if (labeledDevices.length > 0) {
                    html += `<optgroup label="Pilihan Spesifik Perangkat">`;
                    labeledDevices.forEach((dev) => {
                        const isSel = currentVal === dev.id ? 'selected' : '';
                        html += `<option value="${dev.id}" ${isSel}>${dev.label}</option>`;
                    });
                    html += `</optgroup>`;
                }
            }

            cameraSelect.innerHTML = html;
        } catch (err) {
            console.warn('Unable to enumerate cameras:', err);
        }
    }

    // Initialize Html5Qrcode instance
    function initScannerInstance() {
        if (typeof Html5Qrcode === 'undefined') {
            throw new Error('Html5Qrcode library not loaded.');
        }
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode(readerDivId, {
                verbose: false,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            });
        }
        return html5QrCode;
    }

    // Stop video scanning cleanly
    async function stopCamera() {
        if (!html5QrCode) return;
        if (!isScanning) return;

        try {
            await html5QrCode.stop();
        } catch (err) {
            console.warn('html5QrCode.stop() error:', err);
        } finally {
            isScanning = false;
            cleanupVideoTracks();

            btnToggleLabel.textContent = 'Nyalakan Kamera';
            btnToggle.classList.remove('bg-rose-600', 'hover:bg-rose-700');
            btnToggle.classList.add('bg-slate-800', 'hover:bg-slate-900');

            if (scannerHud) scannerHud.classList.add('hidden');
            if (placeholder) placeholder.classList.remove('hidden');

            statusBadge.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                Kamera Nonaktif
            `;
            statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200';
        }

        // Hardware cool-off buffer so mobile camera hardware releases the lock
        await new Promise(r => setTimeout(r, 350));
    }

    // Start video scanning
    async function startCamera() {
        if (isScanning) {
            await stopCamera();
        }

        statusBadge.innerHTML = `
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            Menyiapkan Kamera...
        `;
        statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200';

        const isLoaded = await ensureScannerLibrary();
        if (!isLoaded) {
            statusBadge.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                Library Gagal Dimuat
            `;
            statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';

            Swal.fire({
                icon: 'error',
                title: 'Library Kamera Gagal Dimuat',
                text: 'Browser tidak dapat memuat modul pemindai QR. Periksa koneksi internet atau gunakan opsi "Foto / Unggah QR".',
                confirmButtonText: 'Gunakan Unggah Foto',
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#2563eb'
            }).then((res) => {
                if (res.isConfirmed) {
                    fileInput.click();
                }
            });
            return;
        }

        cleanupVideoTracks();

        try {
            initScannerInstance();
        } catch (err) {
            console.error('Scanner init error:', err);
            return;
        }

        const selectedVal = cameraSelect.value || "environment";
        let primaryConfig;
        if (selectedVal === "environment" || selectedVal === "user") {
            primaryConfig = { facingMode: selectedVal };
        } else {
            primaryConfig = selectedVal; // specific deviceId
        }

        // Clean qrConfig without forced aspectRatio to prevent NotReadableError on mobile
        const qrConfig = {
            fps: 15,
            qrbox: function(viewfinderWidth, viewfinderHeight) {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const qrEdge = Math.max(Math.floor(minEdge * 0.72), 150);
                return { width: qrEdge, height: qrEdge };
            }
        };

        if (placeholder) placeholder.classList.add('hidden');
        if (scannerHud) scannerHud.classList.remove('hidden');

        statusBadge.innerHTML = `
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Menghubungkan Kamera...
        `;

        function handleScanSuccess(decodedText) {
            processQR(decodedText);
        }

        function handleScanError() {
            // Ignored - frame parsing error
        }

        function onStarted() {
            isScanning = true;
            btnToggleLabel.textContent = 'Matikan Kamera';
            btnToggle.classList.remove('bg-slate-800', 'hover:bg-slate-900');
            btnToggle.classList.add('bg-rose-600', 'hover:bg-rose-700');

            statusBadge.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Scanning Aktif
            `;
            statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200';

            // Refresh camera dropdown with permission-unlocked labels
            loadCameras();
        }

        async function tryStart(cfg) {
            return html5QrCode.start(cfg, qrConfig, handleScanSuccess, handleScanError);
        }

        try {
            // Attempt 1: Target camera configuration
            await tryStart(primaryConfig);
            onStarted();
            return;
        } catch (err1) {
            console.warn('Initial camera start failed:', err1);
            cleanupVideoTracks();
            await new Promise(r => setTimeout(r, 250));

            // Attempt 2: Fallback to opposite facingMode
            try {
                const altMode = (selectedVal === 'user') ? 'environment' : 'user';
                await tryStart({ facingMode: altMode });
                cameraSelect.value = altMode;
                onStarted();
                return;
            } catch (err2) {
                console.warn('Alternative facingMode failed:', err2);
                cleanupVideoTracks();
                await new Promise(r => setTimeout(r, 250));

                // Attempt 3: Fallback without facingMode constraint
                try {
                    await tryStart({ facingMode: "environment" });
                    onStarted();
                    return;
                } catch (err3) {
                    console.error('All camera start attempts failed:', err3);
                }
            }
        }

        // All attempts failed
        isScanning = false;
        cleanupVideoTracks();
        if (placeholder) placeholder.classList.remove('hidden');
        if (scannerHud) scannerHud.classList.add('hidden');

        statusBadge.innerHTML = `
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            Kamera Gagal
        `;
        statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';

        let msg = 'Tidak dapat mengakses kamera. Pastikan browser diberikan izin kamera di setelan ponsel dan tidak ada aplikasi lain yang sedang menggunakan kamera.';
        if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            msg = 'Browser memblokir kamera langsung pada koneksi HTTP (bukan HTTPS). Gunakan tombol "Foto / Unggah QR Tiket" atau aktifkan HTTPS di server.';
        }

        Swal.fire({
            icon: 'warning',
            title: 'Kamera Tidak Dapat Dibuka',
            text: msg,
            confirmButtonText: 'Gunakan Unggah Foto',
            showCancelButton: true,
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#2563eb'
        }).then((res) => {
            if (res.isConfirmed) {
                fileInput.click();
            }
        });
    }

    // Toggle button handler
    btnToggle.addEventListener('click', async () => {
        if (isSwitchingCamera) return;
        if (isScanning) {
            await stopCamera();
        } else {
            await startCamera();
        }
    });

    if (btnStartCamera) {
        btnStartCamera.addEventListener('click', async () => {
            if (isSwitchingCamera) return;
            await startCamera();
        });
    }

    // Flip Camera button handler (Front <-> Back toggle)
    if (btnSwitchCamera) {
        btnSwitchCamera.addEventListener('click', async () => {
            if (isSwitchingCamera) return;
            isSwitchingCamera = true;
            btnSwitchCamera.disabled = true;
            btnToggle.disabled = true;

            const nextMode = (cameraSelect.value === 'user') ? 'environment' : 'user';
            cameraSelect.value = nextMode;

            if (isScanning) {
                statusBadge.innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Beralih Kamera...
                `;
                statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200';

                try {
                    await stopCamera();
                    await startCamera();
                } catch (e) {
                    console.error('Flip camera error:', e);
                } finally {
                    isSwitchingCamera = false;
                    btnSwitchCamera.disabled = false;
                    btnToggle.disabled = false;
                }
            } else {
                isSwitchingCamera = false;
                btnSwitchCamera.disabled = false;
                btnToggle.disabled = false;
            }
        });
    }

    // Change camera dropdown
    cameraSelect.addEventListener('change', async () => {
        if (isSwitchingCamera) return;
        if (isScanning) {
            isSwitchingCamera = true;
            btnToggle.disabled = true;
            if (btnSwitchCamera) btnSwitchCamera.disabled = true;

            statusBadge.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Mengganti Kamera...
            `;
            statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200';

            try {
                await stopCamera();
                await startCamera();
            } catch (e) {
                console.error('Change camera error:', e);
            } finally {
                isSwitchingCamera = false;
                btnToggle.disabled = false;
                if (btnSwitchCamera) btnSwitchCamera.disabled = false;
            }
        }
    });

    // File / Photo upload scanner fallback
    btnTriggerFile.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const isLoaded = await ensureScannerLibrary();
        if (!isLoaded) {
            Swal.fire({
                icon: 'error',
                title: 'Library Kamera Gagal Dimuat',
                text: 'Modul pemindai gagal dimuat. Periksa koneksi internet.',
                confirmButtonColor: '#2563eb'
            });
            fileInput.value = '';
            return;
        }

        try {
            initScannerInstance();
        } catch (err) {
            console.error('Init error:', err);
            fileInput.value = '';
            return;
        }

        Swal.fire({
            title: 'Membaca Foto QR...',
            text: 'Harap tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                Swal.close();
                processQR(decodedText);
            })
            .catch(err => {
                console.error('File scan error:', err);
                playBeep(false);
                Swal.fire({
                    icon: 'error',
                    title: 'QR Code Tidak Terbaca',
                    text: 'Pastikan gambar QR code fokus, terang, dan tidak buram.',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#0f172a'
                });
            })
            .finally(() => {
                fileInput.value = '';
            });
    });

    // Manual input submission
    function handleManual() {
        const val = manualInput.value.trim();
        if (val) {
            processQR(val);
            manualInput.value = '';
        }
    }

    manualSubmit.addEventListener('click', handleManual);
    manualInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleManual();
        }
    });

    // Load camera list on boot
    loadCameras();
});
</script>

<style>
/* Smooth scanning laser animation */
@keyframes scanLaser {
    0% {
        top: 8px;
        opacity: 0.8;
    }
    50% {
        top: calc(100% - 10px);
        opacity: 1;
    }
    100% {
        top: 8px;
        opacity: 0.8;
    }
}

.scanner-laser {
    animation: scanLaser 2.2s ease-in-out infinite;
}

/* Ensure video element inside reader fills responsive area without distorted styling */
#reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    border-radius: 1rem !important;
}

#reader {
    border: none !important;
}
</style>
@endsection
