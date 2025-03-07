@props(['name', 'label', 'existingPhoto' => null, 'inputStyle' => ''])

<div x-data="photoPreview()" x-init="loadExistingPhoto('{{ $existingPhoto }}')" class="space-y-2">
    <label for="{{ $name }}"  class="block font-medium text-gray-700">{{ $label }}</label>
    
    <input type="file" id="{{ $name }}" name="{{ $name }}" accept="image/*" @change="previewImage(event)" 
        class="w-full bg-gray-50 text-black border border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error($name) border-red-500 @enderror">

    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror

    <div class="mt-2">
        <template x-if="photo">
            <div class="relative w-32 h-32 rounded-md overflow-hidden shadow-md border border-gray-300">
                <img :src="photo" class="w-full h-full object-cover">
                <button type="button" @click="removeImage()" class="absolute top-1 right-1 w-6 h-6 flex items-center justify-center bg-white rounded-full shadow-md">
                    <x-lucide-square-x class="h-4 w-4 text-red-500" />
                </button>
            </div>
        </template>
    </div>
</div>