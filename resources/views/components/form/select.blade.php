@props(['name', 'label', 'options' => [], 'selected' => null])

<div class="relative mb-6">
    <!-- Label flotante -->
    <label for="{{ $name }}" 
           class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
        {{ $label }}
    </label>

    <!-- Select con estilos dinámicos -->
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        class="w-full bg-white text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
        @error($name) border-red-500 @enderror"
        {{ $attributes }}
    >
        <option value="" disabled {{ !$selected ? 'selected' : '' }}>Seleccionar...</option>
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

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