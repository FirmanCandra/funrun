@extends('admin.layouts.admin')

@section('header_title', 'Participants Data')

@section('content')

    @php
        $targetEvents = \App\Models\Event::orderBy('date', 'desc')->get();
        if (auth()->user()->role === 'admin') {
            $targetEvents = $targetEvents->where('id', auth()->user()->event_id);
        }
    @endphp

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <div>
                <p class="font-medium">{{ session('success') }}</p>
                @if (session('import_skipped_reasons') && count(session('import_skipped_reasons')) > 0)
                    <details class="mt-2">
                        <summary class="text-xs cursor-pointer text-green-600 hover:text-green-800 font-medium">Lihat detail
                            baris yang dilewati ({{ count(session('import_skipped_reasons')) }})</summary>
                        <ul class="mt-1 text-xs space-y-0.5 text-green-600">
                            @foreach (session('import_skipped_reasons') as $reason)
                                <li>• {{ $reason }}</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
            {{-- Baris 1: Judul + tombol aksi --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-lg text-slate-800">Semua Partisipan Terdaftar</h3>
                    <p class="text-xs text-slate-500 mt-1">Total: <span
                            class="font-bold text-slate-700">{{ $participants->total() }}</span> partisipan</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    {{-- Bulk delete — muncul saat ada checkbox terpilih --}}
                    <button type="button" id="bulk-delete-btn" onclick="confirmBulkDelete()"
                        class="bg-red-600 hover:bg-red-700 text-white px-3.5 py-2 rounded-xl text-sm font-medium transition-colors items-center gap-1.5 whitespace-nowrap"
                        style="display: none;">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Hapus (<span id="selected-count">0</span>)
                    </button>

                    {{-- Download Template --}}
                    <button type="button" onclick="document.getElementById('modalTemplate').classList.remove('hidden')"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Template
                    </button>

                    {{-- Export CSV --}}
                    <button type="button" onclick="document.getElementById('modalExport').classList.remove('hidden')"
                        class="bg-green-600 hover:bg-green-700 text-white px-3.5 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export
                    </button>

                    {{-- Import ke Event Lain --}}
                    <button type="button" onclick="document.getElementById('modalImport').classList.remove('hidden')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import
                    </button>
                </div>
            </div>

            {{-- Baris 2: Filter & Search --}}
            <form action="{{ route('admin.participants') }}" method="GET"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <div class="relative flex-1 min-w-0">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, WA, tiket..."
                        class="border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    <div class="absolute left-3 top-2.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                    @if (auth()->user()->role === 'super_admin' && $events->isNotEmpty())
                        <select name="event_id" onchange="this.form.submit()"
                            class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="all" {{ $currentEventId == 'all' ? 'selected' : '' }}>Semua Event</option>
                            @foreach ($events as $ev)
                                <option value="{{ $ev->id }}" {{ $currentEventId == $ev->id ? 'selected' : '' }}>
                                    {{ $ev->title }}</option>
                            @endforeach
                        </select>
                    @endif

                    <select name="category" onchange="this.form.submit()"
                        class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="all" {{ $currentCategory == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->code }}" {{ $currentCategory == $cat->code ? 'selected' : '' }}>
                                {{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select name="sort" onchange="this.form.submit()"
                        class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="newest" {{ $currentSort == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
                        Cari
                    </button>

                    @if (request('search') ||
                            $currentCategory !== 'all' ||
                            $currentSort !== 'newest' ||
                            (isset($currentEventId) && $currentEventId !== 'all'))
                        <a href="{{ route('admin.participants') }}"
                            class="text-slate-500 hover:text-slate-700 text-sm font-medium whitespace-nowrap">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Digulir mendatar di layar sempit, dengan lebar minimum agar kolomnya tetap terbaca. --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[60rem] text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4 w-10">
                        <input type="checkbox" id="select-all"
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Contact</th>
                    @if (auth()->user()->role === 'super_admin')
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Admin</th>
                        <th class="px-6 py-4">Password</th>
                    @endif
                    <th class="px-6 py-4 text-center">Category & Size</th>
                    <th class="px-6 py-4 text-center">Ticket Status</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($participants as $participant)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $participant->id }}"
                                class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 text-center text-slate-500 font-medium">
                            {{ ($participants->currentPage() - 1) * $participants->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $participant->displayName() }}</td>
                        <td class="px-6 py-4">
                            <div class="text-slate-800">{{ $participant->phone }}</div>
                            <div class="text-xs text-slate-500">{{ $participant->user->email ?? '-' }}</div>
                        </td>
                        @if (auth()->user()->role === 'super_admin')
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $participant->event->title ?? '-' }}</div>
                                <div class="text-xs text-blue-600 font-semibold">Kategori: {{ $participant->category }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($participant->event && $participant->event->admins->isNotEmpty())
                                    @foreach ($participant->event->admins as $adm)
                                        <div class="font-medium text-slate-700">{{ $adm->name }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $adm->email }}</div>
                                    @endforeach
                                @else
                                    <span class="text-xs text-slate-400 italic">No Admin</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($participant->user && $participant->user->plain_password)
                                    <div class="flex items-center gap-1.5">
                                        <span class="password-dots text-slate-400 font-mono text-xs">••••••••</span>
                                        <span class="password-text text-slate-800 font-mono text-xs"
                                            style="display:none;">{{ $participant->user->plain_password }}</span>
                                        <button type="button" onclick="togglePassword(this)"
                                            class="text-slate-400 hover:text-blue-600 transition-colors p-0.5"
                                            title="Lihat password">
                                            <svg class="eye-show w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg class="eye-hide w-4 h-4" style="display:none;" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">—</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-6 py-4 text-center">
                            <div class="font-bold text-blue-600">{{ $participant->category }}</div>
                            <div class="text-xs text-slate-500">T-Shirt: {{ $participant->jersey_size }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($participant->ticket)
                                @if ($participant->ticket->status === 'checked-in')
                                    <span
                                        class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">Checked
                                        In</span>
                                @elseif($participant->ticket->status === 'valid')
                                    <span
                                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Valid</span>
                                @else
                                    <span
                                        class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                                @endif
                                <div class="text-xs text-slate-500 mt-1 font-mono">
                                    <a href="{{ route('ticket.show', $participant->ticket->ticket_code) }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        {{ $participant->ticket->ticket_code }}
                                    </a>
                                </div>
                            @else
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-medium">No
                                    Ticket</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.participants.edit', $participant->id) }}"
                                    class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg text-xs font-medium transition-colors">Edit</a>
                                <form action="{{ route('admin.participants.delete', $participant->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this participant? All their tickets and payments will be lost!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg text-xs font-medium transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if ($participants->isEmpty())
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'super_admin' ? 10 : 7 }}"
                            class="px-6 py-12 text-center text-slate-500">No participants found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($participants->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $participants->links() }}
        </div>
    @endif
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.participants.bulk-delete') }}" method="POST" class="hidden">
        @csrf
    </form>

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

            if (confirm(
                    `Are you sure you want to delete ${checkedCheckboxes.length} selected participants? All their tickets and payments will be lost!`
                )) {
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
    </script>

    <script>
        function togglePassword(btn) {
            const container = btn.closest('td');
            const dots = container.querySelector('.password-dots');
            const text = container.querySelector('.password-text');
            const eyeShow = btn.querySelector('.eye-show');
            const eyeHide = btn.querySelector('.eye-hide');

            if (dots.style.display === 'none') {
                dots.style.display = '';
                text.style.display = 'none';
                eyeShow.style.display = '';
                eyeHide.style.display = 'none';
            } else {
                dots.style.display = 'none';
                text.style.display = '';
                eyeShow.style.display = 'none';
                eyeHide.style.display = '';
            }
        }
    </script>

    {{-- ===== MODAL: IMPORT PARTICIPANTS ===== --}}
    <div id="modalImport"
        class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="flex items-center justify-between px-5 sm:px-7 py-5 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Import Partisipan ke Event Lain</h3>
                <button onclick="document.getElementById('modalImport').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.participants.import') }}" method="POST" enctype="multipart/form-data"
                class="px-5 sm:px-7 py-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Event Tujuan
                        *</label>
                    <select name="target_event_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="">-- Pilih Event Tujuan --</option>
                        @foreach ($targetEvents as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->title }}
                                ({{ \Carbon\Carbon::parse($ev->date)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Tiket untuk partisipan yang di-import akan langsung
                        berstatus <b>Valid</b>.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">File CSV /
                        Excel *</label>
                    <input type="file" name="import_file" accept=".csv, .xlsx, .xls" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <p class="text-[11px] text-slate-500 mt-1">Gunakan template yang disediakan. Kolom <b>Nama Lengkap</b>
                        dan <b>Email</b> wajib ada. Baris dengan NIK yang sudah ada di event tujuan akan dilewati.</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                    <button type="submit"
                        onclick="this.innerHTML='Mengimport...'; this.form.submit(); this.disabled=true;"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium transition-colors">Mulai
                        Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: DOWNLOAD TEMPLATE ===== --}}
    <div id="modalTemplate"
        class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="flex items-center justify-between px-5 sm:px-7 py-5 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Unduh Template Import</h3>
                <button onclick="document.getElementById('modalTemplate').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.participants.import-template') }}" method="GET"
                class="px-5 sm:px-7 py-6 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Event
                        *</label>
                    <select name="event_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="">-- Pilih Event --</option>
                        @foreach ($targetEvents as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->title }}
                                ({{ \Carbon\Carbon::parse($ev->date)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Karena setiap event memiliki pertanyaan dan kolom yang
                        berbeda, silakan pilih event untuk menyesuaikan susunan header kolom di Excel.</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('modalTemplate').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2.5 rounded-xl font-medium transition-colors">Unduh
                        Template</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: EXPORT DATA ===== --}}
    <div id="modalExport"
        class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="flex items-center justify-between px-5 sm:px-7 py-5 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Export Partisipan (Excel)</h3>
                <button onclick="document.getElementById('modalExport').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.export') }}" method="GET" class="px-5 sm:px-7 py-6 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Event
                        *</label>
                    <select name="event_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="">-- Pilih Event --</option>
                        @foreach ($targetEvents as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->title }}
                                ({{ \Carbon\Carbon::parse($ev->date)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Export akan mengunduh seluruh data partisipan yang ada di
                        dalam event ini, mengabaikan pencarian atau filter kategori yang aktif.</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('modalExport').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-xl font-medium transition-colors">Export
                        Excel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
