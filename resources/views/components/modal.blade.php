<!-- resources/views/components/modal.blade.php -->
@props([
    'open' => false,
    'title' => '',
    'description' => '',
    'titleId' => 'modal-title',
    'textColor' => 'text-white',
    'descriptionColor' => 'text-gray-300'
])

<template x-if="{{ $open }}">
    <div x-cloak class="fixed inset-0 z-50 flex items-end justify-center md:items-center">
        <!-- Fondo oscuro -->
        <div
            class="absolute inset-0 bg-black bg-opacity-50"
            @click="{{ $open }} = false"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <!-- Contenido del modal -->
        <div
            class="bg-[#1E1E1E] p-6 rounded-lg w-full max-w-md shadow-lg relative z-10 transform transition-all duration-300 ease-in-out"
            x-transition:enter="translate-y-full opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-full opacity-0"
            role="dialog"
            aria-modal="true"
            aria-labelledby="{{ $titleId }}"
        >
            <!-- Swipe bar -->
            <div class="h-1 w-12 bg-gray-500 rounded-full mx-auto mb-3 md:hidden"></div>

            <!-- Botón cerrar -->
            <button @click="{{ $open }} = false" class="absolute top-3 right-3 text-white hover:text-red-500" aria-label="Cerrar modal">
                <x-lucide-x class="w-6 h-6" />
            </button>

            <!-- Título -->
            <h2 id="{{ $titleId }}" class="text-lg font-bold mb-4 text-center {{ $textColor }}">{{ $title }}</h2>

            <!-- Descripción -->
            <p class="mb-4 text-center {{ $descriptionColor }}">{{ $description }}</p>

            <!-- Contenido extra -->
            {{ $slot }}
        </div>
    </div>
</template>
