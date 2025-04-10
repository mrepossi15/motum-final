@props(['name', 'label', 'options' => [], 'selected' => null])

<div class="relative mb-2">
    <!-- Label flotante -->
    <label for="{{ $name }}" 
           class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-600 text-sm">
        {{ $label }}
    </label>
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="w-full text-black border hover:border-orange-500 border-gray-500 rounded-md px-4 py-2 pr-12 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 appearance-none
            @error($name) border-red-500 @enderror"
            {{ $attributes }}
            style="padding-right: 2.5rem;" <!-- Más espacio para la flecha -->
        >
            <option value="" disabled {{ !$selected ? 'selected' : '' }}>Seleccionar</option>
            @foreach ($options as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <!-- Flecha con Lucide Icon -->
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
            <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500"></i>
        </div>


    <!-- Mensaje de error con ícono de advertencia -->
    @error($name)
        <div class="flex items-center mt-1 text-red-500 text-xs">
            <!-- Ícono de advertencia usando Lucide Icons -->
            <i data-lucide="alert-circle" class="w-5 h-5 mr-1"></i>
            <p>⚠️ {{ $message }}</p>
        </div>
    @enderror
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>