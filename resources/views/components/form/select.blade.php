@props(['name', 'label', 'options' => [], 'selected' => null, 'placeholder' => 'Seleccionar','required' => false])

<div class="w-full">
    {{-- Label superior --}}
    <label for="{{ $name }}" 
       class="{{ $attributes->get('label-hidden') ? 'sr-only' : 'block text-sm text-gray-700 mb-1' }}">
       {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
</label>

    <div class="relative">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="w-full border px-4 py-3 pr-10 hover:border-orange-500 border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 appearance-none
            @error($name) border-red-500 @enderror"
            style="color: {{ !$selected ? '#9CA3AF' : '#000000' }};" {{-- gray-400 o black --}}
            onchange="this.style.color = this.value ? '#000000' : '#9CA3AF';"
            {{ $attributes }}
        >
            <option value="" disabled {{ !$selected ? 'selected' : '' }}>{{ $placeholder }}</option>
            @foreach ($options as $value => $optionLabel)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>

        {{-- Flechita Lucide --}}
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
            <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500"></i>
        </div>
    </div>


    @error($name)
        <div class="flex items-center mt-2 text-red-500 text-sm">
            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
            <p>{{ $message }}</p>
        </div>
    @enderror
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endpush