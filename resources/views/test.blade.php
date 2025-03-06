

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
                            @if($filteredSchedules->isNotEmpty())
                                @php
                                    $schedule = $filteredSchedules->first();
                                @endphp
                                <p class="text-gray-700">
                                    {{ $schedule->start_time ?? 'No disponible' }} - {{ $schedule->end_time ?? 'No disponible' }}
                                    @if($schedule->is_exception)
                                        <span class="badge bg-warning">Horario Modificado</span>
                                    @endif
                                </p>
                            @else
                                <p class="text-gray-700">No disponible</p>
                            @endif
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
                                            {{($review->comment) }}
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


@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')

<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Mis Entrenamientos</h1>
            @if ($parks->isEmpty())
                <p class="text-gray-500 text-center text-lg">Aún no tienes entrenamientos asociados. 🏞️</p>
            @else
                <div class="grid grid-cols-1  lg:grid-cols-3 md:grid-cols-2 gap-6">
                    @foreach ($trainings as $training)
                        <div class="bg-gray-50 rounded-md shadow-md hover:shadow-md transition flex flex-col">
                            <!-- Imagen del parque ocupa todo el ancho de la card -->
                            <div class="w-full h-40 rounded-t-lg overflow-hidden">
                                @if(!empty($training->photo_urls) && is_array(json_decode($training->photo_urls, true)))
                                    @php
                                        $photoUrls = json_decode($training->photo_urls, true);
                                        $imageUrl = !empty($photoUrls) ? $photoUrls[0] : null;
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="Imagen de {{ $training->name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/default-training.jpg') }}" alt="Imagen por defecto" class="w-full h-full object-cover">
                                    @endif
                                @else
                                    <img src="{{ asset('images/default-training.jpg') }}" alt="Imagen por defecto" class="w-full h-full object-cover">
                                @endif
                            </div>
                            
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $park->name }}</h3>
                                    <p class="flex items-center space-x-1 text-xs text-gray-600 text-gray-600 mt-1" 
                                    x-data="{ formattedAddress: formatAddress('{{ $park->location ?? '' }}') }">
                                        <x-lucide-map-pin class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" />
                                        <span x-text="formattedAddress || 'Ubicación desconocida'"></span>
                                    </p>
                                    <!-- Rating del parque -->
                                <div class="flex my-2 items-center space-x-1">
                                    @php 
                                        $rating = $park->rating ?? 0; // ✅ Evita errores si rating es null
                                        $fullStars = floor($rating);
                                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                        $averageRating = method_exists($park, 'averageRating') ? round($park->averageRating(), 1) : round($rating, 1);
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

                                    <span class="text-gray-700 text-sm font-semibold">
                                        {{ number_format($averageRating, 1) }}
                                    </span>
                                </div>
                                    <p class="text-gray-600 mt-1 font-semibold">Horario:</p>
                                    
                                    @if ($park->opening_hours)
                                        @php
                                            $openingHours = json_decode($park->opening_hours, true);
                                        @endphp
                                        @if (is_array($openingHours))
                                            <ul class="list-none text-gray-700 text-sm mt-1">
                                                @foreach ($openingHours as $day => $hours)
                                                    <li class="flex justify-between py-1 border-b border-gray-200">
                                                        <span class="font-medium">{{ ucfirst($day) }}</span>
                                                        <span>{{ $hours }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-700">{{ $park->opening_hours }}</p>
                                        @endif
                                    @else
                                        <p class="text-gray-500">No especificado</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
    
    </div>
</div>
<script>
    function formatAddress(address) {
    if (!address) return "Ubicación desconocida";
    
    const parts = address.split(","); // Divide la dirección en partes
    return parts.slice(0, 2).join(","); 
    }// Toma solo las primeras 2 partes
</script>


@endsection

@extends('layouts.main')

@section('title', '')

@section('content')
<div class="flex justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h2 class="text-2xl font-semibold mb-4"> Detalle de {{ $trainer->name }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-24 md:pb-6"> 
            <div class="bg-white shadow-lg rounded-lg p-4 md:sticky md:top-4 md:self-start w-full md:relative border-t md:border-none">
                <div class="border-b align-center pb-4">
                    <div class="flex items-center gap-6">
                        <!-- Imagen de Perfil -->
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-orange-300 shadow-md">
                            @if($user->profile_pic)
                                <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/default-profile.png') }}" alt="Foto de perfil por defecto" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">{{ $trainer->name }}</h2>
                            <p class="text-gray-500">{{ ucfirst($trainer->role) }}</p>
                            <p class="text-gray-700">
                                {{ $user->birth ? \Carbon\Carbon::parse($trainer->birth)->age . ' años' : 'Fecha de nacimiento no especificada' }}
                            </p>
                        </div>
                    </div>
                    
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($user->activities as $activity)
                            <span class="bg-orange-500 text-white px-3 py-1 rounded-md text-sm">
                                {{ $activity->name }}
                            </span>
                        @endforeach
                    </div>
            </div>

            <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-4 relative">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-semibold">Sobre {{$trainer->name}}</h2>
                
                </div>
                <p class="text-gray-600 border-b pb-10">{{ $trainer->biography ?? 'Sin información' }}</p>
                <div class="mt-4">
                    <h3 class="text-lg font-semibold text-gray-900">Último Pago</h3>
                    <p class="text-gray-600 mt-2">
                        {{ optional($trainer->payments()->latest()->first())->created_at ? optional($trainer->payments()->latest()->first())->created_at->format('d/m/Y') : 'No registrado' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



@extends('layouts.main')

@section('title', "Perfil de {$trainer->name}")

@section('content')
<div class="flex justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h2 class="text-2xl font-semibold mb-4"> Perfil de {{ $trainer->name }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-24 md:pb-6"> 
            <!-- 🖼️ Info del Alumno -->
            <div class="bg-white shadow-lg rounded-lg p-4 md:sticky md:top-4 md:self-start w-full md:relative border-t md:border-none">
                <div class="border-b pb-4">
                    <div class="flex items-center gap-6">
                        <!-- Imagen de Perfil -->
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-orange-300 shadow-md">
                            @if($trainer->profile_pic)
                                <img src="{{ asset('storage/' . $trainer->profile_pic) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/default-profile.png') }}" alt="Foto de perfil por defecto" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">{{ $trainer->name }}</h2>
                            <p class="text-gray-500">{{ ucfirst($trainer->role) }}</p>
                            <p class="text-gray-700">
                                {{ $trainer->birth ? \Carbon\Carbon::parse($trainer->birth)->age . ' años' : 'Fecha de nacimiento no especificada' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($trainer->activities as $activity)
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-md text-sm">
                            {{ $activity->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- 📋 Información y entrenamientos comprados -->
            <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-4 px-6 relative ">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-user class="w-5 h-5 text-orange-500 mr-1" />
                        Biografía
                    </h3>
                <p class="text-gray-600 border-b pb-4">{{ $trainer->biography ?? 'Sin información' }}</p>

                <div class="mt-4 border-b pb-4 ">
               
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <x-lucide-dollar-sign class="w-5 h-5 text-orange-500 mr-1" />Experiencia</h3>
                    <div class="bg-white rounded-lg shadow-md p-4">
                        @if ($experiences->isEmpty())
                            <p class="text-gray-500 text-center">No tienes experiencias registradas.</p>
                        @else
                            <ul class="divide-y divide-gray-200">
                                @foreach ($experiences as $experience)
                                    <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $experience->role }}</h3>
                                            <p class="text-gray-600"><strong>Empresa/Gimnasio:</strong> {{ $experience->company ?? 'Freelance' }}</p>
                                            <p class="text-gray-600"><strong>Periodo:</strong> {{ $experience->year_start }} - 
                                                @if($experience->currently_working)
                                                    <span class="text-green-500 font-semibold">Actualmente</span>
                                                @else
                                                    {{ $experience->year_end }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex space-x-2 mt-2 sm:mt-0">
                                            <button onclick="loadExperience({{ $experience->id }})" class="text-orange-500 hover:underline"> Editar</button>
                                            <form action="{{ route('trainer.experience.destroy', $experience->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline">Eliminar</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- 📌 Entrenamientos comprados -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-dumbbell class="w-5 h-5 text-orange-500 mr-2" />
                        Entrenamientos
                    </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-3 gap-6 mb-4">
                            @foreach($trainings as $training)
                                @if($training) {{-- Verifica si el entrenamiento aún existe --}}
                                    <a href="{{ route('trainings.detail', $training->id) }}" class="block">
                                        <div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105 cursor-pointer">
                                            
                                            <!-- 📸 Imagen del entrenamiento -->
                                            @if ($training->photos->isNotEmpty())
                                                <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                                    class="w-full h-48 object-cover" 
                                                    alt="Foto de entrenamiento">
                                            @endif

                                            <!-- 📝 Detalles del entrenamiento -->
                                            <div class="p-4">
                                                <h5 class="text-xl font-semibold text-gray-800">{{ $training->title }}</h5>

                                                <p class="text-gray-600 text-sm">
                                                    <strong>Ubicación:</strong> {{ $training->park->name ?? 'No disponible' }} <br>
                                                    <strong>Actividad:</strong> {{ $training->activity->name ?? 'No disponible' }} <br>
                                                    <strong>Nivel:</strong> {{ ucfirst($training->level) ?? 'No especificado' }}
                                                </p>

                                                <!-- 📅 Días con clases -->
                                                <div class="mt-3">
                                                    <strong class="text-gray-700">Días con Clases:</strong>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @if ($training->schedules->isNotEmpty())
                                                            @foreach ($training->schedules as $schedule)
                                                                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                                    {{ ucfirst($schedule->day) }}
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <p class="text-gray-500 text-xs">No hay horarios disponibles.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    
                                @endif
                            @endforeach
                        </div>
                  
                </div>
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-trees class="w-5 h-5 text-orange-500 mr-2" />
                        Parques donde entrena
                    </h3>
                
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-3 gap-6 mb-4">
                        @foreach ($parks as $park)
                        <div class="bg-gray-50 rounded-md shadow-md hover:shadow-md transition flex flex-col">
                            <!-- Imagen del parque ocupa todo el ancho de la card -->
                            <div class="w-full h-40 rounded-t-lg overflow-hidden">
                                @if(!empty($park->photo_urls) && is_array(json_decode($park->photo_urls, true)))
                                    @php
                                        $photoUrls = json_decode($park->photo_urls, true);
                                        $imageUrl = !empty($photoUrls) ? $photoUrls[0] : null;
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="Imagen de {{ $park->name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/default-park.jpg') }}" alt="Imagen por defecto" class="w-full h-full object-cover">
                                    @endif
                                @else
                                    <img src="{{ asset('images/default-park.jpg') }}" alt="Imagen por defecto" class="w-full h-full object-cover">
                                @endif
                            </div>
                            
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $park->name }}</h3>
                                    <p class="flex items-center space-x-1 text-xs text-gray-600 text-gray-600 mt-1" 
                                    x-data="{ formattedAddress: formatAddress('{{ $park->location ?? '' }}') }">
                                        <x-lucide-map-pin class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" />
                                        <span x-text="formattedAddress || 'Ubicación desconocida'"></span>
                                    </p>
                                    <!-- Rating del parque -->
                                <div class="flex my-2 items-center space-x-1">
                                    @php 
                                        $rating = $park->rating ?? 0; // ✅ Evita errores si rating es null
                                        $fullStars = floor($rating);
                                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                        $averageRating = method_exists($park, 'averageRating') ? round($park->averageRating(), 1) : round($rating, 1);
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

                                    <span class="text-gray-700 text-sm font-semibold">
                                        {{ number_format($averageRating, 1) }}
                                    </span>
                                </div>
                                    <p class="text-gray-600 mt-1 font-semibold">Horario:</p>
                                    
                                    @if ($park->opening_hours)
                                        @php
                                            $openingHours = json_decode($park->opening_hours, true);
                                        @endphp
                                        @if (is_array($openingHours))
                                            <ul class="list-none text-gray-700 text-sm mt-1">
                                                @foreach ($openingHours as $day => $hours)
                                                    <li class="flex justify-between py-1 border-b border-gray-200">
                                                        <span class="font-medium">{{ ucfirst($day) }}</span>
                                                        <span>{{ $hours }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-700">{{ $park->opening_hours }}</p>
                                        @endif
                                    @else
                                        <p class="text-gray-500">No especificado</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                        </div>
                    @endif
                </div>
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-trees class="w-5 h-5 text-orange-500 mr-2" />
                        Parques donde entrena
                    </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-3 gap-6 mb-4">
                        @foreach ($parks as $park)
                        <div class="bg-gray-50 rounded-md shadow-md hover:shadow-md transition flex flex-col">
                            <!-- Imagen del parque ocupa todo el ancho de la card -->
                            <div class="w-full h-40 rounded-t-lg overflow-hidden">
                                @if(!empty($park->photo_urls) && is_array(json_decode($park->photo_urls, true)))
                                    @php
                                        $photoUrls = json_decode($park->photo_urls, true);
                                        $imageUrl = !empty($photoUrls) ? $photoUrls[0] : null;
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="Imagen de {{ $park->name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/default-park.jpg') }}" alt="Imagen por defecto" class="w-full h-full object-cover">
                                    @endif
                                @else
                                    <img src="{{ asset('images/default-park.jpg') }}" alt="Imagen por defecto" class="w-full h-full object-cover">
                                @endif
                            </div>
                            
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $park->name }}</h3>
                                    <p class="flex items-center space-x-1 text-xs text-gray-600 text-gray-600 mt-1" 
                                    x-data="{ formattedAddress: formatAddress('{{ $park->location ?? '' }}') }">
                                        <x-lucide-map-pin class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" />
                                        <span x-text="formattedAddress || 'Ubicación desconocida'"></span>
                                    </p>
                                    <!-- Rating del parque -->
                                <div class="flex my-2 items-center space-x-1">
                                    @php 
                                        $rating = $park->rating ?? 0; // ✅ Evita errores si rating es null
                                        $fullStars = floor($rating);
                                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                        $averageRating = method_exists($park, 'averageRating') ? round($park->averageRating(), 1) : round($rating, 1);
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

                                    <span class="text-gray-700 text-sm font-semibold">
                                        {{ number_format($averageRating, 1) }}
                                    </span>
                                </div>
                                    <p class="text-gray-600 mt-1 font-semibold">Horario:</p>
                                    
                                    @if ($park->opening_hours)
                                        @php
                                            $openingHours = json_decode($park->opening_hours, true);
                                        @endphp
                                        @if (is_array($openingHours))
                                            <ul class="list-none text-gray-700 text-sm mt-1">
                                                @foreach ($openingHours as $day => $hours)
                                                    <li class="flex justify-between py-1 border-b border-gray-200">
                                                        <span class="font-medium">{{ ucfirst($day) }}</span>
                                                        <span>{{ $hours }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-700">{{ $park->opening_hours }}</p>
                                        @endif
                                    @else
                                        <p class="text-gray-500">No especificado</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                        </div>
                    @endif
                </div>
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-star class="w-5 h-5 text-orange-500 mr-2" />
                        Reseñas
                    </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-3 gap-6 mb-4">
                        <ul class="divide-y divide-gray-200">
                @foreach ($reviews as $review)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-white text-lg">
                                {{ substr($review->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $review->user->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="text-gray-700">{{ $review->comment }}</p>
                            <p class="text-yellow-500 text-lg">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


@extends('layouts.main')

@section('title', 'Crear Entrenamiento')

@section('content')
<div x-data="{ step: 1 }" class="max-w-4xl mx-auto p-4 mt-6">
    <a href="#" @click.prevent="if(step > 1) step--" 
       class="text-orange-500 font-medium" x-show="step > 1">&lt; Anterior</a>

    <div class="bg-white rounded-lg mt-6 shadow-md p-4">
        <!-- Indicador de Paso -->
        <h2 class="text-lg text-orange-500 font-semibold mt-4">
            Paso <span x-text="step"></span> de 4
        </h2>

        <!-- Título de cada paso -->
        <h1 class="text-2xl font-bold mt-2 text-black-500">
            <span x-show="step === 1">Datos básicos del entrenamiento</span>
            <span x-show="step === 2">Información adicional</span>
            <span x-show="step === 3">Horarios y precios</span>
            <span x-show="step === 4">Imágenes del entrenamiento</span>
        </h1>

        <!-- Formulario -->
        <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Paso 1: Datos básicos -->
            <div x-show="step === 1" class="space-y-4">
                <x-form.input name="title" label="Título *" placeholder="Ej: Clase de Yoga" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="park_id" value="{{ $selectedParkId }}">
                    <x-form.select 
                        name="park_id" 
                        label="Parque *" 
                        :options="$parks->pluck('name', 'id')" 
                        :selected="old('park_id', $selectedParkId)" 
                    />
                    </div>
                <x-form.select name="activity_id" label="Tipo de Actividad *" :options="$activities->pluck('name', 'id')" :selected="old('activity_id')" />
            </div>

            <!-- Paso 2: Descripción y nivel -->
            <div x-show="step === 2" class="space-y-4">
                <x-form.textarea name="description" label="Descripción" placeholder="Escribe una breve descripción (opcional)" />
                <x-form.radio-group 
                    name="level"
                    label="Nivel *"
                    :options="['Principiante' => 'Principiante', 'Intermedio' => 'Intermedio', 'Avanzado' => 'Avanzado']"
                    :checked="old('level')"
                />
                <x-form.input name="available_spots" type="number" label="Cupos por semana *" placeholder="Ej: 15" required />
            </div>

            <!-- Paso 3: Horarios y precios -->
            <div x-show="step === 3" class="space-y-4">
                <div class="mb-3">
                    <div class="flex justify-between items-center">
                        <h5 class="font-semibold text-gray-700">Días y Horarios</h5>
                        <x-form.button type="button" id="add-schedule" color="orange">Agregar Día y Horario</x-form.button>
                    </div>

                    <div id="schedule-container" class="mt-3">
                        @php $schedules = old('schedule.days', [[]]); @endphp
                        @foreach ($schedules as $index => $scheduleDays)
                            <div class="border rounded p-3 mb-3 schedule-item">
                                <x-form.checkbox-group 
                                    name="schedule[days][{{ $index }}][]" 
                                    label="Días:" 
                                    :options="['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']"
                                    :selected="old('schedule.days.' . $index, [])"
                                />
                                
                                <x-form.input type="time" name="schedule[start_time][{{ $index }}]" label="Hora de Inicio *" required />
                                <x-form.input type="time" name="schedule[end_time][{{ $index }}]" label="Hora de Fin *" required />

                                <div class="text-end">
                                    <x-form.button type="button" class="remove-schedule" color="red">Eliminar</x-form.button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

           
                <h5 class="font-semibold text-gray-700">Precios por Sesiones Semanales</h5>
                <div id="prices" class="mt-3">
                    @if(old('prices.weekly_sessions'))
                        @foreach (old('prices.weekly_sessions') as $index => $session)
                            <div class="border rounded p-3 mb-3">
                                <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces por Semana *" required />
                                <x-form.input type="number" name="prices[price][]" label="Precio *" required />
                            </div>
                        @endforeach
                    @else
                        <div class="border rounded p-3 mb-3">
                            <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces por Semana *" required />
                            <x-form.input type="number" name="prices[price][]" label="Precio *" required />
                        </div>
                    @endif
                </div>
                <x-form.button type="button" id="add-price-button" color="orange">Agregar Precio</x-form.button>
          
            </div>

            <!-- Paso 4: Imágenes -->
            <div x-show="step === 4" class="space-y-4">
                <x-form.file name="photos[]" label="Fotos del Entrenamiento *" accept="image/*" multiple required />
                <x-form.input name="photos_description[]" label="Descripción de la Foto (Opcional)" placeholder="Ej: Clase de Yoga al aire libre" />
            </div>

            <!-- Botones de Navegación -->
            <div class="flex justify-between">
                <button type="button" 
                        @click="if(step > 1) step--" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-md"
                        x-show="step > 1">
                    Anterior
                </button>

                <button type="button" 
                        @click="if(step < 4) step++" 
                        class="bg-orange-500 text-white px-4 py-2 rounded-md"
                        x-show="step < 4">
                    Siguiente
                </button>

                <x-form.button type="button" color="gray" onclick="window.location='{{route('trainer.calendar')}}'">Cancelar</x-form.button>
                <x-form.button type="submit" color="orange" x-show="step === 4">>Guardar Entrenamiento</x-form.button>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection