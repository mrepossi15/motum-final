@props(['name', 'label', 'rows' => 3, 'placeholder' => '', 'value' => ''])

<div class="relative ">
    <!-- Label flotante -->
    <label for="{{ $name }}" 
           class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-600 text-sm">
        {{ $label }}
    </label>

    <!-- Textarea con bordes dinámicos -->
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="w-full text-black border hover:border-orange-500 border-gray-500 rounded-md px-4 py-2 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
        @error($name) border-red-500 @enderror"
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>

    <!-- Mensaje de error con ícono de advertencia -->
    @error($name)
        <div class="flex items-center mt-1 text-red-500 text-xs">
            <!-- Ícono de advertencia -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m-2-2a9 9 0 110-18 9 9 0 010 18z" />
            </svg>
            <p>⚠️ {{ $message }}</p>
        </div>
    @enderror
</div>