
@extends('layouts.main')

@section('title', 'Detalle del Parque')

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
<div class="w-full max-w-7xl mx-auto lg:px-10 mt-4">
        <!-- Contenido principal -->
        <x-image-gallery 
            :photos="$photos" 
            :title="$park->name"
            :has-floating-button="true" 
            :favorite-id="$park->id" 
            favorite-type="park" 
            :is-favorite="$isFavorite"
        />
                        <!-- Fila 2: Detalles -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-24 px-4 md:pb-6">
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
    
        </div>  
             <!-- Fila 3: Data -->
        <div class="relative mx-auto px-6 border-t mt-4 w-full"> 
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
                        class="block activity-card  hover:scale-105 cursor-pointer p-3 sm:p-4 md:p-5 text-center shadow-md rounded-lg bg-white 
                                hover:shadow-xl hover:border-orange-500 border transition duration-300 ease-in-out transform hover:-translate-y-1">
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






