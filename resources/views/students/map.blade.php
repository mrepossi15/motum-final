@extends('layouts.main')

@section('title', 'Mapa de Parques')

@section('content')

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative w-full max-w-md mx-auto mt-4" role="alert">
        <strong class="font-bold">Error:</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            ✖
        </button>
    </div>
@endif

<!-- Contenedor de Búsqueda -->
<div class="container mx-auto mt-6 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Input de dirección -->
        <div class="w-full">
            <div class="relative">
                <input type="text" id="address-input" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Ingresa una dirección">
            </div>
        </div>
        <!-- Botón para recentrar la ubicación -->
        <div>
            <button id="recenter-btn" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg shadow hover:bg-blue-600 transition">
                📍 Mi Ubicación
            </button>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="container mx-auto mt-4 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Selección de Actividad -->
        <div>
            <select id="activity-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todas las actividades</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Selección de Radio -->
        <div>
            <select id="radius-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="1000">1 km</option>
                <option value="2000">2 km</option>
                <option value="3000">3 km</option>
                <option value="4000">4 km</option>
                <option value="5000" selected>5 km</option>
                <option value="6000">6 km</option>
                <option value="7000">7 km</option>
                <option value="8000">8 km</option>
                <option value="9000">9 km</option>
                <option value="10000">10 km</option>
            </select>
        </div>
    </div>
</div>

<!-- Mapa -->
<div id="map" class="mt-6 w-full h-[calc(100vh-150px)] relative border border-gray-300 rounded-lg shadow-lg"></div>

<!-- Spinner de Carga -->
<div id="loading-spinner" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-75 hidden z-50">
    <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-300 rounded-full animate-spin"></div>
</div>

<script src="{{ asset('js/mapas/map.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initMap" async defer></script>

@endsection