@props(['name', 'label', 'options' => [], 'selected' => null])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500 @error($name) border-red-500 @enderror"
        {{ $attributes }}
    >
        <option value="" disabled {{ !$selected ? 'selected' : '' }}>Seleccionar...</option>
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>