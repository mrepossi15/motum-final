@props(['name', 'label' => 'Subir Imagen'])

<div 
    x-data="{ 
        preview: null, 
        fileChosen(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.preview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        removeImage() {
            this.preview = null;
            this.$refs.input.value = null;
        }
    }" 
    class="relative mb-6"
>
    <!-- Label flotante -->
    <label for="{{ $name }}" 
           class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
        {{ $label }}
    </label>

    <!-- Cuadro para Subir Imagen (Cuando no hay preview) -->
    <label 
        for="{{ $name }}" 
        class="block w-full h-60 border hover:border-orange-500 border-gray-500 rounded-md cursor-pointer transition 
               focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
               flex items-center justify-center overflow-hidden
               @error($name) border-red-500 @enderror"
        x-show="!preview"
    >
        <input 
            type="file" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            accept="image/*" 
            class="hidden"
            @change="fileChosen"
            x-ref="input"
        >

        <!-- Ícono y Texto -->
        <div class="flex flex-col items-center space-y-2">
            <div class="w-10 h-10 rounded-full border border-orange-500 flex items-center justify-center">
                <i data-lucide="plus" class="w-6 h-6 text-orange-500"></i>
            </div>
            <span class="text-orange-500">Cargar Imagen</span>
        </div>
    </label>

    <!-- Vista Previa de la Imagen -->
    <div x-show="preview" class="relative w-full h-60 border hover:border-orange-500 border-gray-500 rounded-md overflow-hidden cursor-pointer transition mb-4">
        <label for="{{ $name }}" class="block w-full h-full">
            <img :src="preview" alt="Vista Previa" class="w-full h-full object-cover">
        </label>
        
        <!-- Botón para eliminar la imagen cargada -->
        <button type="button" 
            @click="removeImage()"
            class="absolute top-1 right-1 bg-white text-red-500 rounded-full p-1 shadow-md hover:bg-red-500 hover:text-white transition">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush