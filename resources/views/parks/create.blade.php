@extends('layouts.main')

@section('title', 'Agregar parque')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-center text-2xl font-semibold text-orange-500 mb-6">Agregar parque</h1>

        <form action="{{ route('parks.store') }}" method="POST" class="space-y-4">
            @csrf
            <!-- Parque Preferido -->
           <div class="mb-6">
                        <label for="park-search" class="block text-sm font-medium text-gray-300">Buscar un parque</label>
                        <input 
                            id="park-search" 
                            name="park_search" 
                            class="mt-1 block w-full px-4 py-2 border border-gray-500 rounded-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-white placeholder-gray-500" 
                            placeholder="Escribe el nombre del parque" 
                            required>
                    </div>

                    <div id="map" class="w-full h-96 rounded-sm border border-gray-500"></div>

            <!-- Campos Ocultos -->
            <input type="hidden" id="park_name" name="park_name">
            <input type="hidden" id="lat" name="latitude">
            <input type="hidden" id="lng" name="longitude">
            <input type="hidden" id="location" name="location">
            <input type="hidden" id="opening_hours" name="opening_hours">
            <input type="hidden" name="role" value="entrenador">
            <input type="hidden" id="photo_references" name="photo_references">

           

            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('trainer.calendar') }}" class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">Agregar parque</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="/js/mapas/showMap.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush
@endsection
