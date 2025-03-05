@extends('layouts.main')

@section('title', 'Parques Asociados')

@section('content')

<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Mis parques</h1>
            @if ($parks->isEmpty())
                <p class="text-gray-500 text-center text-lg">Aún no tienes parques asociados. 🏞️</p>
            @else
                <div class="grid grid-cols-1  lg:grid-cols-3 md:grid-cols-2 gap-6">
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
</div>
<script>
    function formatAddress(address) {
    if (!address) return "Ubicación desconocida";
    
    const parts = address.split(","); // Divide la dirección en partes
    return parts.slice(0, 2).join(","); 
    }// Toma solo las primeras 2 partes
</script>
@endsection