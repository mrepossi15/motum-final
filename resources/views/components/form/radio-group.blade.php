@props(['name', 'label', 'options' => [], 'checked' => null])

<div class="relative ">
    <!-- Label flotante -->
    <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
        {{ $label }}
    </label>

    <!-- Contenedor de radios -->
    <div class="flex flex-wrap gap-2  ">
        @foreach ($options as $value => $label)
            <label class="flex items-center border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3 focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500
                @error($name) border-red-500 @enderror">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    {{ old($name, $checked) == $value ? 'checked' : '' }}
                    class="h-4 w-4 text-orange-500 focus:ring-orange-500"
                >
                <span class="ml-2 text-black">{{ $label }}</span>
            </label>
        @endforeach
    </div>

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