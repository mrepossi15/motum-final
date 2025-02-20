@props(['name', 'label', 'options' => [], 'checked' => null])

<div>
    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="flex flex-wrap gap-2 mt-1">
        @foreach ($options as $value => $label)
            <label class="flex items-center">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    {{ $checked == $value ? 'checked' : '' }}
                    class="h-4 w-4 text-orange-500 focus:ring-orange-500"
                >
                <span class="ml-2">{{ $label }}</span>
            </label>
        @endforeach
    </div>

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>