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
    <div class="w-full mb-10">
        <!-- 📸 Carrusel de fotos -->
        <div class="relative mx-auto lg:px-[25%] w-full">
            @if ($training->photos->isNotEmpty())
                @php
                    $trainingPhotos = $training->photos->pluck('photo_path')->map(fn($path) => asset('storage/' . $path))->toArray();
                @endphp

                <div x-data="{ 
                        activeSlide: 0, 
                        slides: {{ json_encode($trainingPhotos) }},
                        showModal: false,
                        next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
                        prev() { this.activeSlide = this.activeSlide === 0 ? this.slides.length - 1 : this.activeSlide - 1 }
                    }" class="relative w-full my-3">

                    <!-- 🖥️ Vista en Computadoras -->
                    <div class="hidden lg:grid gap-2"
                        :class="{
                            'grid-cols-1': slides.length === 1,
                            'grid-cols-2': slides.length === 2 || slides.length === 3,
                            'grid-cols-10': slides.length >= 4
                        }">

                        <!-- 🖼️ Foto en pantalla completa -->
                        <template x-if="slides.length === 1">
                            <img :src="slides[0]" 
                                alt="Foto principal de entrenamiento"
                                class="w-full h-[450px] object-cover cursor-pointer rounded"
                                @click="showModal = true; activeSlide = 0">
                        </template>

                        <!-- 🖼️ Dos fotos en columnas -->
                        <template x-if="slides.length === 2">
                            <div class="grid grid-cols-2 gap-2">
                                <img :src="slides[0]" class="w-full h-[350px] object-cover cursor-pointer rounded" @click="showModal = true; activeSlide = 0">
                                <img :src="slides[1]" class="w-full h-[350px] object-cover cursor-pointer rounded" @click="showModal = true; activeSlide = 1">
                            </div>
                        </template>

                        <!-- 🖼️ Tres fotos (una grande y dos pequeñas) -->
                        <template x-if="slides.length === 3">
                            <div class="grid grid-cols-2 gap-2">
                                <img :src="slides[0]" class="w-full h-[350px] object-cover cursor-pointer rounded" @click="showModal = true; activeSlide = 0">
                                <div class="flex flex-col gap-2">
                                    <img :src="slides[1]" class="w-full h-[170px] object-cover cursor-pointer rounded" @click="showModal = true; activeSlide = 1">
                                    <img :src="slides[2]" class="w-full h-[170px] object-cover cursor-pointer rounded" @click="showModal = true; activeSlide = 2">
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- 📱 Vista en móviles (carrusel con indicadores) -->
                    <div class="lg:hidden relative w-full">
                        <img :src="slides[activeSlide]" class="w-full h-[300px] object-cover cursor-pointer rounded"
                             @touchstart="startSwipe($event)" @touchend="endSwipe($event)">
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                            <template x-for="(photo, index) in slides" :key="index">
                                <button @click="activeSlide = index" 
                                    :class="activeSlide === index ? 'bg-orange-500' : 'bg-gray-300'"
                                    class="w-2 h-2 rounded-full transition-all"></button>
                            </template>
                        </div>
                    </div>

                    <!-- 📸 Modal de imágenes -->
                    <template x-if="showModal">
                        <div class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center" @click="showModal = false">
                            <div class="relative w-full max-w-4xl mx-auto shadow-lg" @click.stop>
                                <button class="absolute top-4 right-4 text-black p-2 rounded-full focus:outline-none z-50" type="button" @click="showModal = false">
                                    <x-lucide-x class="w-6 h-6 text-black" />
                                </button>
                                <div class="relative">
                                    <img :src="slides[activeSlide]" alt="Foto del entrenamiento" class="w-full max-h-[80vh] object-contain rounded">
                                    <button class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                            @click.stop="prev()">
                                        <x-lucide-chevron-left class="w-6 h-6 text-orange-500" />
                                    </button>
                                    <button class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                            @click.stop="next()">
                                        <x-lucide-chevron-right class="w-6 h-6 text-orange-500" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ❤️ Botón flotante de favoritos -->
                    <button id="floating-favorite-btn" 
                        class="absolute top-4 right-4 p-2 rounded-full bg-white shadow-md"
                        data-id="{{ $training->id }}" 
                        data-type="training"
                        data-favorite="{{ $isFavorite ? 'true' : 'false' }}">
                        <x-lucide-heart :class="$isFavorite ? 'w-6 h-6 text-orange-500 fill-current' : 'w-6 h-6 text-orange-500 stroke-current'" id="floating-favorite-icon" />
                    </button>
                </div>
            @endif
        </div>

        <!-- 📍 Fila 2: Detalles -->
        <div class="grid grid-cols-1 md:grid-cols-4 px-6 lg:px-[25%] sm:px-[6%] gap-6 items-start">
            <div class="md:col-span-3 sm:col-span-full">
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

                <p><strong>{{ $training->activity->name }}</strong> - {{ ucfirst($training->level) }}</p>

                <!-- 🔥 Calificación y reseñas -->
                <div class="mt-5 mb-3 flex items-center space-x-4">
                    <div class="p-4 rounded-lg bg-gray-50 shadow-sm flex items-center space-x-3">
                        <div class="bg-orange-500 text-white px-3 py-2 rounded-md text-lg font-bold">
                            {{ number_format($averageRating, 1) }}
                        </div>
                        <div>
                            @php
                                $ratingText = match (true) {
                                    $averageRating >= 4.5 => 'Excelente',
                                    $averageRating >= 4   => 'Muy bueno',
                                    $averageRating >= 3   => 'Está bien',
                                    $averageRating >= 2   => 'Regular',
                                    default               => 'Malo',
                                };
                            @endphp
                            <p class="font-semibold text-gray-900">{{ $ratingText }}</p>
                            <a href="#opiniones" class="text-blue-600 hover:underline">Ver {{ $training->reviews_count }} comentarios</a>
                        </div>
                    </div>
                </div>
            </div>
           <!-- 🛒 Botón de compra en Desktop -->
            <div class="hidden md:flex justify-end mb-4">
                <form action="{{ route('cart.add') }}" method="POST" 
                    class="bg-white p-5 rounded-2xl shadow-lg w-full md:w-[400px] border border-gray-200">
                    @csrf
                    <input type="hidden" name="training_id" value="{{ $training->id }}">

                    <!-- 🏷️ Opciones de precio -->
                    <div class="mb-4 bg-gray-100 px-3 py-4 rounded-md">
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Sesiones por semana</label>
                        <div class="space-y-2">
                            @foreach ($training->prices as $price)
                                <label class="flex justify-between items-center border border-gray-300 rounded-lg px-3 py-2 cursor-pointer bg-white hover:bg-gray-100">
                                    <span class="text-gray-800 font-medium">{{ $price->weekly_sessions }} veces - ${{ number_format($price->price, 0) }}</span>
                                    <input type="radio" name="weekly_sessions" value="{{ $price->weekly_sessions }}" 
                                            class="form-radio text-orange-500 focus:ring-orange-400" required>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 🛒 Botón de compra -->
                    <button type="submit" 
                            class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                        Comprar 
                    </button>
                </form>
            </div>

            <!-- 📱 Mobile: Botón fijo abajo -->
            <div class="md:hidden fixed bottom-0 left-0 w-full bg-white shadow-2xl border-t p-4 z-50">
                <button id="openModal" 
                    class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                Comprar
                </button>
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
                                <label class="flex justify-between items-center border border-gray-500 rounded-lg px-4 py-2 cursor-pointer bg-black hover:border-orange-500 text-white">
                                    <span class="text-white font-medium">{{ $price->weekly_sessions }} veces - ${{ number_format($price->price, 0) }}</span>
                                    <input type="radio" name="weekly_sessions" value="{{ $price->weekly_sessions }}" 
                                        class="form-radio text-orange-500 focus:ring-orange-400" required>
                                </label>
                            @endforeach
                        </div>

                        <!-- 🛒 Botón de Confirmación -->
                        <div class="mt-6 mb-3 flex justify-center space-x-4">
                            <button type="submit" 
                                    class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                                Confirmar reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 🛒 Modal de Aceptación del Carrito -->
            @if(session('cart_success'))
                <div x-data="{ open: true }">
                    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                        <div class="bg-black rounded-lg shadow-lg w-96 p-6">
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

        <!-- 👨‍🏫 Información del entrenador -->
        <div class="relative mx-auto px-6 border-t mt-4 lg:px-[25%] sm:px-[6%] w-full">
            <div class="flex items-center space-x-3 mt-4">
                <img src="{{ Storage::url($training->trainer->profile_pic) }}" alt="Foto de {{ $training->trainer->name }}" 
                     class="w-12 h-12 rounded-full border border-gray-300 object-cover">
                <div>
                    <p class="font-semibold text-gray-900">
                        Entrenador: 
                        <a href="{{ route('students.trainerProfile', ['id' => $training->trainer->id]) }}" class="text-orange-500 underline">
                            {{ $training->trainer->name }}
                        </a>
                    </p>
                    <p class="text-gray-500 text-sm">
                        2 {{ $training->trainer->experience }} años de experiencia
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <!-- 📝 Descripción -->
            <h3 class="text-lg mt-4 font-semibold">Descripción</h3>
            <p class="mt-2">{{ $training->description ?? 'No especificada' }}</p>
            
            <hr class="my-4">
            <!-- ⏰ Horarios -->
            <h3 class="text-lg font-semibold">Horarios</h3>
            <ul class="list-disc list-inside text-gray-700 mt-3">
                @forelse ($training->schedules as $schedule)
                    <li>{{ ucfirst($schedule->day) }}: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</li>
                @empty
                    <li class="text-gray-500">No hay horarios disponibles.</li>
                @endforelse
            </ul>
            <hr class="my-4">
            <!-- 💰 Precios -->
            <h3 class="text-lg mt-4 font-semibold">Precios</h3>
            <ul class="list-disc list-inside text-gray-700 mt-2">
                @forelse ($training->prices as $price)
                    <li>{{ $price->weekly_sessions }} veces por semana: ${{ number_format($price->price, 2) }}</li>
                @empty
                    <li class="text-gray-500">No hay precios definidos.</li>
                @endforelse
            </ul>
            <hr class="my-4">
            <!-- ⭐ Reseñas -->
            <h3 id="opiniones" class="text-lg font-semibold">Opiniones</h3>
            @if ($training->reviews->isEmpty())
                <p class="text-gray-500">No hay reseñas para este entrenamiento.</p>
            @else
                <!-- Contenedor de reseñas iniciales -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-y-2">
                    @foreach ($training->reviews->take(2) as $index => $review)
                        <div class="py-4 rounded-sm flex items-start space-x-6 
                                    {{ $index % 2 == 0 ? 'lg:pr-14' : 'lg:pl-14' }}
                                   max-sm:bg-white max-sm:mt-2 max-sm:shadow-sm max-sm:rounded-lg max-sm:p-4">
                                    
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
                                                <span class="text-sm text-gray-500">• <strong>Hace {{ $review->created_at->diffForHumans() }}</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 🏗️ Fila 2: Comentario + Botón de eliminar -->
                                    <div class="mt-3">
                                        <p class="text-gray-700 font-light">{{ $review->comment }}</p>

                                        <!-- ❌ Botón de eliminar (solo si es su comentario o admin) -->
                                        @if(auth()->id() === $review->user_id || auth()->user()->role === 'admin')
                                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" 
                                                        onclick="return confirm('¿Seguro que quieres eliminar esta reseña?')">
                                                    ❌ Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
                        <!-- ❌ Botón para cerrar -->
                        <button id="close-reviews-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                            &times;
                        </button>

                        <h3 class="text-2xl font-semibold mb-4">Todas las opiniones</h3>

                        <!-- 📜 Contenedor de todas las reseñas -->
                        <div class="overflow-y-auto h-[80vh] space-y-6">
                            @foreach ($training->reviews->sortByDesc('created_at') as $review)
                                <div class="rounded-md bg-gray-100 p-4 border-b">

                                    <!-- 🏗️ Fila 1: Foto + Nombre + Calificación -->
                                    <div class="flex items-center space-x-3">
                                        <!-- 🖼️ Foto del usuario -->
                                        <img src="{{ $review->user->profile_pic ? Storage::url($review->user->profile_pic) : asset('images/default-avatar.png') }}" 
                                            alt="Foto de {{ $review->user->name }}" 
                                            class="w-12 h-12 rounded-full border border-gray-300 object-cover shadow-sm">

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

                                        <!-- ❌ Botón de eliminar (solo si es su comentario o admin) -->
                                        @if(auth()->id() === $review->user_id || auth()->user()->role === 'admin')
                                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" 
                                                        onclick="return confirm('¿Seguro que quieres eliminar esta reseña?')">
                                                    ❌ Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <hr class="my-4">

            <!-- Formulario para agregar reseña -->
            
            @auth
            @if($hasPurchased)
                <form x-data="{ loading: false }" @submit="loading = true" action="{{ route('reviews.store') }}" method="POST" class="bg-gray-50 p-4 rounded shadow-md">
                    @csrf
                    <input type="hidden" name="training_id" value="{{ $training->id }}">

                    <label for="rating" class="block font-semibold">Calificación:</label>
                    <select name="rating" id="rating" class="border p-2 rounded w-full mt-1" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>

                    <label for="comment" class="block font-semibold mt-2">Comentario:</label>
                    <textarea name="comment" id="comment" class="border p-2 rounded w-full mt-1" rows="3" required></textarea>

                    <!-- 🔄 Spinner y Botón -->
                    <button type="submit" class="mt-2 bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 transition flex items-center justify-center w-full">
                        <span x-show="!loading">Enviar Reseña</span>
                        <svg x-show="loading" class="animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </button>
                </form>
            @else
                <p class="text-gray-500">Debes haber comprado este entrenamiento para dejar una reseña.</p>
            @endif

            @if(session('review_success'))
            <div x-data="{ open: true }">
                <!-- 🔲 Fondo Oscuro -->
                <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <!-- 📦 Modal -->
                    <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6">
                        
                        <!-- 🏷️ Encabezado -->
                        <div class="flex justify-between items-center border-b pb-2">
                            <h5 class="text-lg font-semibold text-orange-500">✅ ¡Reseña guardada!</h5>
                            <button @click="open = false" class="text-gray-500 hover:text-gray-700">✖</button>
                        </div>

                        <!-- 📜 Contenido -->
                        <div class="mt-4 text-gray-700">
                            Tu reseña ha sido enviada correctamente.
                        </div>

                        <!-- ✅ Botón de confirmación -->
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
            @endauth
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


</script>

@endsection

