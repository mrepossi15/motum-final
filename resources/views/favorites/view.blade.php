@extends('layouts.main')

@section('title', 'Mis Favoritos')

@section('content')

<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Favoritos</h1>

        <!-- 📌 Tabs para Filtrar Favoritos -->
        <div class="flex border-b mb-6 space-x-6">
            <button id="show-trainings-btn" class="pb-2 border-b-2 text-orange-600 font-semibold border-orange-600">
                Entrenamientos
            </button>
            <button id="show-parks-btn" class="pb-2 text-gray-600 hover:text-orange-600 hover:border-orange-600 transition">
                Parques
            </button>
        </div>

        <!-- 📌 Contenedor Principal -->
        <div class="space-y-8">
            
            <!-- 🔥 Entrenamientos Favoritos -->
            <div id="trainings-container">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Entrenamientos Favoritos</h2>
                @if($favoriteTrainings->isEmpty())
                    <p class="text-gray-700">No tienes entrenamientos guardados.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($favoriteTrainings as $favorite)
                            @php
                                $training = $favorite->favoritable;
                            @endphp
                            @if ($training) <!-- 🔥 Verifica si existe -->
                                <a href="{{ route('trainings.selected', $training->id) }}" 
                                class="block bg-white shadow-md rounded-lg overflow-hidden border w-full mx-auto h-auto 
                                        flex flex-col transition hover:shadow-lg">
                                    
                                    <!-- 📷 Imagen -->
                                    <div class="relative w-full h-56">
                                        <img src="{{ $training->photos->isNotEmpty() ? asset('storage/' . $training->photos->first()->photo_path) : asset('images/default-training.jpg') }}" 
                                            alt="Foto de {{ $training->title }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    <!-- 📄 Contenido -->
                                    <div class="p-4">
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            {{ $training->title ?? 'Entrenamiento no disponible' }}
                                        </h4>
                                         <!-- 📍 Ubicación -->
                                         <p class="flex items-center space-x-1 text-xs text-gray-500">
                                            <x-lucide-map-pin class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" />
                                            <span>{{ $training->park->name }}</span>
                                        </p>

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

               
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 🌳 Parques Favoritos -->
            <div id="parks-container" class="hidden">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Parques Favoritos</h2>
                @if($favoriteParks->isEmpty())
                    <p class="text-gray-700">No tienes parques guardados.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($favoriteParks as $favorite)
                            @php
                                $park = $favorite->favoritable;
                                $photos = !empty($park->photo_urls) ? json_decode($park->photo_urls, true) : [];
                            @endphp
                            <a href="{{ route('parks.show', $park->id) }}" 
                               class="block bg-white shadow-md rounded-lg overflow-hidden border w-full mx-auto h-auto 
                                    flex group flex-col transition hover:shadow-lg">
                                
                                <!-- 📷 Imagen -->
                                <div class="relative w-full h-56">
                                    <img src="{{ $photos[0] ?? asset('images/default-park.jpg') }}" 
                                        alt="Foto de {{ $park->name }}"
                                        class="w-full h-full object-cover">
                                        
                                </div>

                                <!-- 📄 Contenido -->
                                <div class="p-4">
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{ $park->name ?? 'Parque no disponible' }}
                                    </h4>
                                    <p class="flex items-center space-x-1 text-xs text-gray-500" 
                                    x-data="{ formattedAddress: formatAddress('{{ $park->location ?? '' }}') }">
                                        <x-lucide-map-pin class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" />
                                        <span x-text="formattedAddress || 'Ubicación desconocida'"></span>
                                    </p>
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
                                </div>
                               
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- Script para Tabs -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const showTrainingsBtn = document.getElementById("show-trainings-btn");
        const showParksBtn = document.getElementById("show-parks-btn");
        const trainingsContainer = document.getElementById("trainings-container");
        const parksContainer = document.getElementById("parks-container");

        function switchTab(activeBtn, inactiveBtn, activeContainer, inactiveContainer) {
            activeContainer.classList.remove("hidden");
            inactiveContainer.classList.add("hidden");
            activeBtn.classList.add("border-b-2", "text-orange-600", "border-orange-600");
            inactiveBtn.classList.remove("border-b-2", "text-orange-600", "border-orange-600");
            inactiveBtn.classList.add("text-gray-600", "hover:text-orange-600", "hover:border-orange-600");
        }

        showTrainingsBtn.addEventListener("click", function () {
            switchTab(showTrainingsBtn, showParksBtn, trainingsContainer, parksContainer);
        });

        showParksBtn.addEventListener("click", function () {
            switchTab(showParksBtn, showTrainingsBtn, parksContainer, trainingsContainer);
        });
    });
    function formatAddress(address) {
    if (!address) return "Ubicación desconocida";
    
    const parts = address.split(","); // Divide la dirección en partes
    return parts.slice(0, 2).join(","); // Toma solo las primeras 2 partes
}
</script>

@endsection