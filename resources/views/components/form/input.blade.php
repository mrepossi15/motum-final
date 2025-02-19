@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'value' => ''])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <div class="relative">
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500 @error($name) border-red-500 @enderror"
            {{ $attributes }}
        >

        @if ($type === 'password')
        <button type="button"
                class="absolute inset-y-0 right-3 top-1/2 transform -translate-y-1/2 text-gray-500 focus:outline-none toggle-password bg-transparent h-8 w-8 flex items-center justify-center rounded-md"
                data-target="{{ $name }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 
                         8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                         7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
        @endif
    </div>

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
