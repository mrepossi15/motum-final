<div>
    <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <input
        type="file"
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        accept="{{ $accept ?? 'image/*' }}"
        class="w-full px-3 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500 @error($name) border-red-500 @enderror"
        {{ $attributes }}
    >

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
