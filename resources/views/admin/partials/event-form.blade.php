<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Event *</label>
    <input type="text" name="nama" required placeholder="Nama event"
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Thumbnail Event (JPG/PNG/WebP, maks 1MB)</label>
    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Lokasi *</label>
    <input type="text" name="lokasi" required placeholder="Lokasi event"
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Event *</label>
    <input type="text" name="tanggal" required placeholder="contoh: 25 Juli 2026"
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Harga Tiket (Rp) *</label>
    <input type="number" name="harga" min="0" value="0" placeholder="0 untuk gratis" required
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kategori *</label>
    <select name="kategori" required
            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
        <option value="upcoming">Upcoming Events</option>
        <option value="highlight">Highlight Events</option>
    </select>
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">URL Beli Tiket</label>
    <input type="text" name="urlBeli" value="https://wa.me/6289681201941"
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Waktu Event</label>
    <input type="text" name="waktu" placeholder="contoh: 16.00 - 23.00 (kosongkan untuk default)"
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi Event</label>
    <textarea name="deskripsi" placeholder="Deskripsi lengkap event (kosongkan untuk default)" rows="3"
              class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"></textarea>
</div>
<div class="flex items-center gap-2 mt-4 mb-2">
    <input type="checkbox" name="is_closed" value="1" id="add_is_closed" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
    <label for="add_is_closed" class="text-sm font-semibold text-slate-700 cursor-pointer">
        Tutup Pendaftaran (Event tetap aktif untuk Scanner / Check-in)
    </label>
</div>
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Syarat & Ketentuan</label>
    <textarea name="syarat_ketentuan" placeholder="Syarat & ketentuan event (kosongkan untuk default)" rows="3"
              class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"></textarea>
</div>

<div class="mt-4 border-t border-slate-100 pt-4">
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Rekening Pembayaran</label>
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
        Rekening tujuan transfer diatur terpisah di menu
        <a href="{{ route('admin.payment-accounts') }}" class="font-semibold underline">Rekening Pembayaran</a>,
        supaya satu event bisa punya beberapa rekening dan riwayat pesanan tetap menyimpan nomor yang berlaku saat itu.
        <div class="text-xs text-blue-700 mt-1">Event baru wajib diberi minimal satu rekening sebelum tiketnya bisa dibeli.</div>
    </div>
</div>

<script>
</script>
