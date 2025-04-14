@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'value' => ''])

@php
    $isPassword = $type === 'password';
  
@endphp

<div class="relative" @if($isPassword) x-data="{ show: false }" @endif>
    @if($label)
        <label for="{{ $name }}" 
            class="absolute top-0 left-3 -mt-2 px-1 bg-white text-gray-700 text-sm {{ $attributes->get('label-hidden') ? 'hidden' : '' }}">
            {{ $label }}
        </label>
    @endif
    
   <input
    :type="show ? 'text' : 'password'"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        class="w-full text-black border px-4 py-3 hover:border-orange-500 border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
        @error($name) border-red-500 @enderror"
        {{ $attributes }}
        >
        <p x-show="errors.{{ $name }}" class="text-red-500 text-sm" x-text="errors.{{ $name }}"></p>

    @if ($isPassword)
        <button type="button"
                @click="show = !show"
                class="absolute inset-y-0 right-3 top-1/2 transform -translate-y-1/2 text-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 bg-transparent h-8 w-8 flex items-center justify-center rounded-md transition duration-200">
            <template x-if="!show">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 
                             8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                             7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </template>
            <template x-if="show">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round-sm" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.125-3.825M9.75 9.75a3 3 0 014.5 4.5m-6-6l-6-6m16.5 16.5l-6-6" />
                </svg>
            </template>
        </button>
    @endif

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