@props(['name', 'label', 'accept' => 'image/*'])

<div class="relative">
    <label for="{{ $name }}" class="block text-gray-700 font-bold mb-2">
        {{ $label }}
    </label>

    <input 
        type="file" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        accept="{{ $accept }}" 
        class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300"
        @change="previewImage(event, '{{ $name }}')">

    <div class="mt-2" x-data="{ imageUrl: '' }" x-show="imageUrl">
        <img :src="imageUrl" class="w-24 h-24 object-cover rounded-md border">
    </div>

    @error($name)
        <p class="text-red-500 text-xs mt-1">⚠️ {{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
    function previewImage(event, inputName) {
        let input = event.target;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector(`[x-data][x-show][x-init="imageUrl"]`).__x.$data.imageUrl = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush