{{--
    Render satu field peserta pada form pembelian tiket.

    Variabel yang diharapkan:
    - $field : App\Models\EventFormField
    - $i     : indeks peserta (participants[$i][...])
    - $row   : nilai old() untuk peserta ini
--}}
@php
    use App\Models\EventFormField;

    // Field bawaan menempel langsung ke kolom participants, field tambahan
    // dikelompokkan di bawah [custom] agar tidak bertabrakan namanya.
    $name = $field->is_core
        ? "participants[{$i}][{$field->key}]"
        : "participants[{$i}][custom][{$field->key}]";

    $value = $field->is_core
        ? ($row[$field->key] ?? '')
        : ($row['custom'][$field->key] ?? '');

    $inputClass = 'w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all';
    $selectClass = 'w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all';
@endphp

<div class="{{ $field->isFullWidth() ? 'md:col-span-2' : '' }}">
    @if ($field->type === EventFormField::TYPE_CONSENT)

        <label class="flex items-start gap-3 cursor-pointer bg-slate-900/30 border border-white/10 rounded-xl p-4">
            {{-- Hidden 0 supaya checkbox yang tidak dicentang tetap terkirim. --}}
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" name="{{ $name }}" value="1" {{ $value ? 'checked' : '' }}
                {{ $field->required ? 'required' : '' }}
                class="mt-0.5 rounded border-white/20 bg-slate-900 text-amber-500 focus:ring-amber-500/50">
            <span class="text-sm text-gray-300">
                {{ $field->label }}
                @if ($field->required)<span class="text-amber-400">*</span>@endif
                @if ($field->help_text)
                    <span class="block text-xs text-gray-500 mt-1">{{ $field->help_text }}</span>
                @endif
            </span>
        </label>

    @else

        <label class="block text-sm font-medium text-gray-300 mb-2">
            {{ $field->label }}
            @if ($field->required)<span class="text-amber-400">*</span>@endif
        </label>

        @if ($field->is_core && isset(EventFormField::SELECT_OPTIONS[$field->key]))
            <select name="{{ $name }}" {{ $field->required ? 'required' : '' }} class="{{ $selectClass }}">
                @unless ($field->required)
                    <option value="">— Pilih —</option>
                @endunless
                @foreach (EventFormField::SELECT_OPTIONS[$field->key] as $optValue => $optLabel)
                    <option value="{{ $optValue }}" {{ (string) $value === (string) $optValue ? 'selected' : '' }}>
                        {{ $optLabel }}
                    </option>
                @endforeach
            </select>

        @elseif ($field->type === EventFormField::TYPE_TEXTAREA)
            <textarea name="{{ $name }}" rows="2" {{ $field->required ? 'required' : '' }}
                placeholder="{{ $field->placeholder }}" class="{{ $inputClass }}">{{ $value }}</textarea>

        @elseif ($field->type === EventFormField::TYPE_DATE)
            <input type="date" name="{{ $name }}" value="{{ $value }}" {{ $field->required ? 'required' : '' }}
                class="{{ $inputClass }}">

        @elseif ($field->type === EventFormField::TYPE_NUMBER)
            <input type="number" step="any" name="{{ $name }}" value="{{ $value }}"
                {{ $field->required ? 'required' : '' }} placeholder="{{ $field->placeholder }}"
                class="{{ $inputClass }}">

        @else
            <input type="text" name="{{ $name }}" value="{{ $value }}" {{ $field->required ? 'required' : '' }}
                placeholder="{{ $field->placeholder }}" class="{{ $inputClass }}">
        @endif

        @if ($field->help_text)
            <p class="text-xs text-gray-500 mt-1">{{ $field->help_text }}</p>
        @endif

    @endif
</div>
