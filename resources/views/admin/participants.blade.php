@extends('admin.layouts.admin')

@section('header_title', 'Participants Data')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
        {{-- Baris 1: Judul + tombol aksi --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-lg text-slate-800">All Registered Participants</h3>
                <p class="text-xs text-slate-500 mt-1">Total: <span class="font-bold text-slate-700">{{ $participants->total() }}</span> participants</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
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

        {{-- Baris 2: Filter & Search --}}
        <form action="{{ route('admin.participants') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="relative flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, WA, ticket..." class="border border-slate-200 rounded-lg pl-9 pr-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                <select name="category" onchange="this.form.submit()" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $currentCategory == 'all' ? 'selected' : '' }}>All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->code }}" {{ $currentCategory == $cat->code ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="sort" onchange="this.form.submit()" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="newest" {{ $currentSort == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Terlama</option>
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    Search
                </button>

                @if(request('search') || $currentCategory !== 'all' || $currentSort !== 'newest')
                    <a href="{{ route('admin.participants') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium whitespace-nowrap">Reset</a>
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
                        <input type="checkbox" id="select-all" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Contact</th>
                    @if(auth()->user()->role === 'super_admin')
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
                        <div class="text-slate-800">{{ $participant->phone }}</div>
                        <div class="text-xs text-slate-500">{{ $participant->user->email ?? '-' }}</div>
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
                        <td class="px-6 py-4">
                            @if($participant->user && $participant->user->plain_password)
                                <div class="flex items-center gap-1.5">
                                    <span class="password-dots text-slate-400 font-mono text-xs">••••••••</span>
                                    <span class="password-text text-slate-800 font-mono text-xs" style="display:none;">{{ $participant->user->plain_password }}</span>
                                    <button type="button" onclick="togglePassword(this)" class="text-slate-400 hover:text-blue-600 transition-colors p-0.5" title="Lihat password">
                                        <svg class="eye-show w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg class="eye-hide w-4 h-4" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
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
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.participants.edit', $participant->id) }}" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg text-xs font-medium transition-colors">Edit</a>
                            <form action="{{ route('admin.participants.delete', $participant->id) }}" method="POST" onsubmit="return confirm('Delete this participant? All their tickets and payments will be lost!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg text-xs font-medium transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($participants->isEmpty())
                <tr>
                    <td colspan="{{ auth()->user()->role === 'super_admin' ? 10 : 7 }}" class="px-6 py-12 text-center text-slate-500">No participants found.</td>
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
@endsection
