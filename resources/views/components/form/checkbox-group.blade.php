@props(['name', 'label', 'options' => [], 'selected' => []])

<div>
    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="flex flex-wrap gap-2 mt-1">
        @foreach ($options as $option)
            <label class="flex items-center">
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $option }}"
                    {{ in_array($option, $selected) ? 'checked' : '' }}
                    class="h-4 w-4 text-orange-500 focus:ring-orange-500"
                >
                <span class="ml-2">{{ $option }}</span>
            </label>
        @endforeach
    </div>

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>