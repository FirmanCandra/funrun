@extends('admin.layouts.admin')

@section('header_title', 'Edit Participant')

@section('content')
<div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-lg">Edit Participant Data</h3>
        <p class="text-sm text-slate-500">Update biodata and ticket category.</p>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.participants.update', $participant->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="fullname" value="{{ old('fullname', $participant->fullname) }}" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number (WA)</label>
                <input type="text" name="phone" value="{{ old('phone', $participant->phone) }}" required class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select name="category" required class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->code }}" {{ $participant->category == $cat->code ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jersey Size</label>
                    <select name="jersey_size" required class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="S" {{ $participant->jersey_size == 'S' ? 'selected' : '' }}>S</option>
                        <option value="M" {{ $participant->jersey_size == 'M' ? 'selected' : '' }}>M</option>
                        <option value="L" {{ $participant->jersey_size == 'L' ? 'selected' : '' }}>L</option>
                        <option value="XL" {{ $participant->jersey_size == 'XL' ? 'selected' : '' }}>XL</option>
                        <option value="XXL" {{ $participant->jersey_size == 'XXL' ? 'selected' : '' }}>XXL</option>
                    </select>
                </div>
            </div>

            @if($participant->user)
            <div class="border-t border-slate-100 pt-5 mt-3">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">Akun Login Peserta</h4>
                        <p class="text-xs text-slate-500">Kredensial akun untuk login ke SeTiket.</p>
                    </div>
                    @if(!empty($participant->user->plain_password))
                        <span class="text-xs bg-slate-100 px-2.5 py-1 rounded-md text-slate-600">
                            Password: <strong class="font-mono text-slate-800">{{ $participant->user->plain_password }}</strong>
                        </span>
                    @else
                        <span class="text-xs bg-slate-100 px-2.5 py-1 rounded-md text-slate-400 italic">
                            Password: [Terenkripsi Bcrypt]
                        </span>
                    @endif
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email Akun</label>
                        <input type="email" name="email" value="{{ old('email', $participant->user->email) }}" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-500 mt-1">Ubah jika peserta salah ketik email saat registrasi.</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-slate-700">Password Baru</label>
                            <button type="button" onclick="const num = Math.floor(1000 + Math.random() * 9000); document.getElementById('edit-password-input').value = 'Setiket' + num;" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Generate Acak
                            </button>
                        </div>
                        <input type="text" id="edit-password-input" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full border border-slate-200 rounded-lg px-4 py-2 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            @endif

            <div class="pt-4 flex flex-col sm:flex-row gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Save Changes
                </button>
                <a href="{{ route('admin.participants') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2 rounded-lg font-medium transition-colors text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Jawaban pertanyaan tambahan milik event ini (hanya baca) --}}
@if($customFields->isNotEmpty())
    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-6">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-lg">Jawaban Pertanyaan Tambahan</h3>
            <p class="text-sm text-slate-500">
                Pertanyaan khusus event ini, diatur di
                <a href="{{ route('admin.form-fields', ['event_id' => $participant->event_id]) }}"
                    class="text-blue-600 hover:underline">Formulir Pendaftaran</a>.
            </p>
        </div>
        <dl class="divide-y divide-slate-100">
            @foreach($customFields as $field)
                <div class="px-6 py-4 flex flex-wrap justify-between gap-3">
                    <dt class="text-sm text-slate-500">{{ $field->label }}</dt>
                    <dd class="text-sm font-medium text-slate-800 text-right">{{ $participant->customAnswer($field) }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
@endif
@endsection
