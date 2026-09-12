@extends('admin.layouts.admin')

@section('header_title', 'QR Code Scanner')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 sm:p-8 text-center">
        <h2 class="text-xl sm:text-2xl font-bold mb-2">Check-in Scanner</h2>
        <p class="text-slate-500 text-sm sm:text-base mb-6 sm:mb-8">Scan participant's E-Ticket QR Code to verify and check-in.</p>

        {{-- Peringatan HTTPS — kamera memerlukan secure context --}}
        <div id="https-warning" class="hidden mb-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 text-sm text-left">
            <strong>⚠️ Peringatan:</strong> Kamera memerlukan koneksi HTTPS. Halaman ini diakses via HTTP sehingga fitur kamera mungkin tidak tersedia. Gunakan <strong>Manual Entry</strong> di bawah sebagai alternatif.
        </div>

        <div id="reader-container" class="mx-auto w-full max-w-md overflow-hidden rounded-2xl border-4 border-slate-100 shadow-inner bg-slate-50 relative aspect-square flex items-center justify-center">
            <div id="reader" class="w-full h-full"></div>
            {{-- Overlay sebelum kamera diaktifkan --}}
            <div id="start-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10">
                <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <button id="start-scanner" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Start Camera
                </button>
                <p id="camera-error" class="hidden mt-3 text-red-500 text-sm max-w-xs"></p>
            </div>
        </div>

        {{-- Tombol stop kamera — muncul saat kamera aktif --}}
        <button id="stop-scanner" class="hidden mt-4 bg-red-50 hover:bg-red-100 text-red-600 px-5 py-2 rounded-lg font-medium text-sm transition-colors">
            Stop Camera
        </button>

        <div class="mt-8 text-left">
            <h4 class="font-bold text-sm text-slate-500 uppercase tracking-wider mb-2 border-b pb-2">Manual Entry (Fallback)</h4>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" id="manual-qr" placeholder="Masukkan Kode Tiket (contoh: ST-1-5K-NR-0001)..." class="flex-1 min-w-0 border border-slate-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                <button id="manual-submit" class="shrink-0 bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-lg font-medium transition-colors">Verify</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var scannerBtn   = document.getElementById('start-scanner');
    var overlay      = document.getElementById('start-overlay');
    var cameraError  = document.getElementById('camera-error');
    var stopBtn      = document.getElementById('stop-scanner');
    var manualInput  = document.getElementById('manual-qr');
    var manualSubmit = document.getElementById('manual-submit');
    var httpsWarning = document.getElementById('https-warning');

    // Menggunakan Html5Qrcode (low-level API) — bukan Html5QrcodeScanner.
    // Html5Qrcode menyediakan .pause(), .resume(), .start(), .stop() yang benar.
    var html5Qrcode = null;
    var isScanning  = false;
    var isProcessing = false; // lock supaya tidak kirim request berkali-kali

    // Periksa apakah halaman di-serve via HTTPS atau localhost
    var isSecure = location.protocol === 'https:' ||
                   location.hostname === 'localhost' ||
                   location.hostname === '127.0.0.1';
    if (!isSecure) {
        httpsWarning.classList.remove('hidden');
    }

    // === PROCESS QR (shared: camera + manual entry) ===
    function processQR(qrCodeMessage) {
        if (isProcessing) return;
        isProcessing = true;

        // Pause kamera jika sedang aktif
        if (html5Qrcode && isScanning) {
            try { html5Qrcode.pause(true); } catch(e) { /* ignore */ }
        }

        fetch('{{ route("admin.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_code: qrCodeMessage })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Check-in Berhasil!',
                    html: '<div class="text-lg font-bold text-slate-800">' + data.participant + '</div>' +
                          '<div class="text-sm text-slate-500">' + data.message + '</div>',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    backdrop: 'rgba(0,0,0,0.4)'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message,
                    showConfirmButton: true,
                    confirmButtonColor: '#ef4444'
                });
            }

            // Resume kamera setelah popup selesai
            setTimeout(function() {
                isProcessing = false;
                if (html5Qrcode && isScanning) {
                    try { html5Qrcode.resume(); } catch(e) { /* ignore */ }
                }
            }, 2500);
        })
        .catch(function(error) {
            console.error('Scan error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                confirmButtonColor: '#ef4444'
            });
            isProcessing = false;
            if (html5Qrcode && isScanning) {
                try { html5Qrcode.resume(); } catch(e) { /* ignore */ }
            }
        });
    }

    // === START CAMERA ===
    scannerBtn.addEventListener('click', function() {
        cameraError.classList.add('hidden');
        scannerBtn.disabled = true;
        scannerBtn.textContent = 'Memulai kamera...';

        html5Qrcode = new Html5Qrcode("reader");

        Html5Qrcode.getCameras().then(function(cameras) {
            if (!cameras || cameras.length === 0) {
                throw new Error('Tidak ada kamera yang ditemukan di perangkat ini.');
            }

            // Gunakan kamera belakang jika tersedia (lebih cocok untuk scan QR)
            var cameraId = cameras[0].id;
            for (var i = 0; i < cameras.length; i++) {
                var label = (cameras[i].label || '').toLowerCase();
                if (label.indexOf('back') !== -1 || label.indexOf('belakang') !== -1 ||
                    label.indexOf('rear') !== -1 || label.indexOf('environment') !== -1) {
                    cameraId = cameras[i].id;
                    break;
                }
            }

            // Hitung ukuran qrbox yang responsif
            var readerEl = document.getElementById('reader');
            var containerWidth = readerEl.clientWidth || 300;
            var qrboxSize = Math.min(250, Math.floor(containerWidth * 0.7));

            return html5Qrcode.start(
                cameraId,
                { fps: 10, qrbox: { width: qrboxSize, height: qrboxSize } },
                function(decodedText) {
                    processQR(decodedText);
                },
                function() {
                    // scan frame error — abaikan (normal saat belum mendeteksi QR)
                }
            );
        }).then(function() {
            // Kamera berhasil aktif
            isScanning = true;
            overlay.classList.add('hidden');
            stopBtn.classList.remove('hidden');
        }).catch(function(err) {
            // Gagal mengakses kamera
            console.error('Camera error:', err);
            scannerBtn.disabled = false;
            scannerBtn.textContent = 'Start Camera';

            var msg = 'Tidak dapat mengakses kamera.';
            var errStr = (err && err.message) ? err.message : String(err);

            if (errStr.indexOf('NotAllowedError') !== -1 || errStr.indexOf('Permission') !== -1) {
                msg = 'Izin kamera ditolak. Silakan izinkan akses kamera di pengaturan browser Anda, lalu refresh halaman.';
            } else if (errStr.indexOf('NotFoundError') !== -1 || errStr.indexOf('tidak ada kamera') !== -1) {
                msg = 'Tidak ada kamera yang ditemukan di perangkat ini. Gunakan Manual Entry di bawah.';
            } else if (errStr.indexOf('NotReadableError') !== -1 || errStr.indexOf('Could not start') !== -1) {
                msg = 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi lain yang menggunakan kamera, lalu coba lagi.';
            } else if (!isSecure) {
                msg = 'Kamera memerlukan koneksi HTTPS. Gunakan Manual Entry di bawah.';
            } else {
                msg = 'Gagal mengakses kamera: ' + errStr;
            }

            cameraError.textContent = msg;
            cameraError.classList.remove('hidden');
        });
    });

    // === STOP CAMERA ===
    stopBtn.addEventListener('click', function() {
        if (html5Qrcode && isScanning) {
            html5Qrcode.stop().then(function() {
                isScanning = false;
                stopBtn.classList.add('hidden');
                overlay.classList.remove('hidden');
                scannerBtn.disabled = false;
                scannerBtn.textContent = 'Start Camera';
                // Bersihkan sisa elemen video di dalam reader
                var reader = document.getElementById('reader');
                reader.innerHTML = '';
            }).catch(function(err) {
                console.error('Stop error:', err);
            });
        }
    });

    // === MANUAL ENTRY ===
    manualSubmit.addEventListener('click', function() {
        var val = manualInput.value.trim();
        if (val !== '') {
            processQR(val);
            manualInput.value = '';
        }
    });

    // Enter key pada input manual
    manualInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            manualSubmit.click();
        }
    });
});
</script>

<style>
    /* Sembunyikan branding html5-qrcode */
    #reader a[href*="scanapp"] { display: none !important; }
    #reader__header_message { display: none !important; }
    /* Video feed memenuhi container */
    #reader video { width: 100% !important; height: 100% !important; object-fit: cover; border-radius: 12px; }
    #reader { position: relative; }
</style>
@endsection
