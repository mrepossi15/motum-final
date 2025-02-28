@extends('layouts.main')

@section('title', 'Mapa de Parques')

@section('content')

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        {{ session('error') }}
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <span class="text-red-500">✖</span>
        </button>
    </div>
@endif

<!-- Contenedor de Búsqueda -->
<div class="container mx-auto mt-4 px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Input de dirección -->
        <div class="md:col-span-1">
            <div class="relative">
                <input type="text" id="address-input" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Ingresa una dirección">
            </div>
        </div>
        <!-- Botón para recentrar la ubicación -->
        <div class="md:col-span-1">
            <button id="recenter-btn" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">📍 Mi Ubicación</button>
        </div>
    </div>
</div>

<div class="container mx-auto mt-3 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Selección de Actividad -->
        <div>
            <select id="activity-select" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <option value="">Todas las actividades</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Selección de Radio -->
        <div>
            <select id="radius-select" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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
<div id="map" class="mt-4 w-full h-[calc(100vh-150px)] relative"></div>

<!-- Spinner de Carga -->
<div id="loading-spinner" class="hidden fixed inset-0 flex justify-center items-center bg-white z-50">
    <div class="animate-spin rounded-full h-16 w-16 border-4 border-orange-500 border-t-transparent"></div>
</div>

<script src="{{ asset('js/mapas/map.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initMap" async defer></script>

@endsection