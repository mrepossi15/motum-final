@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'value' => ''])

<div class="relative">
    <!-- Label flotante -->
    <label for="{{ $name }}" 
           class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
        {{ $label }}
    </label>

    <!-- Input con diseño limpio y bordes dinámicos -->
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="w-full text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
        @error($name) border-red-500 @enderror"
        {{ $attributes }}
    >
    <p x-show="errors.{{ $name }}" class="text-red-500 text-sm" x-text="errors.{{ $name }}"></p>
    <!-- Botón para mostrar/ocultar contraseña -->
    @if ($type === 'password')
    <button type="button"
            class="absolute inset-y-0 right-3 top-1/2 transform -translate-y-1/2 text-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 toggle-password bg-transparent h-8 w-8 flex items-center justify-center rounded-md transition duration-200"
            data-target="{{ $name }}">
        <!-- Ícono Ojo Abierto -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 
                     8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                     7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>

        <!-- Ícono Ojo Cerrado -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-off-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round-sm" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.125-3.825M9.75 9.75a3 3 0 014.5 4.5m-6-6l-6-6m16.5 16.5l-6-6" />
        </svg>
    </button>
    @endif

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

@push('scripts')
<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            let input = document.getElementById(this.getAttribute('data-target'));
            let eyeIcon = this.querySelector(".eye-icon");
            let eyeOffIcon = this.querySelector(".eye-off-icon");

            if (input.type === "password") {
                input.type = "text";
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = "password";
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        });
    });
</script>
@endpush