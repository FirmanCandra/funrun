@extends('admin.layouts.admin')

@section('header_title', 'Manajemen Event')

@section('content')

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Header row --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-slate-500 text-sm">Total: <span class="font-bold text-slate-700">{{ $events->total() }}</span> event
        </p>
        <div class="flex items-center gap-3">
            <button id="btnBulkDelete" onclick="submitBulkDelete()"
                class="hidden bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14" />
                </svg>
                Tambah Event
            </button>
        </div>
    </div>

    {{-- Search form --}}
    <div class="mb-6 flex justify-between items-center bg-white p-4 rounded-2xl border border-slate-100">
        <form action="{{ route('admin.events') }}" method="GET" class="flex items-center gap-2 w-full max-w-md">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-slate-50/50">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.events') }}"
                    class="text-slate-500 hover:text-slate-700 text-sm font-medium px-2">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500 w-10">
                            <input type="checkbox" id="selectAll"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500">No</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500">Nama Event</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500">Lokasi</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500">Tanggal</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500">Harga</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500">Kategori</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($events as $i => $ev)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4 text-slate-400">
                                <input type="checkbox" name="ids[]" value="{{ $ev['id'] }}"
                                    class="event-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-5 py-4 text-slate-400">
                                {{ ($events->currentPage() - 1) * $events->perPage() + ($i + 1) }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800 max-w-[200px]">
                                <div class="truncate">{{ $ev['nama'] }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-600 max-w-[180px]">
                                <div class="truncate">{{ $ev['lokasi'] }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-600 whitespace-nowrap">{{ $ev['tanggal'] }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800 whitespace-nowrap">
                                {{ $ev['harga'] == 0 ? 'Gratis' : 'Rp' . number_format($ev['harga'], 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                @if($ev['kategori'] === 'upcoming')
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">Upcoming</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700">Highlight</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    {{-- Kategori button --}}
                                    <a href="{{ route('admin.events.categories', $ev['id']) }}"
                                        class="inline-flex items-center gap-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        Kategori
                                    </a>
                                    {{-- Edit button --}}
                                    <button
                                        onclick="openEdit({{ $ev['id'] }}, @js($ev['nama']), @js($ev['lokasi']), @js($ev['tanggal']), {{ $ev['harga'] }}, @js($ev['kategori']), @js($ev['urlBeli'] ?? ''), @js($ev['thumbnail'] ?? ''), @js($ev['waktu'] ?? ''), @js($ev['deskripsi'] ?? ''), @js($ev['syarat_ketentuan'] ?? ''), @js($ev['payment_methods'] ?? []))"
                                        class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.events.destroy', $ev['id']) }}" method="POST"
                                        onsubmit="return confirm('Hapus event \'{{ addslashes($ev['nama']) }}\'?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Belum ada event. Klik "Tambah Event" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL: ADD EVENT ===== --}}
    <div id="modalAdd" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-7 py-5 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Tambah Event Baru</h3>
                <button onclick="document.getElementById('modalAdd').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data"
                class="px-7 py-6 space-y-4">
                @csrf
                @include('admin.partials.event-form')
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium transition-colors">Simpan
                        Event</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: EDIT EVENT ===== --}}
    <div id="modalEdit" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-7 py-5 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Edit Event</h3>
                <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="px-7 py-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Event
                        *</label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Thumbnail
                        Event (JPG/PNG/WebP, maks 1MB)</label>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <div id="edit_thumbnail_preview" class="mt-2 text-xs text-slate-500 hidden">
                        Thumbnail saat ini: <a href="#" id="edit_thumbnail_link" target="_blank"
                            class="text-blue-600 hover:underline">Lihat Gambar</a>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Lokasi
                        *</label>
                    <input type="text" name="lokasi" id="edit_lokasi" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Event
                        *</label>
                    <input type="text" name="tanggal" id="edit_tanggal" placeholder="contoh: 25 Juli 2026" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Harga Tiket
                        (Rp) *</label>
                    <input type="number" name="harga" id="edit_harga" min="0" placeholder="0 untuk gratis" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kategori
                        *</label>
                    <select name="kategori" id="edit_kategori" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="upcoming">Upcoming Events</option>
                        <option value="highlight">Highlight Events</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">URL Beli
                        Tiket</label>
                    <input type="text" name="urlBeli" id="edit_urlBeli"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Waktu
                        Event</label>
                    <input type="text" name="waktu" id="edit_waktu"
                        placeholder="contoh: 16.00 - 23.00 (kosongkan untuk default)"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi
                        Event</label>
                    <textarea name="deskripsi" id="edit_deskripsi"
                        placeholder="Deskripsi lengkap event (kosongkan untuk default)" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Syarat &
                        Ketentuan</label>
                    <textarea name="syarat_ketentuan" id="edit_syarat_ketentuan"
                        placeholder="Syarat & ketentuan event (kosongkan untuk default)" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"></textarea>
                </div>
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Metode Pembayaran Transfer *</label>
                    <div id="edit-payment-methods-container" class="space-y-3">
                        <!-- Will be populated dynamically by JS -->
                    </div>
                    <button type="button" onclick="addEditPaymentRow()" class="mt-2 inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Metode Pembayaran
                    </button>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium transition-colors">Update
                        Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addEditPaymentRow(name = '', number = '', holder = '') {
            const container = document.getElementById('edit-payment-methods-container');
            const row = document.createElement('div');
            row.className = 'edit-payment-method-row flex gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-100 relative';
            row.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 flex-1">
                    <input type="text" name="payment_name[]" value="${name}" placeholder="Nama Bank / E-Wallet" required
                           class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                    <input type="text" name="payment_number[]" value="${number}" placeholder="Nomor Rekening / HP" required
                           class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                    <input type="text" name="payment_holder[]" value="${holder}" placeholder="Nama Pemilik" required
                           class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                </div>
                <button type="button" onclick="removeEditPaymentRow(this)" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            `;
            container.appendChild(row);
        }

        function removeEditPaymentRow(button) {
            const container = document.getElementById('edit-payment-methods-container');
            const rows = container.querySelectorAll('.edit-payment-method-row');
            if (rows.length > 1) {
                button.closest('.edit-payment-method-row').remove();
            } else {
                alert('Minimal harus ada 1 metode pembayaran.');
            }
        }

        function openEdit(id, nama, lokasi, tanggal, harga, kategori, urlBeli, thumbnail, waktu, deskripsi, syarat_ketentuan, paymentMethods) {
            var form = document.getElementById('editForm');
            form.action = '/admin/events/' + id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_lokasi').value = lokasi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_urlBeli').value = urlBeli;

            var preview = document.getElementById('edit_thumbnail_preview');
            var link = document.getElementById('edit_thumbnail_link');
            if (thumbnail) {
                preview.classList.remove('hidden');
                link.href = thumbnail;
            } else {
                preview.classList.add('hidden');
            }

            document.getElementById('edit_waktu').value = waktu || '';
            document.getElementById('edit_deskripsi').value = deskripsi || '';
            document.getElementById('edit_syarat_ketentuan').value = syarat_ketentuan || '';

            // Populate payment methods
            const editContainer = document.getElementById('edit-payment-methods-container');
            editContainer.innerHTML = '';
            if (paymentMethods && paymentMethods.length > 0) {
                paymentMethods.forEach(pm => {
                    addEditPaymentRow(pm.name, pm.account_number, pm.account_holder);
                });
            } else {
                addEditPaymentRow('Transfer Bank BCA', '80771234567890', 'SeTiket Organizer');
                addEditPaymentRow('E-Wallet DANA', '081234567890', 'SeTiket Organizer');
            }

            document.getElementById('modalEdit').classList.remove('hidden');
        }

        // Checkboxes and Bulk delete logic
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.event-checkbox');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const selectedCount = document.getElementById('selectedCount');

            function updateBulkDeleteButton() {
                const checked = document.querySelectorAll('.event-checkbox:checked');
                selectedCount.textContent = checked.length;
                if (checked.length > 0) {
                    btnBulkDelete.classList.remove('hidden');
                } else {
                    btnBulkDelete.classList.add('hidden');
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateBulkDeleteButton();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const allChecked = document.querySelectorAll('.event-checkbox:checked').length === checkboxes.length;
                    if (selectAll) selectAll.checked = allChecked;
                    updateBulkDeleteButton();
                });
            });
        });

        function submitBulkDelete() {
            const checked = document.querySelectorAll('.event-checkbox:checked');
            if (checked.length === 0) return;
            if (confirm('Hapus ' + checked.length + ' event terpilih?')) {
                const ids = Array.from(checked).map(cb => cb.value);

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.events.bulk-destroy") }}';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modals on backdrop click
        ['modalAdd', 'modalEdit'].forEach(function (id) {
            document.getElementById(id).addEventListener('click', function (e) {
                if (e.target === this) this.classList.add('hidden');
            });
        });
    </script>
@endsection