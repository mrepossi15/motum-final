<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Trainer App')</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://unpkg.com/lucide@latest"></script>
  
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    @if (!isset($hideNavbar) || !$hideNavbar)
        <header>
            <x-nav></x-nav>
        </header>
    @endif

    <main class="{{ $background ?? 'bg-gray-100' }} min-h-screen">
        @if (session()->has('feedback.message'))
            <div class="alert alert-{{ session()->get('feedback.type', 'success') }}">
                {!! session()->get('feedback.message') !!}
            </div>
        @endif

        <div class="{{ isset($hideNavbar) && $hideNavbar ? '' : '' }}">
            @yield('content')
        </div>
        <!-- 📸 Modal de Imágenes (Disponible en toda la app) -->
            <div x-data="{ showModal: false, activeImage: '', prev: () => {}, next: () => {} }"
                x-show="showModal"
                class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center p-4 sm:p-8 hidden"
                @keydown.window.escape="showModal = false">

                <div class="relative w-full max-w-4xl mx-auto shadow-lg rounded-lg bg-white overflow-hidden" 
                    @click.away="showModal = false">

                    <!-- ❌ Botón de Cerrar -->
                    <button class="absolute top-4 right-4 text-white bg-black p-2 rounded-full focus:outline-none z-50" 
                            type="button" 
                            @click="showModal = false">
                        <x-lucide-x class="w-6 h-6 text-white" />
                    </button>

                    <!-- 📸 Imagen -->
                    <div class="relative flex items-center justify-center w-full">
                        <img :src="activeImage" alt="Imagen ampliada" 
                            class="w-full max-h-[80vh] object-contain rounded-lg sm:rounded-xl">

                        <!-- 🔄 Navegación (Botones Anterior/Siguiente) -->
                        <button class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white p-3 rounded-full shadow-md hover:bg-gray-200"
                                @click="prev()">
                            <x-lucide-chevron-left class="w-6 h-6 text-orange-500" />
                        </button>
                        <button class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white p-3 rounded-full shadow-md hover:bg-gray-200"
                                @click="next()">
                            <x-lucide-chevron-right class="w-6 h-6 text-orange-500" />
                        </button>
                    </div>
                </div>
            </div>

<!-- 📜 Script para manejar el modal global -->

    </main>

    <footer></footer>
    @stack('scripts')
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let modal = document.querySelector("[x-data]");
        let images = [];

        function openModal(index) {
            modal.__x.$data.showModal = true;
            modal.__x.$data.activeImage = images[index];
            modal.__x.$data.prev = () => {
                modal.__x.$data.activeImage = images[(index - 1 + images.length) % images.length];
                index = (index - 1 + images.length) % images.length;
            };
            modal.__x.$data.next = () => {
                modal.__x.$data.activeImage = images[(index + 1) % images.length];
                index = (index + 1) % images.length;
            };
        }

        document.querySelectorAll("[data-modal-image]").forEach((img, index) => {
            images.push(img.getAttribute("data-modal-image"));
            img.addEventListener("click", () => openModal(index));
        });
    });
</script>

</body>
</html>
