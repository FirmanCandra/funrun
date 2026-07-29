@extends('admin.layouts.admin')

@section('header_title', 'Gambar Event')

@section('content')

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Konteks event --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 sm:p-6 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="min-w-0">
        <h3 class="font-bold text-lg text-slate-800 truncate">{{ $event->title }}</h3>
        <p class="text-xs text-slate-500 mt-1">
            Gambar ini yang dilihat calon peserta di halaman depan, halaman detail event,
            dan halaman pembelian tiket.
        </p>
    </div>

    @if(auth()->user()->isSuperAdmin())
        <form method="GET" action="{{ route('admin.event-image') }}" class="flex items-center gap-2 shrink-0">
            <label class="text-sm text-slate-600 whitespace-nowrap">Event:</label>
            <select name="event_id" onchange="this.form.submit()"
                class="w-full md:w-auto border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ $ev->id === $event->id ? 'selected' : '' }}>{{ $ev->title }}</option>
                @endforeach
            </select>
        </form>
    @else
        <span class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg font-medium whitespace-nowrap self-start md:self-auto">
            Event yang Anda tangani
        </span>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Pratinjau seperti yang dilihat peserta --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Tampilan di halaman peserta</h3>
            <p class="text-xs text-slate-500 mt-1">
                Kotak di bawah memakai perbandingan 16:9 — sama persis dengan kartu event di halaman depan.
            </p>
        </div>

        <div class="p-5 sm:p-6">
            <div class="rounded-xl overflow-hidden border border-slate-200">
                {{-- Pratinjau berkas yang baru dipilih, menggantikan gambar lama sebelum disimpan --}}
                <img id="preview-baru" src="" alt="Pratinjau gambar baru" class="media-16-9 hidden">

                <div id="preview-lama">
                    @include('partials.event-image', [
                        'nama' => $event->title,
                        'thumbnail' => $thumbnail,
                    ])
                </div>
            </div>

            @if($thumbnail)
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ $thumbnail }}" target="_blank"
                        class="text-sm text-blue-600 hover:underline font-medium">Buka gambar ukuran penuh</a>

                    <form method="POST" action="{{ route('admin.event-image.destroy') }}"
                        onsubmit="return confirm('Hapus gambar event ini? Kartu event akan kembali memakai tampilan cadangan.')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <button type="submit"
                            class="text-sm font-medium text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                            Hapus Gambar
                        </button>
                    </form>
                </div>
            @else
                <p class="mt-4 text-xs text-slate-500">
                    Event ini belum punya gambar, jadi yang tampil adalah panel berisi inisial nama event.
                    Unggah gambar di sebelah untuk menggantinya.
                </p>
            @endif
        </div>
    </div>

    {{-- Formulir unggah --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">{{ $thumbnail ? 'Ganti Gambar' : 'Unggah Gambar' }}</h3>
            <p class="text-xs text-slate-500 mt-1">
                Format JPG, PNG, atau WebP dengan ukuran berkas maksimal 1 MB.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.event-image.update') }}" enctype="multipart/form-data"
            class="p-5 sm:p-6 space-y-5">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div>
                <label for="thumbnail"
                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                    Berkas gambar *
                </label>
                <input type="file" id="thumbnail" name="thumbnail" required accept="image/jpeg,image/png,image/webp"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none transition file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p id="pesan-berkas" class="text-xs text-slate-500 mt-2"></p>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-800 space-y-1.5">
                <p class="font-semibold">Supaya gambar tidak terpotong</p>
                <p>
                    Kartu event memotong gambar ke perbandingan 16:9 (contoh ukuran pas: 1280 × 720 piksel).
                    Taruh logo dan tulisan penting di tengah, jangan di pinggir atas atau bawah.
                </p>
            </div>

            <button type="submit"
                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
                Simpan Gambar
            </button>
        </form>
    </div>
</div>

<script>
    // Pratinjau lokal sebelum berkas diunggah, plus penjagaan ukuran di sisi
    // peramban supaya admin tidak menunggu unggahan hanya untuk ditolak server.
    (function () {
        var input = document.getElementById('thumbnail');
        var previewBaru = document.getElementById('preview-baru');
        var previewLama = document.getElementById('preview-lama');
        var pesan = document.getElementById('pesan-berkas');
        var BATAS = 1024 * 1024;

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];

            if (!file) {
                previewBaru.classList.add('hidden');
                previewLama.classList.remove('hidden');
                pesan.textContent = '';
                return;
            }

            if (file.size > BATAS) {
                input.value = '';
                previewBaru.classList.add('hidden');
                previewLama.classList.remove('hidden');
                pesan.className = 'text-xs text-red-600 mt-2';
                pesan.textContent = 'Ukuran berkas ' + (file.size / 1024 / 1024).toFixed(2) +
                    ' MB, melebihi batas 1 MB. Perkecil gambarnya dulu.';
                return;
            }

            previewBaru.src = URL.createObjectURL(file);
            previewBaru.classList.remove('hidden');
            previewLama.classList.add('hidden');
            pesan.className = 'text-xs text-slate-500 mt-2';
            pesan.textContent = 'Pratinjau di sebelah adalah gambar baru. Klik "Simpan Gambar" untuk menerapkannya.';
        });
    })();
</script>
@endsection
