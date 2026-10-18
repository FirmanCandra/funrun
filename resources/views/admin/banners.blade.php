@extends('admin.layouts.admin')

@section('header_title', 'Kelola Banner Slider')

@section('content')

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="text-sm font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="text-sm font-medium">{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 rounded-2xl shadow-xs">
            <div class="flex items-center gap-2 font-bold text-sm mb-1 text-red-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Terjadi kesalahan validasi:
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-red-700">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Banner Slider Hero Homepage</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola slide gambar dan promosi yang tampil pada carousel utama di beranda SeTiket.
            </p>
        </div>

        <button type="button" onclick="openAddModal()"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Banner Baru</span>
        </button>
    </div>

    {{-- CATATAN KETENTUAN UKURAN & PANDUAN GAMBAR (PENTING) --}}
    <div class="mb-8 rounded-2xl bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 text-white p-6 shadow-md border border-indigo-950/50 relative overflow-hidden">
        {{-- Decorative background shapes --}}
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-20 -bottom-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/30 flex items-center justify-center text-blue-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-base tracking-tight text-white">
                    Panduan & Ketentuan Ukuran Gambar Banner Slider
                </h3>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-500/30 text-blue-200 border border-blue-400/30">
                    Wajib Diperhatikan
                </span>
            </div>

            <p class="text-xs text-blue-100/90 leading-relaxed max-w-4xl mb-4">
                Agar tampilan banner slider di halaman utama terlihat tajam, presisi, dan tidak terpotong pada layar ponsel maupun desktop ultra-wide, ikuti spesifikasi berikut:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-3 border-t border-white/10 text-xs">
                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                    <span class="text-blue-300 font-bold block mb-1">📐 Rasio Aspek</span>
                    <p class="text-slate-200 font-semibold text-sm">16:9 atau 21:9</p>
                    <span class="text-[11px] text-slate-400 block mt-0.5">Format lanskap horizontal ideal untuk carousel hero.</span>
                </div>

                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                    <span class="text-blue-300 font-bold block mb-1">📏 Resolusi Ideal</span>
                    <p class="text-slate-200 font-semibold text-sm">1920 × 720 px</p>
                    <span class="text-[11px] text-slate-400 block mt-0.5">Minimal 1200 × 480 px agar tidak pecah di monitor Retina.</span>
                </div>

                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                    <span class="text-blue-300 font-bold block mb-1">📁 Format & Ukuran</span>
                    <p class="text-slate-200 font-semibold text-sm">WebP / JPG / PNG (Maks 2 MB)</p>
                    <span class="text-[11px] text-slate-400 block mt-0.5">Format WebP sangat direkomendasikan untuk kecepatan loading.</span>
                </div>

                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                    <span class="text-blue-300 font-bold block mb-1">🎯 Safe Zone Visual</span>
                    <p class="text-slate-200 font-semibold text-sm">Fokus Tengah / Kanan</p>
                    <span class="text-[11px] text-slate-400 block mt-0.5">Sisi kiri bawah akan tertimpa teks & tombol CTA di desktop.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Banner List --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Banner Aktif & Tersimpan ({{ count($banners) }})</h3>
            <span class="text-xs text-slate-500">Urutan terkecil (1, 2, ...) tampil lebih dulu di carousel</span>
        </div>

        @if(empty($banners))
            <div class="py-16 text-center text-slate-500 text-sm">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Belum ada banner slider yang dibuat. Klik tombol <strong>Tambah Banner Baru</strong> di atas untuk membuat.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($banners as $banner)
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5 hover:bg-slate-50/60 transition-colors">
                        
                        {{-- Left: Preview Thumbnail + Details --}}
                        <div class="flex flex-col sm:flex-row items-start gap-4 flex-1 min-w-0">
                            {{-- Image Preview with 16:9 ratio --}}
                            <div class="w-full sm:w-52 h-28 rounded-xl overflow-hidden bg-slate-900 border border-slate-200 shrink-0 relative group">
                                <img src="{{ $banner['image_url'] ?? asset('images/setiketbg.webp') }}"
                                    alt="{{ $banner['title'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    onerror="this.src='{{ asset('images/setiketbg.webp') }}'">
                                <span class="absolute top-2 left-2 text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ !empty($banner['is_active']) ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-slate-200' }}">
                                    {{ !empty($banner['is_active']) ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="absolute bottom-2 right-2 text-[10px] font-bold px-1.5 py-0.5 rounded bg-black/60 text-white backdrop-blur-xs">
                                    Urutan #{{ $banner['sort_order'] ?? 1 }}
                                </span>
                            </div>

                            {{-- Text Info --}}
                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 uppercase">
                                        {{ $banner['badge_text'] ?? 'Featured Event' }}
                                    </span>
                                    <h4 class="font-extrabold text-slate-900 text-base truncate">{{ $banner['title'] }}</h4>
                                    @if(isset($banner['has_overlay']) && !$banner['has_overlay'])
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Desain Penuh (No Overlay)
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($banner['subtitle']))
                                    <p class="text-xs text-slate-600 line-clamp-1">{{ $banner['subtitle'] }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 pt-1">
                                    <span class="inline-flex items-center gap-1 truncate max-w-xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-3.051a4.5 4.5 0 0 0-1.242-7.244l-4.5-4.5a4.5 4.5 0 0 0-6.364 6.364l1.757 1.757"/></svg>
                                        Target: <span class="text-slate-600 font-medium truncate">{{ $banner['target_url'] ?? '-' }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59"/></svg>
                                        Tombol: <span class="text-slate-600 font-medium">{{ $banner['button_text'] ?? 'Beli Tiketnya di Sini' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Right: Actions --}}
                        <div class="flex items-center gap-2 self-end lg:self-center shrink-0 border-t lg:border-t-0 pt-3 lg:pt-0 w-full lg:w-auto justify-end">
                            {{-- Toggle Active Status --}}
                            <form action="{{ route('admin.banners.toggle', $banner['id']) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors {{ !empty($banner['is_active']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}"
                                    title="Klik untuk mengubah status tayang">
                                    {{ !empty($banner['is_active']) ? '✓ Tayang' : 'Off (Sembunyi)' }}
                                </button>
                            </form>

                            {{-- Edit Button --}}
                            <button type="button"
                                onclick="openEditModal({{ json_encode($banner) }})"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>

                            {{-- Delete Form --}}
                            <form action="{{ route('admin.banners.destroy', $banner['id']) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner slider ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH BANNER BARU --}}
    <div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-base">Tambah Banner Slider Baru</h3>
                <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: SOUNDCHECK VOL. 1 NIGHT PARTY"
                        class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subjudul / Lokasi & Tanggal</label>
                    <input type="text" name="subtitle" placeholder="Contoh: Stadion Madya GBK • 14 November 2026"
                        class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Teks Badge Label</label>
                        <input type="text" name="badge_text" placeholder="Featured Event" value="Featured Event"
                            class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Teks Tombol CTA</label>
                        <input type="text" name="button_text" placeholder="Beli Tiketnya di Sini" value="Beli Tiketnya di Sini"
                            class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Link Target Banner (URL)</label>
                    <input type="text" name="target_url" id="addTargetUrl" placeholder="/event/nama-event atau URL lengkap"
                        class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    
                    {{-- Quick Select Event Helper --}}
                    @if(isset($activeEvents) && $activeEvents->isNotEmpty())
                        <div class="mt-1.5 flex items-center gap-1.5 text-[11px] text-slate-500">
                            <span>Pilih cepat event aktif:</span>
                            <select onchange="if(this.value) document.getElementById('addTargetUrl').value = this.value;"
                                class="text-[11px] bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 outline-none text-slate-700 max-w-xs">
                                <option value="">-- Pilih Event --</option>
                                @foreach($activeEvents as $ae)
                                    <option value="{{ route('event.show', $ae['slug'] ?? \Illuminate\Support\Str::slug($ae['nama'])) }}">
                                        {{ $ae['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Urutan Tampilan</label>
                        <input type="number" name="sort_order" min="1" value="1"
                            class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                {{-- Pilihan Mode Tampilan Overlay --}}
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="hidden" name="has_overlay" value="0">
                        <input type="checkbox" name="has_overlay" id="addHasOverlay" value="1" checked
                            class="w-4 h-4 mt-0.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Tampilkan Overlay Teks & Tombol CTA</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">
                                Aktifkan jika banner adalah background event biasa. <strong>Hilangkan centang</strong> jika banner adalah poster artwork desain penuh di mana logo dan teks sudah ada di gambar (seperti banner promosi SeTiket).
                            </span>
                        </div>
                    </label>
                </div>

                {{-- Upload Gambar Banner --}}
                <div class="pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Unggah Gambar Banner (Rasio 16:9 atau 21:9, Rekomendasi 1920×720 px, Max 2MB)
                    </label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    
                    <div class="mt-2 text-[11px] text-slate-400">
                        Atau gunakan URL gambar langsung:
                    </div>
                    <input type="text" name="image_url" placeholder="https://... atau /images/thumbnails/..."
                        class="w-full mt-1 text-xs border border-slate-200 rounded-xl px-3 py-2 outline-none">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all">
                        Simpan Banner
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT BANNER --}}
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-base">Edit Banner Slider</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="editTitle" required
                        class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subjudul / Lokasi & Tanggal</label>
                    <input type="text" name="subtitle" id="editSubtitle"
                        class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Teks Badge Label</label>
                        <input type="text" name="badge_text" id="editBadge"
                            class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Teks Tombol CTA</label>
                        <input type="text" name="button_text" id="editButton"
                            class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Link Target Banner (URL)</label>
                    <input type="text" name="target_url" id="editTargetUrl"
                        class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Urutan Tampilan</label>
                        <input type="number" name="sort_order" id="editSortOrder" min="1"
                            class="w-full text-xs sm:text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                {{-- Pilihan Mode Tampilan Overlay Edit --}}
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="hidden" name="has_overlay" value="0">
                        <input type="checkbox" name="has_overlay" id="editHasOverlay" value="1"
                            class="w-4 h-4 mt-0.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Tampilkan Overlay Teks & Tombol CTA</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">
                                Aktifkan jika banner adalah background event biasa. <strong>Hilangkan centang</strong> jika banner adalah poster artwork desain penuh di mana logo dan teks sudah ada di gambar.
                            </span>
                        </div>
                    </label>
                </div>

                {{-- Ganti Gambar Banner --}}
                <div class="pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Ganti Gambar Banner (Biarkan kosong jika tidak ingin mengganti)
                    </label>
                    <div id="editCurrentImgPreview" class="mb-2">
                        <!-- Disisipkan via JS -->
                    </div>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    
                    <div class="mt-2 text-[11px] text-slate-400">
                        Atau perbarui URL gambar:
                    </div>
                    <input type="text" name="image_url" id="editImageUrl"
                        class="w-full mt-1 text-xs border border-slate-200 rounded-xl px-3 py-2 outline-none">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all">
                        Perbarui Banner
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script Modal Kelola Banner --}}
    <script>
        function openAddModal() {
            var modal = document.getElementById('addModal');
            if (modal) {
                modal.classList.remove('hidden');
            } else {
                console.error('Modal addModal tidak ditemukan di DOM');
            }
        }

        function closeAddModal() {
            var modal = document.getElementById('addModal');
            if (modal) modal.classList.add('hidden');
        }

        function openEditModal(banner) {
            var form = document.getElementById('editForm');
            if (!form) return;
            form.action = '/admin/banners/' + banner.id;

            var titleEl = document.getElementById('editTitle');
            if (titleEl) titleEl.value = banner.title || '';
            var subEl = document.getElementById('editSubtitle');
            if (subEl) subEl.value = banner.subtitle || '';
            var badgeEl = document.getElementById('editBadge');
            if (badgeEl) badgeEl.value = banner.badge_text || 'Featured Event';
            var btnEl = document.getElementById('editButton');
            if (btnEl) btnEl.value = banner.button_text || 'Beli Tiketnya di Sini';
            var targetEl = document.getElementById('editTargetUrl');
            if (targetEl) targetEl.value = banner.target_url || '';
            var sortEl = document.getElementById('editSortOrder');
            if (sortEl) sortEl.value = banner.sort_order || 1;
            var imgEl = document.getElementById('editImageUrl');
            if (imgEl) imgEl.value = banner.image_url || '';
            var overlayEl = document.getElementById('editHasOverlay');
            if (overlayEl) {
                overlayEl.checked = (banner.has_overlay === undefined || banner.has_overlay === null) ? true : Boolean(banner.has_overlay);
            }

            var previewContainer = document.getElementById('editCurrentImgPreview');
            if (previewContainer) {
                if (banner.image_url) {
                    previewContainer.innerHTML = `
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
                            <img src="${banner.image_url}" class="w-16 h-10 object-cover rounded" onerror="this.src='/images/setiketbg.webp'">
                            <span class="text-[11px] text-slate-500 truncate">Gambar saat ini: ${banner.image_url}</span>
                        </div>
                    `;
                } else {
                    previewContainer.innerHTML = '';
                }
            }

            var editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.classList.remove('hidden');
            } else {
                console.error('Modal editModal tidak ditemukan di DOM');
            }
        }

        function closeEditModal() {
            var editModal = document.getElementById('editModal');
            if (editModal) editModal.classList.add('hidden');
        }

        // Tutup modal bila klik di area gelap di luar modal
        window.addEventListener('click', function(e) {
            var addModal = document.getElementById('addModal');
            var editModal = document.getElementById('editModal');
            if (addModal && e.target === addModal) closeAddModal();
            if (editModal && e.target === editModal) closeEditModal();
        });
    </script>
@endsection

