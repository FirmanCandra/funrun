@extends('admin.layouts.admin')

@section('header_title', 'Verifikasi Pesanan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
        {{-- Baris 1: Judul + tombol aksi --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-lg text-slate-800">Pesanan Tiket</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Satu pesanan bisa berisi beberapa tiket dengan satu bukti transfer. Menyetujui pesanan akan
                    menerbitkan seluruh tiket di dalamnya sekaligus.
                </p>
            </div>

            <button type="button" id="bulk-delete-btn" onclick="confirmBulkDelete()"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap shrink-0"
                style="display: none;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus Terpilih (<span id="selected-count">0</span>)
            </button>
        </div>

        {{-- Baris 2: Filter & Search --}}
        <form action="{{ route('admin.orders') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="relative flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode, nama, NIK..."
                    class="border border-slate-200 rounded-lg pl-9 pr-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                @if(auth()->user()->role === 'super_admin' && $events->isNotEmpty())
                    <select name="event_id" onchange="this.form.submit()"
                        class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ $currentEventId == 'all' ? 'selected' : '' }}>Semua Event</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev->id }}" {{ $currentEventId == $ev->id ? 'selected' : '' }}>{{ $ev->title }}</option>
                        @endforeach
                    </select>
                @endif

                <select name="status" onchange="this.form.submit()"
                    class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $currentStatus == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="waiting_verification" {{ $currentStatus == 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="paid" {{ $currentStatus == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="rejected" {{ $currentStatus == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <select name="sort" onchange="this.form.submit()"
                    class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="newest" {{ $currentSort == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Terlama</option>
                </select>

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    Cari
                </button>

                @if(request('search') || $currentStatus !== 'all' || $currentSort !== 'newest' || (isset($currentEventId) && $currentEventId !== 'all'))
                    <a href="{{ route('admin.orders') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium whitespace-nowrap">Reset</a>
                @endif
            </div>
        </form>
    </div>
    </div>

    @if(session('success'))
        <div class="mx-6 mt-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mx-6 mt-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form id="bulk-delete-form" action="{{ route('admin.orders.bulk-delete') }}" method="POST">
        @csrf

        {{-- Digulir mendatar di layar sempit, dengan lebar minimum agar kolomnya tetap terbaca. --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[64rem] text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left w-10">
                            <input type="checkbox" id="select-all" class="rounded border-slate-300">
                        </th>
                        <th class="px-6 py-4 text-left">Pesanan</th>
                        <th class="px-6 py-4 text-left">Pembeli</th>
                        <th class="px-6 py-4 text-left">Peserta &amp; Tiket</th>
                        <th class="px-6 py-4 text-left">Total</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Bukti</th>
                        <th class="px-6 py-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $order)
                        <tr class="hover:bg-slate-50 align-top">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $order->id }}"
                                    class="row-checkbox rounded border-slate-300">
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-mono font-semibold text-slate-800">{{ $order->order_code }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                @if(auth()->user()->role === 'super_admin')
                                    <div class="text-xs text-slate-400 mt-1">{{ $order->event->title ?? '-' }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $order->user->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $order->user->email ?? '-' }}</div>
                                {{-- Rekening tujuan seperti yang dilihat pembeli saat memesan --}}
                                <div class="mt-2 text-xs">
                                    <div class="text-slate-600 font-medium">{{ $order->payment_method ?? '-' }}</div>
                                    @if($order->payment_account_number)
                                        <div class="font-mono text-slate-500">{{ $order->payment_account_number }}</div>
                                        <div class="text-slate-400">a.n. {{ $order->payment_account_holder }}</div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-xs font-semibold text-slate-600 mb-1">
                                    {{ $order->tickets_count }} tiket
                                </div>
                                <ul class="space-y-1">
                                    @foreach($order->tickets as $ticket)
                                        <li class="text-xs">
                                            <span class="text-slate-800">{{ $ticket->participant?->displayName() ?? '-' }}</span>
                                            <span class="text-slate-400">·</span>
                                            <span class="text-slate-500">{{ $ticket->participant->category ?? '-' }}</span>
                                            <div class="font-mono text-[11px] text-slate-400">{{ $ticket->ticket_code }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4">
                                @if($order->payment_status === \App\Models\Order::STATUS_PAID)
                                    <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded font-medium">Lunas</span>
                                @elseif($order->payment_status === \App\Models\Order::STATUS_REJECTED)
                                    <span class="inline-block bg-red-100 text-red-700 text-xs px-2 py-1 rounded font-medium">Ditolak</span>
                                    @if($order->rejection_reason)
                                        <div class="text-[11px] text-slate-500 mt-1 max-w-[10rem]">{{ $order->rejection_reason }}</div>
                                    @endif
                                @else
                                    <span class="inline-block bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded font-medium">Menunggu</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($order->proof_of_payment)
                                    <button type="button"
                                        onclick="openLightbox('{{ asset('storage/' . $order->proof_of_payment) }}')"
                                        class="text-blue-600 hover:text-blue-800 underline text-xs font-medium focus:outline-none">
                                        Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs">Belum ada</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2 min-w-[9rem]">
                                    @if($order->payment_status !== \App\Models\Order::STATUS_PAID)
                                        <button type="button"
                                            onclick="submitApprove({{ $order->id }}, '{{ $order->order_code }}', {{ $order->tickets_count }})"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                            Setujui &amp; Terbitkan
                                        </button>
                                        <button type="button"
                                            onclick="openReject({{ $order->id }}, '{{ $order->order_code }}')"
                                            class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                            Tolak
                                        </button>
                                    @else
                                        <span class="bg-slate-100 text-slate-500 px-4 py-1.5 rounded-lg text-xs font-medium text-center">
                                            Disetujui
                                        </span>
                                        @foreach($order->tickets as $ticket)
                                            <a href="{{ route('admin.eticket.pdf', $ticket->ticket_code) }}"
                                                class="text-blue-600 hover:text-blue-800 underline text-[11px] font-mono">
                                                PDF {{ $ticket->ticket_code }}
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($orders->isEmpty())
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada pesanan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </form>

    <div class="p-6 border-t border-slate-100">
        {{ $orders->links() }}
    </div>
</div>

{{-- Form tersembunyi untuk approve --}}
<form id="approve-form" method="POST" style="display:none;">@csrf</form>

{{-- Modal tolak --}}
<div id="reject-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <h3 class="font-bold text-lg text-slate-800 mb-1">Tolak Pesanan</h3>
        <p class="text-sm text-slate-500 mb-4">
            Pesanan <span id="reject-order-code" class="font-mono font-semibold"></span> akan ditolak.
            Pembeli dapat mengunggah ulang bukti pembayaran.
        </p>
        <form id="reject-form" method="POST">
            @csrf
            <label class="block text-sm font-medium text-slate-700 mb-2">Alasan penolakan *</label>
            <textarea name="rejection_reason" rows="3" required maxlength="500"
                placeholder="contoh: Nominal transfer tidak sesuai dengan total pesanan."
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="closeReject()"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Tolak Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Lightbox bukti --}}
<div id="lightbox" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center p-4"
    onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="Bukti pembayaran" class="max-h-[90vh] max-w-[90vw] w-auto h-auto object-contain rounded-lg shadow-2xl">
</div>

<script>
    const approveUrl = id => "{{ url('admin/orders') }}/" + id + "/approve";
    const rejectUrl = id => "{{ url('admin/orders') }}/" + id + "/reject";

    function submitApprove(id, code, count) {
        if (!confirm('Setujui pesanan ' + code + '? ' + count + ' tiket akan langsung diterbitkan.')) return;
        const form = document.getElementById('approve-form');
        form.action = approveUrl(id);
        form.submit();
    }

    function openReject(id, code) {
        document.getElementById('reject-order-code').textContent = code;
        document.getElementById('reject-form').action = rejectUrl(id);
        const modal = document.getElementById('reject-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeReject() {
        const modal = document.getElementById('reject-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        const box = document.getElementById('lightbox');
        box.classList.remove('hidden');
        box.classList.add('flex');
    }

    function closeLightbox() {
        const box = document.getElementById('lightbox');
        box.classList.add('hidden');
        box.classList.remove('flex');
    }

    // Pilih massal
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = () => Array.from(document.querySelectorAll('.row-checkbox'));

    function refreshBulkButton() {
        const count = rowCheckboxes().filter(c => c.checked).length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-delete-btn').style.display = count > 0 ? 'flex' : 'none';
    }

    selectAll?.addEventListener('change', function () {
        rowCheckboxes().forEach(c => { c.checked = selectAll.checked; });
        refreshBulkButton();
    });

    rowCheckboxes().forEach(c => c.addEventListener('change', refreshBulkButton));

    function confirmBulkDelete() {
        const count = rowCheckboxes().filter(c => c.checked).length;
        if (count === 0) return;
        if (confirm('Hapus ' + count + ' pesanan terpilih beserta peserta dan tiketnya? Tindakan ini tidak bisa dibatalkan.')) {
            document.getElementById('bulk-delete-form').submit();
        }
    }
</script>
@endsection
