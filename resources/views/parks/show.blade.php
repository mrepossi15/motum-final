
@extends('layouts.main')

@section('title', 'Detalle del Entrenamiento')

@section('content')

@php
    use Illuminate\Support\Str;
@endphp
@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" aria-label="Close">✖</button>
    </div>
@endif
<div class="flex justify-center min-h-screen text-black">
    <div class="w-full mb-10"> <!-- MODIFICADO: Márgenes dinámicos según dispositivo -->
        <!-- Contenido principal -->
        <div class="relative mx-auto lg:px-[25%] w-full"> 
            <!-- Carrusel de Fotos del Parque -->
            @if ($park->photo_urls)
                @if (!empty($photos))
                    <div x-data="{ 
                                        activeSlide: 0, 
                                        slides: {{ json_encode($photos) }},
                                        showModal: false,
                                        next() { 
                                            this.activeSlide = (this.activeSlide + 1) % this.slides.length 
                                        },
                                        prev() { 
                                            this.activeSlide = this.activeSlide === 0 ? this.slides.length - 1 : this.activeSlide - 1 
                                        }
                                    }" class="relative w-full my-3">
                                    

                                    <!-- 🖥️ Modo Computadora (Grid de 2 columnas) -->
                                    <div class="hidden lg:grid grid-cols-10 gap-2">
                                        <!-- 📸 Imagen principal (70%) -->
                                        <div class="col-span-7">
                                            <img src="{{ asset($photos[0]) }}"
                                                alt="Foto principal de {{ $park->name }}"
                                                class="w-full h-[350px] object-cover cursor-pointer"
                                                @click="showModal = true; activeSlide = 0">
                                        </div>

                                        <!-- 📸 Columna derecha (30%) -->
                                        <div class="col-span-3 grid grid-rows-2 gap-2">
                                            <!-- 🖼️ Primera fila (1 imagen) -->
                                            <div>
                                                @if(isset($photos[1]))
                                                    <img src="{{ asset($photos[1]) }}"
                                                        alt="Foto secundaria"
                                                        class="w-full h-[170px] object-cover  cursor-pointer"
                                                        @click="showModal = true; activeSlide = 1">
                                                @endif
                                            </div>

                                            <!-- 🖼️ Segunda fila (2 imágenes en columnas) -->
                                            <div class="grid grid-cols-2 gap-2">
                                                @if(isset($photos[2]))
                                                    <img src="{{ asset($photos[2]) }}"
                                                        alt="Foto adicional"
                                                        class="w-full h-[170px] object-cover  cursor-pointer"
                                                        @click="showModal = true; activeSlide = 2">
                                                @endif

                                                @if(isset($photos[3]))
                                                    <img src="{{ asset($photos[3]) }}"
                                                        alt="Foto adicional"
                                                        class="w-full h-[170px] object-cover  cursor-pointer"
                                                        @click="showModal = true; activeSlide = 3">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 📱 Modo Tablet / iPhone (Carrusel con flechas) -->
                                    <div class="lg:hidden relative w-full" x-data="{
                                        activeSlide: 0, 
                                        slides: {{ json_encode($photos) }},
                                        touchStartX: 0,
                                        touchEndX: 0,
                                        startSwipe(event) { this.touchStartX = event.touches[0].clientX; },
                                        endSwipe(event) { 
                                            this.touchEndX = event.changedTouches[0].clientX;
                                            let diff = this.touchStartX - this.touchEndX;
                                            if (Math.abs(diff) > 50) {
                                                if (diff > 0) { this.activeSlide = (this.activeSlide + 1) % this.slides.length; } 
                                                else { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length; }
                                            }
                                        }
                                        }">
                                        <img :src="slides[activeSlide]"
                                            alt="Foto de {{ $park->name }}"
                                            class="w-full h-[300px] object-cover"
                                            @touchstart="startSwipe($event)"
                                            @touchend="endSwipe($event)">

                                        <!-- Indicadores -->
                                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                            <template x-for="(photo, index) in slides" :key="index">
                                                <button @click="activeSlide = index" 
                                                    :class="activeSlide === index ? 'bg-orange-500' : 'bg-gray-300'"
                                                    class="w-2 h-2 rounded-full transition-all"></button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- 📸 Modal de Imágenes -->
                                    <template x-if="showModal">
                                        <div class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center" @click="showModal = false">
                                            <div class="relative w-full max-w-4xl mx-auto  shadow-lg" @click.stop>
                                                
                                                <!-- ❌ Botón de Cerrar -->
                                            
                                                <button class="absolute top-4 right-4 text-black p-2 rounded-full focus:outline-none z-50" type="button" @click="showModal = false">
                                                    <x-lucide-x class="w-6 h-6 text-black" />
                                                </button>

                                                <!-- 📸 Contenedor de Imagen -->
                                                <div class="relative">
                                                    <img :src="slides[activeSlide]" alt="Foto del parque" class="w-full max-h-[80vh] object-contain">

                                                    <!-- ⬅ Botón Anterior -->
                                                    <!-- ⬅ Botón Anterior -->
                                                    <button class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                                            @click.stop="prev()">
                                                        <x-lucide-chevron-left class="w-6 h-6 text-orange-500" />
                                                    </button>

                                                    <!-- ➡ Botón Siguiente -->
                                                    <button class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                                            @click.stop="next()">
                                                        <x-lucide-chevron-right class="w-6 h-6 text-orange-500" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- Botón flotante dentro de la imagen (para tablets y celulares) -->
                                    <button id="floating-favorite-btn" 
                                        class="absolute top-4 right-4 p-2 rounded-full  bg-white shadow-md md:hidden"
                                        data-id="{{ $park->id }}" 
                                        data-type="park"
                                        data-favorite="{{ $isFavorite ? 'true' : 'false' }}">
                                        
                                        <x-lucide-heart :class="$isFavorite ? 'w-6 h-6 text-orange-500 fill-current' : 'w-6 h-6 text-orange-500 stroke-current'" id="floating-favorite-icon" />
                                    </button>
                    </div>
                @endif
            @endif
        </div>
                <!-- Fila 2: Detalles -->
        <div class="grid grid-cols-1 md:grid-cols-4 px-6 lg:px-[25%] sm:px-[6%] gap-6 items-start">
            <div class="md:col-span-3 sm:col-span-full ">
                <h1 class="text-2xl sm:text-3xl my-2 font-bold text-gray-900 flex items-center">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-black rounded-sm flex items-center justify-center p-2 mr-2">
                        <x-lucide-trees class="w-5 h-5 sm:w-6 sm:h-6 text-orange-500" />
                    </div>
                    {{ $park->name }}
                </h1>
                <div class="flex my-2 items-center space-x-1">
                    @php 
                        $fullStars = floor($park->rating);
                        $hasHalfStar = ($park->rating - $fullStars) >= 0.5;
                    @endphp
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $fullStars)
                            <x-lucide-star class="w-4 h-4 sm:w-5 sm:h-5 text-orange-500 fill-current" />
                        @elseif ($hasHalfStar && $i == $fullStars + 1)
                            <x-lucide-star-half class="w-4 h-4 sm:w-5 sm:h-5 text-orange-500" />
                        @else
                            <x-lucide-star class="w-4 h-4 sm:w-5 sm:h-5 text-gray-300" />
                        @endif
                    @endfor
                </div>
                <p class="text-gray-600 text-xs sm:text-sm mt-1 flex items-center space-x-1 my-2">
                    <x-lucide-map-pin class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" />
                    <span>{{ $park->location }}</span> 
                </p>
                <div class="mt-5 mb-3 flex items-center  space-x-4">
                    <div class="p-4 rounded-lg bg-gray-50 shadow-sm flex items-center space-x-3">
                        <div class="bg-orange-500 text-white px-3 py-2 rounded-md text-lg font-bold">
                            {{ number_format($park->rating, 1) }}
                        </div>
                        <div>
                            @php
                                $ratingText = match (true) {
                                    $park->rating >= 4.5 => 'Excelente',
                                    $park->rating >= 4   => 'Muy bueno',
                                    $park->rating >= 3   => 'Está bien',
                                    $park->rating >= 2   => 'Regular',
                                    default              => 'Malo',
                                };
                            @endphp
                            <p class="font-semibold text-gray-900">{{ $ratingText }}</p>
                            <a href="#opiniones" class="text-blue-600 hover:underline">Ver {{ $park->reviews_count }} comentarios</a>
                        </div>
                    </div>
                </div>
            </div>
        
                    <!-- Botón estándar (para computadoras) -->
            <div class="hidden md:flex justify-end mb-4">
                <button id="favorite-btn" 
                    class="px-4 py-2 rounded transition border border-black text-black 
                            hover:text-orange-500 hover:border-orange-500 flex items-center space-x-2"
                    data-id="{{ $park->id }}" 
                    data-type="park"
                    data-favorite="{{ $isFavorite ? 'true' : 'false' }}">

                    <x-lucide-heart :class="$isFavorite ? 'w-5 h-5 text-orange-500 fill-current' : 'w-5 h-5 text-orange-500 stroke-current'" id="favorite-icon" />
                    <span>{{ $isFavorite ? 'Guardado' : 'Guardar' }}</span>
                </button>
            </div>
        </div>  
             <!-- Fila 3: Data -->
        <div class="relative mx-auto px-6 border-t mt-4 lg:px-[25%] sm:px-[6%] w-full"> 
                <h3 class="text-lg mt-4 font-semibold">Horarios de Apertura</h3>
                <div class=" mt-2">
                    @if ($park->opening_hours)
                        @php $hours = json_decode($park->opening_hours, true); @endphp
                        <ul class="list-disc  space-y-1">
                            @foreach ($hours as $day => $time)
                                <li class="flex items-center space-x-2">
                                    <x-lucide-clock class="w-4 h-4 text-black" />
                                    <strong>{{ $day }}:</strong> 
                                    <span>
                                        {{ Str::lower($time) === 'abierto las 24 horas' ? 'Abierto las 24 horas' : $time }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">No se han especificado horarios.</p>
                    @endif
                </div>
                <hr class="my-4">
                
                <!-- 🏋️ Actividades Disponibles -->
                <h3 class="text-lg font-semibold"> Actividades Disponibles</h3>
                @if ($park->trainings->isEmpty())
                    <p class="text-gray-500">No hay actividades en este parque.</p>
                @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-3">
                    @foreach($park->trainings->groupBy('activity_id') as $activityId => $trainings)
                        @php $activity = $trainings->first()->activity; @endphp
                        @if ($activity) 
                        <a href="{{ route('trainings.catalog', ['park' => $park->id, 'activity' => $activity->id]) }}" 
                        class="block activity-card p-3 sm:p-4 md:p-5 text-center shadow-md rounded-lg bg-white 
                                hover:shadow-xl hover:border-orange-500 hover:bg-orange-300 transition duration-300 ease-in-out transform hover:-translate-y-1">
                            <h5 class="text-black font-bold text-lg sm:text-xl">{{ $activity->name }}</h5>
                            <p class="hidden md:block text-sm text-gray-700">{{ $trainings->count() }} entrenamiento/s</p>
                        </a>
                        @endif
                    @endforeach
                </div>
                @endif
                <hr class="my-4">
                <!-- ⭐ Reseñas -->
                <h3 id="opiniones" class="text-lg font-semibold">Opiniones</h3>
                @if ($park->reviews->isEmpty())
                    <p class="text-gray-500">No hay reseñas para este parque.</p>
                @else
                    <!-- Contenedor de reseñas iniciales -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-y-2 ">
                        @foreach ($park->reviews->take(2) as $index => $review)
                        <div class="py-4 rounded-sm flex items-start space-x-6 
                                    {{ $index % 2 == 0 ? 'lg:pr-14' : 'lg:pl-14' }}
                                   max-sm:bg-white max-sm:mt-2 max-sm:shadow-sm max-sm:rounded-lg max-sm:p-4">
                                <div>
                               
                                    <p class="font-semibold text-gray-900">{{ $review->author }}</p>
                                    <div class="flex items-center space-x-1 mt-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-lucide-star class="w-4 h-4 {{ $i <= $review->rating ? 'text-orange-500 fill-current' : 'text-gray-300' }}" />
                                        @endfor
                                        <span class="text-sm text-gray-500">• <strong>Hace {{ $review->time }}</strong> • <span class="text-gray-500">{{ $review->group_type }}</span></span>
                                    </div>
                                    <p class="text-gray-700 mt-2">{{ $review->text }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Botón para abrir el modal -->
                    <div class="flex justify-end mt-2">
                        <button id="open-reviews-modal" class="text-orange-500 font-semibold underline hover:underline">Ver más opiniones</button>
                    </div>
                    <!-- Modal de reseñas -->
                    <div id="reviews-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full p-8 relative h-[90vh] overflow-hidden">
                            <button id="close-reviews-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                            <h3 class="text-2xl font-semibold mb-4">Todas las opiniones</h3>
                            <!-- Contenedor de todas las reseñas -->
                            <div class="overflow-y-auto h-[80vh] px-6 space-y-6">
                                @foreach ($park->reviews->sortByDesc('created_at') as $review)
                                    <div class="py-4 rounded-sm flex items-start space-x-6 bg-white border-b pb-4">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $review->author }}</p>
                                            <div class="flex items-center space-x-1 mt-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <x-lucide-star class="w-4 h-4 {{ $i <= $review->rating ? 'text-orange-500 fill-current' : 'text-gray-300' }}" />
                                                @endfor
                                                <span class="text-sm text-gray-500">• <strong>Hace {{ $review->time }}</strong> • <span class="text-gray-500">{{ $review->group_type }}</span></span>
                                            </div>
                                            <p class="text-gray-700 font-light mt-2">{{ $review->text }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
        </div>      
    </div>
    
</div>

<!-- Script de Favoritos -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let desktopButton = document.querySelector("#favorite-btn");
        let floatingButton = document.querySelector("#floating-favorite-btn");

        if (!desktopButton && !floatingButton) return;

        function toggleFavorite(button, icon) {
            let isCurrentlyFavorite = button.dataset.favorite === "true";

            // Cambia el estado del botón visualmente
            button.classList.toggle("bg-black", !isCurrentlyFavorite);
            button.classList.toggle("text-orange-500", !isCurrentlyFavorite);
            button.classList.toggle("border-black", isCurrentlyFavorite);
            button.classList.toggle("text-black", isCurrentlyFavorite);
            icon.classList.toggle("fill-current", !isCurrentlyFavorite);
            icon.classList.toggle("stroke-current", isCurrentlyFavorite);
            button.dataset.favorite = isCurrentlyFavorite ? "false" : "true";
        }

        async function handleFavoriteClick(event, button, icon) {
            event.preventDefault();
            if (button.dataset.processing === "true") return;
            button.dataset.processing = "true";

            let favoritableId = button.dataset.id;
            let favoritableType = button.dataset.type;

            toggleFavorite(button, icon);

            try {
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
                if (!csrfToken) throw new Error("No se encontró el token CSRF en el HTML.");

                let response = await fetch("/favorites/toggle", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ favoritable_id: favoritableId, favoritable_type: favoritableType }),
                });

                if (!response.ok) throw new Error("Error en la respuesta del servidor");

                let data = await response.json();
                console.log("✅ Respuesta del servidor:", data);
            } catch (error) {
                console.error("❌ Error en la solicitud:", error);
                alert("Hubo un error al procesar la solicitud.");
                toggleFavorite(button, icon); // Deshacer cambios si falla
            } finally {
                button.dataset.processing = "false";
            }
        }

        // Agregar eventos a ambos botones (si existen)
        if (desktopButton) {
            let desktopIcon = desktopButton.querySelector("#favorite-icon");
            desktopButton.addEventListener("click", (event) => handleFavoriteClick(event, desktopButton, desktopIcon));
        }

        if (floatingButton) {
            let floatingIcon = floatingButton.querySelector("#floating-favorite-icon");
            floatingButton.addEventListener("click", (event) => handleFavoriteClick(event, floatingButton, floatingIcon));
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        let openModalBtn = document.querySelector("#open-reviews-modal");
        let closeModalBtn = document.querySelector("#close-reviews-modal");
        let modal = document.querySelector("#reviews-modal");

        if (openModalBtn && closeModalBtn && modal) {
            openModalBtn.addEventListener("click", () => {
                modal.classList.remove("hidden");
            });

            closeModalBtn.addEventListener("click", () => {
                modal.classList.add("hidden");
            });

            modal.addEventListener("click", (e) => {
                if (e.target === modal) {
                    modal.classList.add("hidden");
                }
            });
        }
    });
</script>

@endsection




