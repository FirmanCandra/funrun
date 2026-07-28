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
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Syarat & Ketentuan</label>
    <textarea name="syarat_ketentuan" placeholder="Syarat & ketentuan event (kosongkan untuk default)" rows="3"
              class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"></textarea>
</div>

<div class="mt-4 border-t border-slate-100 pt-4">
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Metode Pembayaran Transfer *</label>
    <div id="payment-methods-container" class="space-y-3">
        <!-- Default BCA -->
        <div class="payment-method-row flex gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-100 relative">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 flex-1">
                <input type="text" name="payment_name[]" value="Transfer Bank BCA" placeholder="Nama Bank / E-Wallet" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                <input type="text" name="payment_number[]" value="80771234567890" placeholder="Nomor Rekening / HP" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                <input type="text" name="payment_holder[]" value="SeTiket Organizer" placeholder="Nama Pemilik" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
            </div>
            <button type="button" onclick="removePaymentRow(this)" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        <!-- Default DANA -->
        <div class="payment-method-row flex gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-100 relative">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 flex-1">
                <input type="text" name="payment_name[]" value="E-Wallet DANA" placeholder="Nama Bank / E-Wallet" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                <input type="text" name="payment_number[]" value="081234567890" placeholder="Nomor Rekening / HP" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                <input type="text" name="payment_holder[]" value="SeTiket Organizer" placeholder="Nama Pemilik" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
            </div>
            <button type="button" onclick="removePaymentRow(this)" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>
    <button type="button" onclick="addPaymentRow()" class="mt-2 inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Metode Pembayaran
    </button>
</div>

<script>
    function addPaymentRow(name = '', number = '', holder = '') {
        const container = document.getElementById('payment-methods-container');
        const row = document.createElement('div');
        row.className = 'payment-method-row flex gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-100 relative';
        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 flex-1">
                <input type="text" name="payment_name[]" value="${name}" placeholder="Nama Bank / E-Wallet" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                <input type="text" name="payment_number[]" value="${number}" placeholder="Nomor Rekening / HP" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                <input type="text" name="payment_holder[]" value="${holder}" placeholder="Nama Pemilik" required
                       class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
            </div>
            <button type="button" onclick="removePaymentRow(this)" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        `;
        container.appendChild(row);
    }

    function removePaymentRow(button) {
        const container = document.getElementById('payment-methods-container');
        const rows = container.querySelectorAll('.payment-method-row');
        if (rows.length > 1) {
            button.closest('.payment-method-row').remove();
        } else {
            alert('Minimal harus ada 1 metode pembayaran.');
        }
    }
</script>
