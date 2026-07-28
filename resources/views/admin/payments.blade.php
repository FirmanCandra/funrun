@extends('admin.layouts.admin')

@section('header_title', 'Payment Verification')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-lg text-slate-800">Pending & Verified Payments</h3>
            <p class="text-xs text-slate-500 mt-1">Verify uploaded payment proofs and validate tickets.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter & Search Form -->
            <form action="{{ route('admin.payments') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, WA, ticket..." class="border border-slate-200 rounded-lg pl-9 pr-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-52 md:w-60">
                    <div class="absolute left-3 top-2.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <select name="status" onchange="this.form.submit()" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $currentStatus == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="waiting_verification" {{ $currentStatus == 'waiting_verification' ? 'selected' : '' }}>Pending (Waiting)</option>
                    <option value="paid" {{ $currentStatus == 'paid' ? 'selected' : '' }}>Approved (Paid)</option>
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors">
                    Search
                </button>

                @if(request('search') || $currentStatus !== 'all')
                    <a href="{{ route('admin.payments') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium px-1">Reset</a>
                @endif
            </form>

            <button type="button" id="bulk-delete-btn" onclick="confirmBulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap" style="display: none;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Delete Selected (<span id="selected-count">0</span>)
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4 w-10">
                        <input type="checkbox" id="select-all" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Participant</th>
                    <th class="px-6 py-4">Ticket Code</th>
                    <th class="px-6 py-4">Amount & Method</th>
                    <th class="px-6 py-4 text-center">Proof of Payment</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($payments as $payment)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <input type="checkbox" name="ids[]" value="{{ $payment->id }}" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-4 text-center text-slate-500 font-medium">
                        {{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $payment->ticket->participant->fullname ?? '-' }}</div>
                        <div class="text-xs text-slate-500">{{ $payment->ticket->participant->phone ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs font-semibold">
                        {{ $payment->ticket->ticket_code ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        <div class="text-xs text-slate-500 uppercase">{{ $payment->payment_method ?? 'Unknown' }}</div>
                        @if($payment->payment_status === 'paid')
                            <span class="inline-block mt-1 bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">Verified</span>
                        @elseif($payment->payment_status === 'waiting_verification')
                            <span class="inline-block mt-1 bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded">Pending Verification</span>
                        @else
                            <span class="inline-block mt-1 bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded">{{ $payment->payment_status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($payment->proof_of_payment)
                            <button type="button" onclick="openLightbox('{{ asset('storage/' . $payment->proof_of_payment) }}')" class="text-blue-600 hover:text-blue-800 underline text-xs font-medium focus:outline-none">
                                View Image
                            </button>
                        @else
                            <span class="text-slate-400 text-xs">No proof uploaded</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($payment->payment_status === 'waiting_verification')
                            <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to approve this payment and validate the ticket?');">
                                @csrf
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium transition-colors">
                                    Verify & Approve
                                </button>
                            </form>
                        @elseif($payment->payment_status === 'paid')
                            <div class="flex flex-col gap-1.5 justify-center items-center">
                                <button disabled class="bg-slate-100 text-slate-400 px-4 py-1.5 rounded-lg text-xs font-medium cursor-not-allowed w-full">
                                    Approved
                                </button>
                                <a href="{{ route('admin.eticket.pdf', $payment->ticket->ticket_code) }}" target="_blank" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-1.5 rounded-lg text-xs font-medium transition-colors w-full flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download E-Ticket PDF
                                </a>
                                @php
                                    $waNumber = $payment->ticket && $payment->ticket->participant ? preg_replace('/^0/', '62', $payment->ticket->participant->phone) : '';
                                    
                                    $rawWaText = "";
                                    if ($payment->ticket && $payment->ticket->participant) {
                                        $p = $payment->ticket->participant;
                                        $t = $payment->ticket;
                                        
                                        $categoryModel = \App\Models\EventCategory::where('event_id', $p->event_id)
                                            ->where('code', $p->category)
                                            ->first();
                                        $ticketTitle = $categoryModel ? $categoryModel->name : $p->category;

                                        $rawWaText = "*PEMBAYARAN BERHASIL*\n" .
                                                     "*SeTiket*\n\n" .
                                                     "Halo *{$p->fullname}*,\n\n" .
                                                     "Pembayaran pendaftaran Anda untuk event *SeTiket* telah berhasil diverifikasi!\n\n" .
                                                     "*Detail Peserta:*\n" .
                                                     "• Nama Lengkap: *{$p->fullname}*\n" .
                                                     "• No. WhatsApp: *{$p->phone}*\n" .
                                                     "• Kode Tiket: *{$t->ticket_code}*\n" .
                                                     "• Kategori: *{$ticketTitle}*\n" .
                                                     "• Ukuran Jersey: *{$p->jersey_size}*\n\n" .
                                                     "*Download PDF Resmi E-Ticket:*\n" . route('ticket.pdf', $t->ticket_code) . "\n\n" .
                                                     "*Catatan:*\n" .
                                                     "Silakan simpan link di atas atau unduh PDF tiket Anda. Tunjukkan QR Code pada tiket saat melakukan check-in di lokasi acara untuk pengambilan Race Pack & BIB.\n\n" .
                                                     "Terima kasih atas partisipasi Anda, sampai jumpa di garis start!";
                                    }
                                    $waText = urlencode($rawWaText);
                                @endphp
                                @if($waNumber)
                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-medium transition-colors w-full flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                    WA Manual
                                </a>
                                @endif
                            </div>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
                
                @if($payments->isEmpty())
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">No payment records found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
        {{ $payments->links() }}
    </div>
    @endif
</div>

<!-- Bulk Delete Form -->
<form id="bulk-delete-form" action="{{ route('admin.payments.bulk-delete') }}" method="POST" class="hidden">
    @csrf
</form>

<!-- Lightbox Modal -->
<div id="lightbox-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300 opacity-0" onclick="closeLightbox()">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-4 relative flex flex-col items-center" onclick="event.stopPropagation()">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <h4 class="font-bold text-slate-800 self-start mb-2 px-1 text-lg">Proof of Payment</h4>
        <div class="w-full flex justify-center bg-slate-50 rounded-xl overflow-hidden max-h-[70vh]">
            <img id="lightbox-img" src="" alt="Proof of Payment" class="object-contain max-h-[60vh] max-w-full">
        </div>
        <div class="mt-4 flex gap-3 w-full justify-end">
            <a id="lightbox-download" href="" download class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Image
            </a>
            <button onclick="closeLightbox()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkDeleteBtn.style.display = 'inline-flex';
            selectedCountSpan.textContent = checkedCount;
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButton();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
            const someChecked = Array.from(rowCheckboxes).some(c => c.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
            updateBulkDeleteButton();
        });
    });

    function confirmBulkDelete() {
        const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedCheckboxes.length === 0) return;

        if (confirm(`Are you sure you want to delete ${checkedCheckboxes.length} selected payment records? This will reset their associated ticket status to pending!`)) {
            // Clear previous inputs
            bulkDeleteForm.innerHTML = `@csrf`;
            
            // Append checked IDs
            checkedCheckboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                bulkDeleteForm.appendChild(input);
            });

            bulkDeleteForm.submit();
        }
    }

    function openLightbox(imgUrl) {
        const modal = document.getElementById('lightbox-modal');
        const img = document.getElementById('lightbox-img');
        const downloadLink = document.getElementById('lightbox-download');
        
        img.src = imgUrl;
        downloadLink.href = imgUrl;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
        }, 50);
    }

    function closeLightbox() {
        const modal = document.getElementById('lightbox-modal');
        modal.classList.remove('opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection
