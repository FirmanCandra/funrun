@extends('admin.layouts.admin')

@section('header_title', 'Manajemen Kategori — ' . $dbEvent->title)

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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

{{-- Breadcrumb back to events --}}
<div class="mb-4">
    <a href="{{ route('admin.events') }}" class="text-blue-600 hover:underline flex items-center gap-1 text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Event
    </a>
</div>

{{-- Header row --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">{{ $dbEvent->title }}</h2>
        <p class="text-slate-500 text-xs mt-0.5">Kelola kategori run yang terdaftar untuk event ini.</p>
    </div>
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/></svg>
        Tambah Kategori
    </button>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500 w-12">No</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Nama Kategori</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Kode Kategori</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Kode BIB</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Harga</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($categories as $i => $cat)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-4 text-slate-400">{{ $i + 1 }}</td>
                    <td class="px-5 py-4 font-semibold text-slate-800">{{ $cat->name }}</td>
                    <td class="px-5 py-4 text-slate-600 font-mono">{{ $cat->code }}</td>
                    <td class="px-5 py-4 text-slate-600 font-mono font-bold text-blue-600">{{ $cat->bib_code }}</td>
                    <td class="px-5 py-4 font-semibold text-slate-800 whitespace-nowrap">
                        {{ $cat->price == 0 ? 'Gratis' : 'Rp '.number_format($cat->price, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            {{-- Edit button --}}
                            <button onclick="openEdit({{ $cat->id }}, @js($cat->name), @js($cat->code), @js($cat->bib_code), {{ $cat->price }})"
                                    class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            {{-- Delete --}}
                            <form action="{{ route('admin.events.categories.destroy', $cat->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus kategori \'{{ addslashes($cat->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        Belum ada kategori untuk event ini. Klik "Tambah Kategori" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== MODAL: ADD CATEGORY ===== --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-7 py-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Tambah Kategori Baru</h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.events.categories.store', $dbEvent->id) }}" method="POST" class="px-7 py-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" required placeholder="contoh: 3K Fun Walk"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kode Kategori *</label>
                <input type="text" name="code" required placeholder="contoh: 3K"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                <p class="text-slate-400 text-[10px] mt-1">Digunakan untuk parameter seleksi dan prefix nomor tiket.</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kode Unik BIB *</label>
                <input type="text" name="bib_code" required placeholder="contoh: FW"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                <p class="text-slate-400 text-[10px] mt-1">Kalimat atau kode unik yang disisipkan setelah kode kategori pada nomor BIB (misal: ST-3K-FW-0001).</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Harga Tiket (Rp) *</label>
                <input type="number" name="price" required min="0" placeholder="0 untuk gratis"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium transition-colors">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL: EDIT CATEGORY ===== --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-7 py-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Edit Kategori</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST" class="px-7 py-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kode Kategori *</label>
                <input type="text" name="code" id="edit_code" required
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kode Unik BIB *</label>
                <input type="text" name="bib_code" id="edit_bib_code" required
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Harga Tiket (Rp) *</label>
                <input type="number" name="price" id="edit_price" required min="0"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium transition-colors">Update Kategori</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, name, code, bib_code, price) {
    var form = document.getElementById('editForm');
    form.action = '/admin/events/categories/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_bib_code').value = bib_code;
    document.getElementById('edit_price').value = price;
    document.getElementById('modalEdit').classList.remove('hidden');
}

// Close modals on backdrop click
['modalAdd','modalEdit'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
});
</script>
@endsection
