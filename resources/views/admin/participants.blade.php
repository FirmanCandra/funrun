@extends('admin.layouts.admin')

@section('header_title', 'Participants Data')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-lg text-slate-800">All Registered Participants</h3>
            <p class="text-xs text-slate-500 mt-1">Total: <span class="font-bold text-slate-700">{{ $participants->total() }}</span> participants</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter & Search Form -->
            <form action="{{ route('admin.participants') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                <div class="relative flex-1 min-w-[12rem]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, WA, ticket..." class="border border-slate-200 rounded-lg pl-9 pr-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-60">
                    <div class="absolute left-3 top-2.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <select name="category" onchange="this.form.submit()" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $currentCategory == 'all' ? 'selected' : '' }}>All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->code }}" {{ $currentCategory == $cat->code ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors">
                    Search
                </button>

                @if(request('search') || $currentCategory !== 'all')
                    <a href="{{ route('admin.participants') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium px-1">Reset</a>
                @endif
            </form>

            <button type="button" id="bulk-delete-btn" onclick="confirmBulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap" style="display: none;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Delete Selected (<span id="selected-count">0</span>)
            </button>

            <a href="{{ route('admin.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export CSV
            </a>
        </div>
    </div>
    
    {{-- Digulir mendatar di layar sempit, dengan lebar minimum agar kolomnya tetap terbaca. --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[60rem] text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4 w-10">
                        <input type="checkbox" id="select-all" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Contact</th>
                    @if(auth()->user()->role === 'super_admin')
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Admin</th>
                    @endif
                    <th class="px-6 py-4 text-center">Category & Size</th>
                    <th class="px-6 py-4 text-center">Ticket Status</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($participants as $participant)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <input type="checkbox" name="ids[]" value="{{ $participant->id }}" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-4 text-center text-slate-500 font-medium">
                        {{ ($participants->currentPage() - 1) * $participants->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $participant->displayName() }}</td>
                    <td class="px-6 py-4">
                        <div class="text-slate-800 font-medium">{{ $participant->phone }}</div>
                        <div class="text-xs text-slate-500">{{ $participant->user->email ?? '-' }}</div>
                        @if($participant->user)
                            <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                                <span class="text-slate-400 font-mono text-[11px]">Pass:</span>
                                @if(!empty($participant->user->plain_password))
                                    <span id="pass-val-{{ $participant->id }}" class="font-mono text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded text-[11px] select-all">••••••••</span>
                                    <button type="button" 
                                            onclick="toggleParticipantPassword({{ $participant->id }}, '{{ addslashes($participant->user->plain_password) }}')" 
                                            class="text-slate-400 hover:text-blue-600 focus:outline-none p-0.5 transition-colors" 
                                            title="Lihat/Sembunyikan Password">
                                        <svg id="pass-eye-{{ $participant->id }}" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-slate-400 italic text-[11px]" title="Password terenkripsi Bcrypt (terdaftar sebelum fitur plain password)">[Terenkripsi]</span>
                                @endif
                                <button type="button" 
                                        onclick="openAccountModal({{ $participant->id }}, '{{ addslashes($participant->displayName()) }}', '{{ addslashes($participant->user->email ?? '') }}', '{{ $participant->phone ?? '' }}', '{{ addslashes($participant->user->plain_password ?? '') }}')"
                                        class="text-blue-600 hover:text-blue-800 font-medium hover:underline text-[11px] ml-0.5">
                                    Ubah
                                </button>
                            </div>
                        @endif
                    </td>
                    @if(auth()->user()->role === 'super_admin')
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $participant->event->title ?? '-' }}</div>
                            <div class="text-xs text-blue-600 font-semibold">Kategori: {{ $participant->category }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($participant->event && $participant->event->admins->isNotEmpty())
                                @foreach($participant->event->admins as $adm)
                                    <div class="font-medium text-slate-700">{{ $adm->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $adm->email }}</div>
                                @endforeach
                            @else
                                <span class="text-xs text-slate-400 italic">No Admin</span>
                            @endif
                        </td>
                    @endif
                    <td class="px-6 py-4 text-center">
                        <div class="font-bold text-blue-600">{{ $participant->category }}</div>
                        <div class="text-xs text-slate-500">Jersey: {{ $participant->jersey_size }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($participant->ticket)
                            @if($participant->ticket->status === 'checked-in')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">Checked In</span>
                            @elseif($participant->ticket->status === 'valid')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Valid</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                            @endif
                            <div class="text-xs text-slate-500 mt-1 font-mono">
                                <a href="{{ route('ticket.show', $participant->ticket->ticket_code) }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $participant->ticket->ticket_code }}
                                </a>
                            </div>
                        @else
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-medium">No Ticket</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center gap-1.5">
                            @if($participant->user)
                                <button type="button" 
                                        onclick="openAccountModal({{ $participant->id }}, '{{ addslashes($participant->displayName()) }}', '{{ addslashes($participant->user->email ?? '') }}', '{{ $participant->phone ?? '' }}', '{{ addslashes($participant->user->plain_password ?? '') }}')"
                                        class="text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors flex items-center gap-1"
                                        title="Atur Password & Kirim WhatsApp">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    <span>Password</span>
                                </button>
                            @endif
                            <a href="{{ route('admin.participants.edit', $participant->id) }}" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">Edit</a>
                            <form action="{{ route('admin.participants.delete', $participant->id) }}" method="POST" onsubmit="return confirm('Hapus peserta ini? Semua tiket dan pembayaran terkait akan ikut terhapus!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($participants->isEmpty())
                <tr>
                    <td colspan="{{ auth()->user()->role === 'super_admin' ? 9 : 7 }}" class="px-6 py-12 text-center text-slate-500">No participants found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($participants->hasPages())
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

        if (confirm(`Are you sure you want to delete ${checkedCheckboxes.length} selected participants? All their tickets and payments will be lost!`)) {
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

    // --- Manajemen Akun & Password Peserta ---
    let currentParticipantPhone = '';
    let currentParticipantName = '';

    function toggleParticipantPassword(id, plainPass) {
        const el = document.getElementById('pass-val-' + id);
        if (!el) return;
        if (el.textContent === '••••••••') {
            el.textContent = plainPass;
            el.classList.add('text-blue-700', 'font-bold');
        } else {
            el.textContent = '••••••••';
            el.classList.remove('text-blue-700', 'font-bold');
        }
    }

    function openAccountModal(id, name, email, phone, currentPass) {
        document.getElementById('modal-participant-id').value = id;
        document.getElementById('modal-participant-name').textContent = name;
        document.getElementById('modal-participant-phone').textContent = phone || '-';
        document.getElementById('modal-input-email').value = email;
        document.getElementById('modal-input-password').value = currentPass || '';

        currentParticipantPhone = phone;
        currentParticipantName = name;

        document.getElementById('modal-error-box').classList.add('hidden');
        document.getElementById('modal-success-box').classList.add('hidden');

        const modal = document.getElementById('account-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAccountModal() {
        const modal = document.getElementById('account-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function generateRandomPassword() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let rand = '';
        for (let i = 0; i < 4; i++) {
            rand += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const num = Math.floor(1000 + Math.random() * 9000);
        document.getElementById('modal-input-password').value = 'Setiket' + num;
    }

    function formatPhoneForWa(phone) {
        let clean = (phone || '').replace(/[^0-9]/g, '');
        if (clean.startsWith('0')) {
            clean = '62' + clean.slice(1);
        }
        return clean;
    }

    async function performAccountSave() {
        const id = document.getElementById('modal-participant-id').value;
        const email = document.getElementById('modal-input-email').value;
        const password = document.getElementById('modal-input-password').value;

        const errorBox = document.getElementById('modal-error-box');
        const successBox = document.getElementById('modal-success-box');
        errorBox.classList.add('hidden');
        successBox.classList.add('hidden');

        const response = await fetch(`/admin/participants/${id}/account`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal menyimpan perubahan.'));
        }

        return { email, password };
    }

    async function submitAccountModal(e) {
        e.preventDefault();
        const errorBox = document.getElementById('modal-error-box');
        const successBox = document.getElementById('modal-success-box');

        try {
            await performAccountSave();
            successBox.textContent = 'Perubahan berhasil disimpan. Memuat ulang halaman...';
            successBox.classList.remove('hidden');
            setTimeout(() => {
                window.location.reload();
            }, 800);
        } catch (err) {
            errorBox.textContent = err.message;
            errorBox.classList.remove('hidden');
        }
    }

    async function saveAndOpenWhatsApp() {
        const errorBox = document.getElementById('modal-error-box');
        const successBox = document.getElementById('modal-success-box');

        try {
            const { email, password } = await performAccountSave();
            successBox.textContent = 'Data berhasil disimpan. Membuka WhatsApp...';
            successBox.classList.remove('hidden');

            const cleanPhone = formatPhoneForWa(currentParticipantPhone);
            let msg = `Halo ${currentParticipantName},\n\nBerikut adalah data akun Anda di SeTiket.id:\nEmail: ${email}\n`;
            if (password) {
                msg += `Password: ${password}\n\n`;
            } else {
                msg += `\n`;
            }
            msg += `Silakan login di tautan berikut:\nhttps://setiket.id/login\n\nSimpan pesan ini dengan baik. Terima kasih!`;

            const waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(msg)}`;
            window.open(waUrl, '_blank');

            setTimeout(() => {
                window.location.reload();
            }, 1200);
        } catch (err) {
            errorBox.textContent = err.message;
            errorBox.classList.remove('hidden');
        }
    }
</script>

<!-- Modal Atur Akun & Password Peserta -->
<div id="account-modal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h4 class="font-bold text-slate-800 text-base">Atur Akun & Password Peserta</h4>
                <p class="text-xs text-slate-500 mt-0.5">Solusi jika peserta lupa password atau salah ketik email.</p>
            </div>
            <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="account-modal-form" method="POST" onsubmit="submitAccountModal(event)" class="p-5 space-y-4">
            @csrf
            <input type="hidden" id="modal-participant-id" value="">

            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <div class="text-xs text-slate-500">Nama Peserta</div>
                <div id="modal-participant-name" class="font-semibold text-slate-800 text-sm">-</div>
                <div class="text-xs text-slate-500 mt-1">No. WhatsApp</div>
                <div id="modal-participant-phone" class="font-mono text-slate-700 text-xs font-medium">-</div>
            </div>

            <div>
                <label for="modal-input-email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Email Akun
                </label>
                <input type="email" id="modal-input-email" name="email" required
                       class="w-full border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-[11px] text-slate-500 mt-1">Ubah email jika peserta sebelumnya salah ketik / typo email.</p>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="modal-input-password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Password Baru
                    </label>
                    <button type="button" onclick="generateRandomPassword()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Generate Acak
                    </button>
                </div>
                <div class="relative">
                    <input type="text" id="modal-input-password" name="password" placeholder="Ketik atau klik Generate Acak (min 6 karakter)"
                           class="w-full border border-slate-200 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika hanya ingin memperbarui email.</p>
            </div>

            <div id="modal-error-box" class="hidden p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg"></div>
            <div id="modal-success-box" class="hidden p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg"></div>

            <div class="pt-2 flex flex-col gap-2">
                <button type="button" onclick="saveAndOpenWhatsApp()" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition-colors flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Simpan & Kirim ke WhatsApp</span>
                </button>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-xl text-xs transition-colors">
                        Simpan Saja
                    </button>
                    <button type="button" onclick="closeAccountModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-2 px-4 rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</script>
@endsection
