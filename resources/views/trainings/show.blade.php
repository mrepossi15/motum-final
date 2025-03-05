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

<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto  lg:px-10">
        <!-- 📸 Carrusel de fotos -->
        <div class="relative mx-auto w-full"> 
            <!-- Carrusel de Fotos del Entrenamiento -->
            @if ($training->photos->isNotEmpty())
                @php
                    $photos = $training->photos->pluck('photo_path')->map(fn($path) => asset('storage/' . $path))->toArray();
                @endphp

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
                    <div class="hidden lg:grid grid-cols-4 gap-2">
                        <!-- Caso: Solo 1 foto -->
                        @if(count($photos) === 1)
                            <div class="col-span-4">
                                <img src="{{ asset($photos[0]) }}"
                                    alt="Foto principal de {{ $training->title }}"
                                    class="w-full h-[350px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 0">
                            </div>

                            <!-- Caso: 2 fotos -->
                        @elseif(count($photos) === 2)
                            <div class="col-span-3">
                                <img src="{{ asset($photos[0]) }}"
                                    alt="Foto principal"
                                    class="w-full h-[350px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 0">
                            </div>
                            <div class="col-span-1">
                                <img src="{{ asset($photos[1]) }}"
                                    alt="Foto secundaria"
                                    class="w-full h-[350px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 1">
                            </div>

                            <!-- Caso: 3 fotos -->
                        @elseif(count($photos) === 3)
                            <div class="col-span-3">
                                <img src="{{ asset($photos[0]) }}"
                                    alt="Foto principal"
                                    class="w-full h-[350px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 0">
                            </div>
                            <div class="col-span-1 grid grid-rows-2 gap-2">
                                <img src="{{ asset($photos[1]) }}"
                                    alt="Foto secundaria"
                                    class="w-full h-[170px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 1">
                                <img src="{{ asset($photos[2]) }}"
                                    alt="Foto terciaria"
                                    class="w-full h-[170px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 2">
                            </div>

                        <!-- Caso: 4 fotos -->
                        @elseif(count($photos) >= 4)
                            <div class="col-span-3">
                                <img src="{{ asset($photos[0]) }}"
                                    alt="Foto principal"
                                    class="w-full h-[350px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 0">
                            </div>
                            <div class="col-span-1 grid grid-rows-2 gap-2">
                                <img src="{{ asset($photos[1]) }}"
                                    alt="Foto secundaria"
                                    class="w-full h-[170px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 1">
                                <div class="grid grid-cols-2 gap-2">
                                    <img src="{{ asset($photos[2]) }}"
                                        alt="Foto terciaria"
                                        class="w-full h-[170px] object-cover cursor-pointer"
                                        @click="showModal = true; activeSlide = 2">
                                    <img src="{{ asset($photos[3]) }}"
                                        alt="Foto cuarta"
                                        class="w-full h-[170px] object-cover cursor-pointer"
                                        @click="showModal = true; activeSlide = 3">
                                </div>
                            </div>
                        @endif
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
                            alt="Foto de {{ $training->title }}"
                            class="w-full h-[300px] object-cover"
                            @touchstart="startSwipe($event)"
                            @click="showModal = true; activeSlide = 1"
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
                            <div class="relative w-full max-w-4xl mx-auto shadow-lg" @click.stop
                                x-data="{ 
                                    touchStartX: 0, 
                                    touchEndX: 0, 
                                    startSwipe(event) { this.touchStartX = event.touches[0].clientX; },
                                    endSwipe(event) { 
                                        this.touchEndX = event.changedTouches[0].clientX;
                                        let diff = this.touchStartX - this.touchEndX;
                                        if (Math.abs(diff) > 50) {
                                            if (diff > 0) { next(); } 
                                            else { prev(); }
                                        }
                                    }
                                }"
                                @touchstart="startSwipe($event)" 
                                @touchend="endSwipe($event)"
                            >

                                <!-- ❌ Botón de Cerrar -->
                                <button class="absolute top-4 right-4 p-2 rounded-full  z-50 focus:outline-none" type="button" @click="showModal = false">
                                    <x-lucide-x class="w-6 h-6 text-gray-900" />
                                </button>

                                <!-- 📸 Contenedor de Imagen -->
                                <div class="relative">
                                    <img :src="slides[activeSlide]" alt="Foto del entrenamiento" class="w-full max-h-[80vh] object-contain">
                                    
                                    <!-- ⬅ Botón Anterior (solo en lg+) -->
                                    <button class="hidden lg:flex absolute top-1/2 left-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                            @click.stop="prev()">
                                        <x-lucide-chevron-left class="w-6 h-6 text-orange-500" />
                                    </button>

                                    <!-- ➡ Botón Siguiente (solo en lg+) -->
                                    <button class="hidden lg:flex absolute top-1/2 right-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                            @click.stop="next()">
                                        <x-lucide-chevron-right class="w-6 h-6 text-orange-500" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Botón flotante dentro de la imagen (para tablets y celulares) -->
                    <div class="absolute top-0 right-4 sm:right-6 lg:right-8 mt-4 z-10">
                        <div class="relative">
                            <!-- Botón de opciones -->
                            <button class="bg-white text-black px-3 py-1 rounded-md shadow" onclick="toggleDropdown()">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>

                            <!-- Menú desplegable -->
                            <ul id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md hidden z-20">
                                <li>
                                    <a href="{{ route('trainings.edit', ['id' => $training->id, 'date' => $selectedDate ?? now()->toDateString()]) }}" 
                                        class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                                        Editar
                                    </a>
                                </li>

                                <li>
                                    <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100 
                                                focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50" 
                                            onclick="toggleModal()">
                                        Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- 📍 Fila 2: Prinicpal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-4 px-4 md:pb-6">
            <div class="md:col-span-2">
                <!-- 🏋️ Título del entrenamiento -->
                <h1 class="text-2xl sm:text-3xl my-2 font-bold text-gray-900 flex items-center">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-black rounded-sm flex items-center justify-center p-2 mr-2">
                        <x-lucide-dumbbell class="w-5 h-5 sm:w-6 sm:h-6 text-orange-500" />
                    </div>
                    {{ $training->title }}
                </h1>

                <!-- ⭐ Calificación -->
                @php
                    $averageRating = round($training->averageRating(), 1);
                    $fullStars = floor($averageRating);
                    $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                @endphp

                <div class="flex my-2 items-center space-x-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <x-lucide-star class="w-4 h-4 sm:w-5 sm:h-5 {{ $i <= $fullStars ? 'text-orange-500 fill-current' : ($hasHalfStar && $i == $fullStars + 1 ? 'text-orange-500' : 'text-gray-300') }}" />
                    @endfor
                    <span class="text-gray-700 text-sm font-semibold">
                        {{ number_format($averageRating, 1) }}
                    </span>
                </div>

                <!-- 📍 Ubicación -->
                <p class="text-gray-600 text-xs sm:text-sm flex items-center space-x-1 my-2">
                    <x-lucide-map-pin class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" />
                    <span>{{ $training->park->name }} - {{ $training->park->location }}</span>
                </p>
            </div>
            <!-- 📱 Btn de selección de sesiones en Mobile -->
            <div class="md:hidden fixed bottom-0 left-0 w-full bg-white shadow-2xl border-t p-4 z-50">
                <div class="flex justify-center">
                    <div class="w-full max-w-[400px]">
                        
                            @if ($isClassAccessible)
                                <a href="{{ $reservationDetailUrl }}" >
                                <button id="apply-filters-btn" class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                                        Tomar lista
                                </button>
                                </a>
                            @else
                                <button  class="bg-orange-300 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition" disabled>
                                    {{ $accessMessage }}
                                </button>
                            @endif
                        
                    </div>
                </div>
            </div>

            <!-- 📱 Modal de selección de sesiones en Mobile -->
            <div id="sessions-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-end md:items-center z-50">
                <div id="sessions-content" class="bg-[#1E1E1E] p-6 rounded-t-lg md:rounded-lg w-full max-w-md md:max-w-lg shadow-lg relative transform translate-y-full transition-transform duration-300 ease-in-out">
                    
                    <!-- Barra de swipe en mobile -->
                    <div class="h-1 w-12 bg-gray-500 rounded-full mx-auto mb-3 md:hidden"></div>

                    <!-- ❌ Botón de Cerrar -->
                    <button id="close-sessions-btn" class="absolute top-3 right-3 text-white hover:text-red-500">
                        <x-lucide-x class="w-6 h-6" />
                    </button>

                    <!-- 🏋️ Título -->
                    <h2 class="text-lg text-white mb-4">Cantidad de sesiones</h2>

                    <!-- 📋 Formulario de selección -->
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="training_id" value="{{ $training->id }}">

                        <div class="space-y-2">
                            @foreach ($training->prices as $price)
                                <label class="flex justify-between items-center border bg-black text-white hover:border-orange-500 border border-gray-500 rounded-sm px-4 py-[14px] mt-2 appearance-none">
                                    <span class="text-white font-medium">{{ $price->weekly_sessions }} veces - ${{ number_format($price->price, 0) }}</span>
                                    <input type="radio" name="weekly_sessions" value="{{ $price->weekly_sessions }}" 
                                        class="form-radio text-orange-500 focus:ring-orange-500" required>
                                </label>
                            @endforeach
                        </div>
                        
                        <!-- 🛒 Botón de Confirmación -->
                        <div class="mt-6 mb-3 flex justify-center space-x-4">
                            <button type="submit" 
                                    class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-500 transition">
                                Confirmar reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 🛒 Botón de compra en Desktop (oculto en móvil) -->
            <div class="hidden md:block bg-white shadow-lg rounded-lg p-4 md:sticky md:top-4 md:self-start h-auto w-full border-t md:border-none">
                @if ($isClassAccessible)
                    <a href="{{ $reservationDetailUrl }}" >
                    <button id="apply-filters-btn" class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                            Tomar lista
                    </button>
                    </a>
                @else
                    <button  class="bg-orange-300 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition" disabled>
                        {{ $accessMessage }}
                    </button>
                @endif
            </div>

            <!-- 🛒 Modal de Aceptación del Carrito -->
            @if(session('cart_success'))
                <div x-data="{ open: true }">
                    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                        <div class="bg-[#1E1E1E] rounded-lg shadow-lg w-96 p-6">
                            <div class="flex justify-between items-center border-b pb-2">
                                <h5 class="text-lg font-semibold text-orange-500">¡Agregado al carrito!</h5>
                                <button @click="open = false" class="text-white hover:text-white">
                                <x-lucide-x class="w-6 h-6" />
                                </button>
                            </div>

                            <div class="mt-4 text-white">
                                 {{ session('cart_success') }}
                            </div>

                            <div class="mt-4 text-right">
                                <button @click="open = false" 
                                        class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 transition">
                                    Aceptar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- 👨‍🏫 Fila 3-->
        <div class="relative mx-auto md:px-6 border-t px-4  w-full">
              <!-- 📝 Data junta -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 pt-4">
                <!-- 📅 Fecha -->
                <div class="text-left sm:text-center border-b sm:border-b-0 sm:border-l max-sm:pb-4 md:border-l-0 md:px-4">
                    <div class="flex sm:justify-center items-center space-x-2">
                        <x-lucide-calendar class="w-5 h-5 text-orange-500" />
                        <p class="text-md text-black font-semibold">Fecha</p>
                    </div>
                    <p class="text-gray-700">
                        {{ \Carbon\Carbon::parse($selectedDate)->locale('es')->translatedFormat('l d/m/Y') }}
                    </p>
                </div>

                <!-- 🏋️‍♂️ Actividad -->
                <div class="text-left sm:text-center border-b sm:border-b-0 sm:border-l max-sm:pb-4 md:px-4">
                    <div class="flex sm:justify-center items-center space-x-2">
                        <x-lucide-dumbbell class="w-5 h-5 text-orange-500" />
                        <p class="text-md text-black font-semibold">Actividad</p>
                    </div>
                    <p class="text-gray-700">{{ $training->activity->name }}</p>
                </div>

                <!-- 🔥 Nivel -->
                <div class="text-left sm:text-center border-b sm:border-b-0 sm:border-l max-sm:pb-4  md:px-4">
                    <div class="flex sm:justify-center items-center space-x-2">
                        <x-lucide-bar-chart class="w-5 h-5 text-orange-500" />
                        <p class="text-md text-black font-semibold">Nivel</p>
                    </div>
                    <p class="text-gray-700">{{ ucfirst($training->level) }}</p>
                </div>

                <!-- 🕒 Horario -->
                <div class="text-left sm:text-center border-b sm:border-b-0 sm:border-l max-sm:pb-4 md:px-4">
                    <div class="flex sm:justify-center items-center space-x-2">
                        <x-lucide-clock class="w-5 h-5 text-orange-500" />
                        <p class="text-md text-black font-semibold">Horario</p>
                    </div>
                    @if($filteredSchedules->isNotEmpty())
                        @php
                            $schedule = $filteredSchedules->first();
                        @endphp
                        <p class="text-gray-700">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') ?? 'No disponible' }} - 
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') ?? 'No disponible' }}
                        </p>

                        @if($schedule->is_exception)
                            <div class="mt-1">
                            <span class="bg-yellow-500 text-white px-2 py-1 text-xs rounded-lg w-full sm:block">
                                Horario Modificado
                            </span>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-700">No disponible</p>
                    @endif
                </div>

                <!-- 🎟 Cupos -->
                <div class="text-left sm:text-center sm:border-l md:px-4 ">
                    <div class="flex sm:justify-center items-center space-x-2">
                        <x-lucide-users class="w-5 h-5 text-orange-500" />
                        <p class="text-md text-black font-semibold">Cupos</p>
                    </div>
                    <p class="text-gray-700">
                        {{ $filteredReservations->has($selectedTime) ? $filteredReservations[$selectedTime]->count() : 0 }} /
                        {{ $training->available_spots ?? 'No especificados' }}
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <!-- 📝 Descripción -->
            <h3 class="text-lg mt-4 font-semibold">Descripción</h3>
            <p class="mt-2">{{ $training->description ?? 'No especificada' }}</p>
            <hr class="my-4">
            

            <!-- 💰 Precios -->
            <h3 class=" font-semibold text-lg  text-gray-900 mb-4">Precios del Entrenamiento</h3>
            <div class="space-y-3  ">
                @forelse ($training->prices as $price)
                    <div class="flex items-center  border-gray-200 border-b pb-2 ">
                        <!-- Icono -->
                        <x-lucide-wallet class="w-6 h-6 text-orange-500 mr-3" />

                        <!-- Descripción -->
                        <div class="flex-1">
                            <p class="text-gray-800 font-medium ">
                                {{ $price->weekly_sessions }} {{ $price->weekly_sessions == 1 ? 'vez' : 'veces' }} por semana
                            </p>
                        </div>

                        <!-- Precio -->
                        <span class="text-lg font-bold text-orange-600">
                            ${{ number_format($price->price, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">No hay precios definidos.</p>
                @endforelse
            </div>
            <hr class="my-4">

            <!-- ⭐ Reseñas -->
            <h3 id="opiniones" class="text-lg font-semibold my-3">Opiniones</h3>
            @if ($training->reviews->isEmpty())
                <p class="text-gray-500">No hay reseñas para este entrenamiento.</p>
            @else
                <!-- Contenedor de reseñas iniciales -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4  gap-y-2">
                    @foreach ($training->reviews->take(2) as $index => $review)
                        <div class="bg-gray-50 py-4 px-6 rounded-md shadow-md flex items-start space-x-6">           
                            <div>
                                <!-- ⭐ Calificación -->
                                <div class="grid grid-cols-12 gap-4 items-center">
                                        <!-- 🖼️ Foto del usuario -->
                                        <div class="col-span-2 flex justify-center">
                                            <img src="{{ $review->user->profile_pic ? Storage::url($review->user->profile_pic) : asset('images/default-avatar.png') }}" 
                                            alt="Foto de {{ $review->user->name }}" 
                                            class="w-14 h-14 rounded-full border border-gray-300 object-cover">
                                        </div>

                                        <!-- 👤 Nombre + ⭐ Calificación -->
                                        <div class="col-span-10">
                                            <p class="font-semibold text-gray-900">{{ $review->user->name }}</p>
                                            <div class="flex items-center space-x-1 mt-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <x-lucide-star class="w-4 h-4 {{ $i <= $review->rating ? 'text-orange-500 fill-current' : 'text-gray-300' }}" />
                                                @endfor
                                                <p class="text-sm text-gray-500"><strong>{{ \Carbon\Carbon::parse($review->created_at)->locale('es')->diffForHumans() }}</strong></p>
                                            </div>
                                        </div>
                                </div>

                                <!-- 🏗️ Fila 2: Comentario + Botón de eliminar -->
                                <div class="mt-3">
                                    <p class="text-gray-700 font-light">{{ $review->comment }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Botón para abrir el modal -->
                
                @if ($training->reviews->count() > 2)
                    <!-- Botón para abrir el modal -->
                    <div class="flex justify-end mt-2">
                        <button id="open-reviews-modal" class="text-orange-500 font-semibold underline hover:underline">
                            Ver más opiniones
                        </button>
                    </div>
                @endif

                <!-- Modal de reseñas -->
                <div id="reviews-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
                    <div id="reviews-content" class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-4xl shadow-lg relative transform transition-transform duration-300 ease-in-out h-[90vh] overflow-hidden">
                        <!-- ❌ Botón para cerrar -->
                        <button id="close-reviews-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                            &times;
                        </button>

                        <h3 class="text-2xl font-semibold mb-4">Todas las opiniones</h3>

                        <!-- 📜 Contenedor de todas las reseñas -->
                        <div class="overflow-y-auto h-[80vh]  space-y-6">
                            @foreach ($training->reviews->sortByDesc('created_at') as $review)
                                <div class="rounded-sm bg-white p-2 ">

                                    <!-- 🏗️ Fila 1: Foto + Nombre + Calificación -->
                                    <div class="flex items-center space-x-3">
                                        <!-- 🖼️ Foto del usuario -->
                                        <img src="{{ $review->user->profile_pic ? Storage::url($review->user->profile_pic) : asset('images/default-avatar.png') }}" 
                                            alt="Foto de {{ $review->user->name }}" 
                                            class="w-12 h-12 rounded-full border-2 border-orange-300 object-cover shadow-sm">

                                        <!-- 👤 Nombre + ⭐ Calificación -->
                                        <div>
                                            <p class="font-semibold text-gray-900 leading-tight">{{ $review->user->name }}</p>
                                            <div class="flex items-center space-x-1 mt-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <x-lucide-star class="w-4 h-4 {{ $i <= $review->rating ? 'text-orange-500 fill-current' : 'text-gray-300' }}" />
                                                @endfor
                                                <span class="text-sm text-gray-500">• <strong>Hace {{ $review->created_at->diffForHumans() }}</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 🏗️ Fila 2: Comentario + Botón de eliminar -->
                                    <div class="mt-3">
                                        <p class="text-gray-700 font-light">{{ $review->comment }}</p>

                                        @if(auth()->id() === $review->user_id || auth()->user()->role === 'admin')
                                            <button type="button" 
                                                onclick="openDeleteModal('{{ route('reviews.destroy', $review->id) }}')" 
                                                class="text-red-500 hover:text-red-700 mt-2">
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <hr class="my-4">
        </div>
        
    </div>
</div>
   
<div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-[#1E1E1E] rounded-lg shadow-lg w-96 p-6 relative">
        <!-- ❌ Botón para cerrar -->
        <button onclick="toggleModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
            <x-lucide-x class="w-6 h-6" />
        </button> 
         <!-- 🏷️ Encabezado -->
        <h5 class="text-lg font-semibold text-orange-500" id="deleteModalLabel">Confirmar Eliminación</h5>
                                                <!-- 📜 Contenido -->
        <p class="mt-4 text-white">¿Estás seguro de que deseas suspender este entrenamiento? Esta acción no se puede deshacer</p>

                                                <!-- ✅ Botones de acción -->
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="toggleModal()" 
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                Cancelar
            </button>
            <form action="{{ route('trainings.suspend') }}" method="POST" class="ml-3">
                @csrf
                <input type="hidden" name="training_id" value="{{ $training->id }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <button type="submit" 
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    Suspender Clase
                </button>
            </form>
        </div>
    </div>  
</div>                                    

<script>
    function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('hidden');
    }

    function toggleModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = (modal.style.display === "none" || modal.style.display === "") ? "flex" : "none";
    }
    //favoritos
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
    ///modal reviews
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("reviews-modal");
        const openModalButton = document.getElementById("open-reviews-modal"); // Asegúrate de tener un botón con este ID
        const closeModalButton = document.getElementById("close-reviews-modal");

        // Función para abrir el modal y bloquear el scroll
        function openModal() {
            modal.classList.remove("hidden");
            document.body.classList.add("overflow-hidden"); // Bloquea el scroll de fondo
        }

        // Función para cerrar el modal y desbloquear el scroll
        function closeModal() {
            modal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden"); // Habilita el scroll de fondo
        }

        // Evento para abrir el modal
        if (openModalButton) {
            openModalButton.addEventListener("click", openModal);
        }

        // Evento para cerrar el modal
        if (closeModalButton) {
            closeModalButton.addEventListener("click", closeModal);
        }

        // Cerrar con la tecla ESC
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                closeModal();
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        let modal = document.getElementById("sessions-modal");
        let modalContent = document.getElementById("sessions-content");
        let openModalBtn = document.getElementById("openModal");
        let closeModalBtn = document.getElementById("close-sessions-btn");

        // 🟢 ABRIR MODAL
        openModalBtn.addEventListener("click", function () {
            modal.classList.remove("hidden"); // Hace visible el modal
            setTimeout(() => {
                modalContent.classList.remove("translate-y-full");
            }, 10); // Pequeño delay para suavizar la animación
        });

        // ❌ CERRAR MODAL
        closeModalBtn.addEventListener("click", function () {
            modalContent.classList.add("translate-y-full");
            setTimeout(() => {
                modal.classList.add("hidden");
            }, 300); // Espera la animación antes de ocultarlo
        });

        // ⬆️ Cerrar tocando fuera del modal
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                modalContent.classList.add("translate-y-full");
                setTimeout(() => {
                    modal.classList.add("hidden");
                }, 300);
            }
        });
    });
    //CONFIRMRAR BORRAR
    function openDeleteModal(action) {
        document.getElementById("delete-modal").classList.remove("hidden");
        document.getElementById("delete-form").setAttribute("action", action);
        document.body.classList.add("overflow-hidden"); // Bloquea el scroll
    }

    function closeDeleteModal() {
        document.getElementById("delete-modal").classList.add("hidden");
        document.body.classList.remove("overflow-hidden"); // Restaura el scroll
    }

    // Cerrar modal con la tecla ESC
    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            closeDeleteModal();
        }
    });


</script>

@endsection


