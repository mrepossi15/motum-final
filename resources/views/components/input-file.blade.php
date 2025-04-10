@props(['name', 'label' => 'Cargar imagen'])

<div x-data="{ isHovering: false, preview: null }" class="relative w-full mb-4">
    <label 
        for="{{ $name }}" 
        class="block w-full h-60 border-2 border-dashed rounded-lg cursor-pointer transition
               flex flex-col items-center justify-center
               bg-orange-50 hover:bg-orange-100 
               border-orange-500 hover:border-orange-600
               text-orange-600"
        @dragover.prevent="isHovering = true"
        @dragleave.prevent="isHovering = false"
        @drop.prevent="isHovering = false"
        :class="{ 'bg-orange-100': isHovering, 'hidden': preview }"
    >
        <input 
            type="file" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            accept="image/*" 
            class="hidden"
            @change="fileChosen"
            {{ $attributes }}
        >

        <!-- Icono y Texto -->
        <div class="flex flex-col items-center space-y-2">
            <div class="w-10 h-10 rounded-full border-2 border-orange-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="font-bold text-orange-600">{{ $label }}</span>
        </div>
    </label>

    <!-- Vista Previa de la Imagen -->
    <div x-show="preview" class="relative mt-4 flex items-center justify-center">
        <img :src="preview" alt="Preview" class="w-full h-60 object-cover rounded-lg border border-orange-500">
        <!-- Botón para eliminar la imagen cargada -->
        <button type="button" 
                @click="preview = null; document.getElementById('{{ $name }}').value = ''"
                class="absolute top-1 right-1 bg-white text-red-500 rounded-full p-1 shadow-md hover:bg-red-500 hover:text-white transition">
            ✖
        </button>
    </div>
</div>

@push('scripts')
<script>
    function fileChosen(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush