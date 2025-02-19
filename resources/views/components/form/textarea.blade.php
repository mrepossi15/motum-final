<div>
    <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <textarea
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        rows="{{ $rows ?? 3 }}"
        placeholder="{{ $placeholder ?? '' }}"
        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500  @error($name) border-red-500 @enderror"
        {{ $attributes }}
    >{{ old($name, $value ?? '') }}</textarea>

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
