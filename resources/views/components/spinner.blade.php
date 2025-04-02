<div x-data="{ isLoading: @entangle($attributes->wire('model')) }"
     x-show="isLoading"
     class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50"
     style="display: none;"
>
    <div class="flex flex-col items-center justify-center space-y-2">
        <h1 class="text-orange-500 font-semibold italic text-2xl">motum</h1>
        <p class="text-white text-xl mb-4">{{ $message ?? 'Creando el usuario...' }}</p>
        <!-- Spinner -->
        <div class="loader"></div>
    </div>
</div>

<!-- Spinner CSS -->
<style>
    .loader {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #F97316; /* Naranja */
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>