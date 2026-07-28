@extends('admin.layouts.admin')

@section('header_title', 'Participants Data')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-lg text-slate-800">All Registered Participants</h3>
            <p class="text-xs text-slate-500 mt-1">Total: <span class="font-bold text-slate-700">{{ $participants->total() }}</span> participants</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter & Search Form -->
            <form action="{{ route('admin.participants') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, WA, ticket..." class="border border-slate-200 rounded-lg pl-9 pr-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-52 md:w-60">
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
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
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
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $participant->fullname }}</td>
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
</script>
@endsection
