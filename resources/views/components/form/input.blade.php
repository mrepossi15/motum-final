

@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'value' => '', 'required' => false])

@php
    $isPassword = $type === 'password';
@endphp
@props(['name', 'label', 'required' => false])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" 
            class="block text-sm text-gray-700 mb-1 {{ $attributes->get('label-hidden') ? 'hidden' : '' }}">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
        
    @endif

    <div class="relative" @if($isPassword) x-data="{ show: false }" @endif>
        <input
            :type="show ? 'text' : 'password'"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class="w-full text-black border  px-4 py-3  pr-10 hover:border-orange-500 border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
            @error($name) border-red-500 @enderror"
            {{ $attributes }}
        >

        @if ($isPassword)
            <button type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-3 flex items-center justify-center text-gray-500 h-full">
                <template x-if="!show">
                    <x-lucide-eye class="w-5 h-5" />
                </template>
                <template x-if="show">
                    <x-lucide-eye-off class="w-5 h-5" />
                </template>
            </button>
        @endif
    </div>

    {{-- Error desde Alpine --}}
    <p x-show="errors.{{ $name }}" class="text-red-500 text-sm mt-1" x-text="errors.{{ $name }}"></p>

    {{-- Error desde Laravel --}}
    @error($name)
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>