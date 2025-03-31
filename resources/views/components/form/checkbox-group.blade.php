@props(['name', 'label' => null, 'options' => [], 'selected' => [], 'hideLabel' => false])

<div class="relative ">
    <!-- Label flotante (oculto si `hideLabel` es true) -->
    @if (!$hideLabel)
        <label class="absolute -top-2 left-3 bg-gray-50 px-1 text-black text-sm">
            {{ $label }}
        </label>
    @endif

    <!-- Contenedor de checkboxes en una grilla -->
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
        @foreach ($options as $option)
            <label class="flex items-center gap-2  rounded-md hover:bg-gray-100 cursor-pointer transition">
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $option }}"
                    {{ in_array($option, (array) old($name, $selected)) ? 'checked' : '' }}
                    class="h-5 w-5 bg-gray-50 text-orange-500 focus:ring-orange-500"
                >
                <span class="text-black">{{ $option }}</span>
            </label>
        @endforeach
    </div>
    <p x-show="errors['{{ $name }}']" class="text-red-500 text-sm" x-text="errors['{{ $name }}']"></p>
    <!-- Mensaje de error con ícono de advertencia -->
    @error($name)
        <div class="flex items-center mt-1 text-red-500 text-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m-2-2a9 9 0 110-18 9 9 0 010 18z" />
            </svg>
            <p>⚠️ {{ $message }}</p>
        </div>
    @enderror
</div>