@extends('admin.layouts.admin')

@section('header_title', 'QR Code Scanner')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
        <h2 class="text-2xl font-bold mb-2">Check-in Scanner</h2>
        <p class="text-slate-500 mb-8">Scan participant's E-Ticket QR Code to verify and check-in.</p>
        
        <div id="reader-container" class="mx-auto w-full max-w-md overflow-hidden rounded-2xl border-4 border-slate-100 shadow-inner bg-slate-50 relative aspect-square flex items-center justify-center">
            <div id="reader" class="w-full"></div>
            <!-- Temporary overlay before start -->
            <div id="start-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10">
                <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <button id="start-scanner" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Start Camera
                </button>
            </div>
        </div>
        
        <div id="result-alert" class="mt-8 hidden">
            <!-- Alert content will be injected here -->
        </div>

        <div class="mt-8 text-left">
            <h4 class="font-bold text-sm text-slate-500 uppercase tracking-wider mb-2 border-b pb-2">Manual Entry (Fallback)</h4>
            <div class="flex gap-2">
                <input type="text" id="manual-qr" placeholder="Enter Ticket Code (e.g. ST-3K-FR-0001)..." class="flex-1 border border-slate-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                <button id="manual-submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-lg font-medium transition-colors">Verify</button>
            </div>
        </div>
    </div>
</div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const scannerBtn = document.getElementById('start-scanner');
                const overlay = document.getElementById('start-overlay');
                const manualInput = document.getElementById('manual-qr');
                const manualSubmit = document.getElementById('manual-submit');
                
                let html5QrcodeScanner = null;

                function processQR(qrCodeMessage) {
                    // Stop scanning temporarily
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.pause();
                    }
                    
                    // Call API
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Check-in Berhasil!',
                                html: `<div class="text-lg font-bold text-slate-800">${data.participant}</div><div class="text-sm text-slate-500">${data.message}</div>`,
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true,
                                backdrop: `rgba(0,0,0,0.4)`
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
                        
                        // Resume scanning after the popup timer
                        setTimeout(() => {
                            if (html5QrcodeScanner) {
                                html5QrcodeScanner.resume();
                            }
                        }, 2500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (html5QrcodeScanner) html5QrcodeScanner.resume();
                    });
                }

        scannerBtn.addEventListener('click', () => {
            overlay.classList.add('hidden');
            
            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: 250 }
            );
            
            html5QrcodeScanner.render((decodedText, decodedResult) => {
                processQR(decodedText);
            }, (errorMessage) => {
                // parse error, ignore
            });
        });
        
        manualSubmit.addEventListener('click', () => {
            if (manualInput.value.trim() !== '') {
                processQR(manualInput.value.trim());
                manualInput.value = '';
            }
        });
    });
</script>
<style>
    /* Styling html5-qrcode elements */
    #reader button { background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; margin-bottom: 10px; }
    #reader select { padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; margin-bottom: 10px; }
    #reader a { display: none; }
    #reader__dashboard_section_csr span { display: none; }
</style>
@endsection
