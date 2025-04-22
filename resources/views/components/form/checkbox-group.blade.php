@props(['name', 'label' => null, 'options' => [], 'selected' => [], 'hideLabel' => false])

<div class="relative" x-data="{ selectedOptions: @json((array) old($name, $selected)) }">
    @if (!$hideLabel)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
        @foreach ($options as $option)
            <label 
                class="cursor-pointer border rounded-lg p-2 text-center font-medium transition
                       hover:border-orange-400 hover:bg-orange-50"
                :class="{ 
                    'border-orange-500 bg-orange-100 text-orange-700': selectedOptions.includes('{{ $option }}'),
                    'border-gray-300 text-gray-700': !selectedOptions.includes('{{ $option }}')
                }"
                @click.prevent="
                    if (selectedOptions.includes('{{ $option }}')) {
                        selectedOptions = selectedOptions.filter(o => o !== '{{ $option }}')
                    } else {
                        selectedOptions.push('{{ $option }}')
                    }
                "
            >
                {{ $option }}
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $option }}"
                    class="hidden"
                    :checked="selectedOptions.includes('{{ $option }}')"
                >
            </label>
        @endforeach
    </div>

    @error($name)
        <div class="flex items-center mt-1 text-red-500 text-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m-2-2a9 9 0 110-18 9 9 0 010 18z" />
            </svg>
            <p>⚠️ {{ $message }}</p>
        </div>
    @enderror
</div>