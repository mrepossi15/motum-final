@extends('layouts.main')

@section('title', 'Detalle del Entrenamiento')

@section('content')
<div class="flex justify-center min-h-screen bg-white text-black">
    <div class="w-full px-4 sm:px-6 md:px-6 lg:px-8">
        <!-- Contenido principal -->
        <div class="relative mx-auto w-11/12 sm:w-10/12 md:w-10/12 lg:w-2/3">
            <!-- Dropdown flotante arriba a la derecha respetando márgenes -->
            <div class="absolute top-0 right-4 sm:right-6 lg:right-8 mt-4 z-10">
                <div class="relative">
                    <button class="bg-white text-black px-3 py-1 rounded-md shadow" onclick="toggleDropdown()">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md hidden z-20">
                        <li>
                            <a href="{{ route('trainings.edit', ['id' => $training->id, 'date' => $selectedDate ?? now()->toDateString()]) }}" 
                            class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                                Editar
                            </a>
                        </li>
                        
                        <li>
    <button class="dropdown-item text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50" onclick="toggleModal()">Eliminar</button>
</li>
                       
                    </ul>
                </div>
            </div>

            <!-- Galería de Imágenes -->
            <div class="mb-6">
            
    <div class="hidden md:block">
        <a href="{{ route('trainings.gallery', ['training' => $training->id]) }}" class="block">
            @php $photoCount = $training->photos->count(); @endphp

            @if($photoCount > 0)
                <div class="grid gap-4 
                            @if($photoCount == 1) grid-cols-1 
                            @elseif($photoCount == 2) grid-cols-2 
                            @else grid-cols-4 @endif">

                    {{-- 🖼️ Si hay solo una foto, mostrarla en pantalla completa --}}
                    @if($photoCount == 1)
                        <div class="overflow-hidden cursor-pointer">
                            <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}"
                                 alt="Foto entrenamiento"
                                 class="w-full h-[300px] object-cover ">
                        </div>

                    {{-- 🖼️ Si hay 2 fotos, cada una ocupa la mitad --}}
                    @elseif($photoCount == 2)
                        @foreach($training->photos as $photo)
                            <div class="overflow-hidden cursor-pointer">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                     alt="Foto entrenamiento"
                                     class="w-full h-[300px] object-cover ">
                            </div>
                        @endforeach

                    {{-- 🖼️ Si hay 3 o más fotos, mostrar en layout de 4 columnas --}}
                    @elseif($photoCount >= 3)
                        <div class="col-span-3 overflow-hidden">
                            <img src="{{ asset('storage/' . $training->photos[0]->photo_path) }}"
                                 alt="Foto principal"
                                 class="w-full h-[300px] object-cover ">
                        </div>

                        <div class="grid grid-rows-2 gap-4">
                            @foreach($training->photos->slice(1, 2) as $photo)
                                <div class="overflow-hidden cursor-pointer">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                         alt="Foto adicional"
                                         class="w-full h-[140px] object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            @else
                {{-- 🖼️ Imagen por defecto si no hay fotos --}}
                <div class="overflow-hidden cursor-pointer">
                    <img src="{{ asset('images/default-training.jpg') }}" 
                         alt="Foto de entrenamiento" 
                         class="w-full h-[300px] object-cover ">
                </div>
            @endif
        </a>
    </div>


    <!-- Carrusel en dispositivos móviles -->
    <div class="block md:hidden" x-data="carousel({{ $training->photos->count() }})">
        <div class="relative w-full overflow-hidden"
            x-ref="carousel"
            @touchstart="startSwipe($event)"
            @touchend="endSwipe($event)">
            <div class="flex transition-transform duration-500 ease-in-out"
                :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
                @foreach($training->photos as $photo)
                    <div class="w-full flex-shrink-0">
                        <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}"
                            alt="Foto de entrenamiento"
                            class="w-full h-[300px] object-cover"
                            @click="openGallery('{{ route('trainings.gallery', ['training' => $training->id]) }}')">
                    </div>
                @endforeach
            </div>

            <!-- Indicadores -->
            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1">
                <template x-for="index in totalSlides">
                    <div @click="activeSlide = index - 1"
                        class="w-2 h-2 rounded-full"
                        :class="activeSlide === (index - 1) ? 'bg-blue-500' : 'bg-gray-300'">
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

            <!-- Fila 2: Detalles -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <div class="md:col-span-3 space-y-4">
                    <h1 class="text-3xl font-bold text-orange-600">{{ $training->title }}</h1>
                    <div class="flex items-center mt-2">
                        @php
                            $averageRating = round($training->averageRating(), 1);
                            $fullStars = floor($averageRating);
                            $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                        @endphp

                        <!-- Estrellas -->
                        <div class="flex items-center mr-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $fullStars)
                                    <svg class="w-4 h-4 text-orange-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M10 1l2.39 6.86h7.11l-5.7 4.13 2.39 6.87-5.7-4.14-5.7 4.14 2.39-6.87-5.7-4.13h7.11z"/>
                                    </svg>
                                @elseif ($hasHalfStar && $i == $fullStars + 1)
                                    <svg class="w-4 h-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <defs>
                                            <linearGradient id="halfStar">
                                                <stop offset="50%" stop-color="currentColor"/>
                                                <stop offset="50%" stop-color="lightgray"/>
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#halfStar)" d="M10 1l2.39 6.86h7.11l-5.7 4.13 2.39 6.87-5.7-4.14-5.7 4.14 2.39-6.87-5.7-4.13h7.11z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M10 1l2.39 6.86h7.11l-5.7 4.13 2.39 6.87-5.7-4.14-5.7 4.14 2.39-6.87-5.7-4.13h7.11z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>

                        
                        <!-- Puntuación promedio en formato 0.0 -->
                        <span class="text-gray-700 text-sm font-semibold">
                            {{ number_format($averageRating, 1) }} 
                        </span>
                    </div>
                    <p class="text-gray-600 mt-1">🏞 {{ $training->park->name }}</p>

                    <!-- Información Clave -->
                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-4 gap-4 border-b border-t pb-4 pt-4">
                        <div class="text-left">
                            <p class="text-sm text-gray-500">📅 Fecha</p>
                            <p class="text-gray-700">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d \d\e F Y') }}</p>
                        </div>

                        <div class="text-left">
                            <p class="text-sm text-gray-500">🕒 Horario</p>
                            <p class="text-gray-700">{{ $filteredSchedules->first()-> start_time ?? 'No disponible' }} - {{ $filteredSchedules->first()-> end_time ?? 'No disponible' }}</p>
                        </div>

                        <div class="text-left">
                            <p class="text-sm text-gray-500">⚽ Actividad</p>
                            <p class="text-gray-700">{{ $training->activity->name }}</p>
                        </div>

                        <div class="text-left">
                            <p class="text-sm text-gray-500">🎟 Cupos</p>
                            <p class="text-gray-700">{{ $filteredReservations->has($selectedTime) ? $filteredReservations[$selectedTime]->count() : 0 }} / {{ $training->available_spots ?? 'No especificados' }}</p>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <h2 class="text-lg font-semibold mb-2">Descripción</h2>
                        <p class="text-gray-700">{{ $training->description ?? 'No hay descripción disponible.' }}</p>
                    </div>
                    <!-- 💰 Sección de Precios -->
                    @if($training->prices->isNotEmpty())
                        <div class="border-b pb-4 pt-4">
                            <h2 class="text-lg font-semibold mb-2">💰 Precios</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($training->prices as $price)
                                    <div class="border p-4 rounded-lg shadow-sm bg-white">
                                        <p class="text-gray-700"><strong>{{ $price->weekly_sessions }} x semana</strong></p>
                                        <p class="text-gray-500 text-sm">Precio: <span class="text-orange-600 font-semibold">${{ number_format($price->price, 2) }}</span></p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <!-- Resenas -->
                    <div class="border-t pb-4 pt-4">
                        <h2 class="text-lg font-semibold mb-2">Reseñas</h2>
                        @if($training->reviews->isEmpty())
                            <p class="text-gray-500">No hay reseñas para este entrenamiento.</p>
                        @else
                            @foreach($training->reviews as $review)
                                <div class="mb-6 p-4 border rounded-lg shadow-sm bg-white">
                                    <!-- Encabezado: Usuario y Antigüedad -->
                                    <div class="flex items-center mb-2">
                                        <img src="{{ $review->user->profile_photo_url ?? asset('images/default-avatar.png') }}" 
                                            alt="Foto de {{ $review->user->name }}" 
                                            class="w-12 h-12 rounded-full mr-3 object-cover">
                                        <div>
                                            <p class="font-bold text-lg">{{ $review->user->name }}</p>
                                            <p class="text-gray-500 text-sm">Hace {{ now()->diffInYears($review->user->created_at) }} años que está en la plataforma</p>
                                        </div>
                                    </div>

                                    <!-- Calificación y Fecha -->
                                    <div class="flex items-center text-gray-600 text-sm mb-2">
                                        <div class="flex items-center mr-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.09 2.673 1.273-5.455-4.183-3.636 5.455-.455L10 3l2.545 5.127 5.455.455-4.183 3.636 1.273 5.455z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-gray-500">• {{ \Carbon\Carbon::parse($review->created_at)->translatedFormat('F Y') }}</span>
                                    </div>

                                    <!-- Comentario (Mostrar más si es largo) -->
                                    <div x-data="{ expanded: false }">
                                        <p class="text-gray-700" x-show="!expanded">
                                            {{ Str::limit($review->comment, 150) }}
                                            @if(strlen($review->comment) > 150)
                                                <span @click="expanded = true" class="text-blue-500 cursor-pointer">Mostrar más</span>
                                            @endif
                                        </p>
                                        <p class="text-gray-700" x-show="expanded" x-cloak>
                                            {{ $review->comment }}
                                            <span @click="expanded = false" class="text-blue-500 cursor-pointer">Mostrar menos</span>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Botón Ver Lista -->
                <!-- Botón para Desktop y Tablet -->
                <div class="flex justify-center items-center">
                <div class="bg-white p-4 rounded-lg shadow-lg border w-full max-w-[400px] mx-auto">
                    <div class="flex justify-center">
                        @if ($isClassAccessible)
                            <a href="{{ $reservationDetailUrl }}" 
                            class="inline-flex items-center justify-center bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 w-full text-base truncate whitespace-nowrap">
                                📋 Tomar Lista
                            </a>
                        @else
                            <button class="bg-yellow-500 text-black px-4 py-2 rounded-md w-full text-base truncate whitespace-nowrap" disabled>
                                {{ $accessMessage }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
                

                <!-- Botón Fijo en Móviles (sm o menos) -->
                <div class="fixed inset-x-0 bottom-0 z-50 bg-white p-2 shadow-lg md:hidden">
                    <div class="flex justify-center">
                        <div class="w-full max-w-[400px]">
                            @if($filteredReservations->has($selectedTime) && $filteredReservations[$selectedTime]->isNotEmpty())
                                @if($isClassAccessible)
                                    <a href="{{ $reservationDetailUrl }}" class="inline-flex items-center justify-center bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 w-full text-sm truncate whitespace-nowrap">
                                        Tomar Lista
                                    </a>
                                @else
                                    <button class="bg-yellow-500 text-black px-4 py-2 rounded-md w-full text-sm truncate whitespace-nowrap" disabled>
                                        Disponible el día de la clase
                                    </button>
                                @endif
                            @else
                                <button class="bg-gray-400 text-white px-4 py-2 rounded-md w-full text-sm truncate whitespace-nowrap" disabled>
                                    No disponible
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 hidden" id="deleteModal">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <h5 class="text-lg font-semibold text-red-600" id="deleteModalLabel">Confirmar Eliminación</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="toggleModal()">
                <span class="sr-only">Cerrar</span>&times;
            </button>
        </div>
        <div class="px-4 py-6">
            <p class="text-gray-700">¿Estás seguro de que deseas suspender este entrenamiento? Esta acción no se puede deshacer.</p>
        </div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500" onclick="toggleModal()">Cancelar</button>
            <form action="{{ route('trainings.suspend') }}" method="POST" class="ml-3">
                @csrf
                <input type="hidden" name="training_id" value="{{ $training->id }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    Suspender Clase
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = (modal.style.display === "none" || modal.style.display === "") ? "flex" : "none";
    }
</script>


<script>
function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('hidden');
}


   
function carousel(totalSlides) {
    return {
        activeSlide: 0,
        totalSlides: totalSlides,
        touchStartX: 0,
        touchEndX: 0,

        // Swipe Inicio
        startSwipe(event) {
            this.touchStartX = event.touches[0].clientX;
        },

        // Swipe Fin
        endSwipe(event) {
            this.touchEndX = event.changedTouches[0].clientX;
            const diff = this.touchStartX - this.touchEndX;

            if (Math.abs(diff) > 30) {
                // Desplazamiento lateral (Swipe)
                if (diff > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
            } else {
                // Tap sin swipe: abrir galería
                this.openGallery('{{ route('trainings.gallery', ['training' => $training->id]) }}');
            }
        },

        // Siguiente Imagen
        nextSlide() {
            this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
        },

        // Imagen Anterior
        prevSlide() {
            this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
        },

        // Abrir Galería
        openGallery(url) {
            window.location.href = url;
        }
    };
}
</script>

@endsection