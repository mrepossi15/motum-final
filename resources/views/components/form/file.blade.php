<div x-show="step === 4" class="space-y-4">
    <div class="relative">
        <!-- Label flotante -->
        <label for="photos" 
               class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
               {{ $label }}
        </label>

        <!-- Input con diseño limpio y bordes dinámicos -->
        <input
            type="file"
            id="photos"
            name="photos[]"
            accept="image/*"
            multiple
            class="w-full bg-gray-50 text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
            @error('photos') border-red-500 @enderror"
        >

        <!-- Mensaje de error con ícono de advertencia -->
        @error('photos')
            <div class="flex items-center mt-1 text-red-500 text-xs">
                <!-- Ícono de advertencia -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m-2-2a9 9 0 110-18 9 9 0 010 18z" />
                </svg>
                <p>⚠️ {{ $message }}</p>
            </div>
        @enderror
    </div>
</div>