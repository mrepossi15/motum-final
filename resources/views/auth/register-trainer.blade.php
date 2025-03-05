@extends('layouts.main')

@section('title', 'Registro de Entrenador')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-center text-2xl font-semibold text-orange-500 mb-6">Registro de Entrenador</h1>

        <form action="{{ route('store.trainer') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="role" value="entrenador">

            <!-- Nombre -->
           <x-form.input name="name" label="Nombre completo *" placeholder="Tu nombre completo" value="{{ old('name') }}" />

            <!-- Correo Electrónico -->
           <x-form.input type="email" name="email" label="Correo Electrónico *" placeholder="ejemplo@correo.com" value="{{ old('email') }}" />

            <!-- Mercado Pago Email -->
           <x-form.input type="email" name="mercado_pago_email" label="Correo Mercado Pago *" placeholder="email@mercadopago.com" value="{{ old('mercado_pago_email') }}" />

            <!-- Contraseña -->
           <x-form.input type="password" name="password" label="Contraseña *" placeholder="Crea una contraseña" />
           <div class="mb-4">
    <label for="phone" class="block text-sm font-medium text-gray-700">Collector ID (Opcional)</label>
    <input type="text" name="phone" id="phone" 
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 
           @error('phone') border-red-500 @enderror" 
           value="{{ old('phone') }}">
    <p class="mt-1 text-sm text-gray-500">Ingresa tu Collector ID de Mercado Pago.</p>
    @error('collector_id')
        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>
            <!-- Confirmar Contraseña -->
           <x-form.input type="password" name="password_confirmation" label="Confirmar Contraseña *" placeholder="Repite tu contraseña" />

            <!-- Certificación Profesional -->
           <x-form.input name="certification" label="Certificación Profesional *" placeholder="Ej: Entrenador Personal Certificado" value="{{ old('certification') }}" />

            <!-- Foto de Certificación -->
           <x-form.input type="file" name="certification_pic" label="Foto de la Certificación (Opcional)" />

            <!-- Biografía -->
            <x-form.textarea name="biography" label="Breve Biografía (Opcional)" placeholder="Escribe una breve biografía (máximo 500 caracteres)">{{ old('biography') }}</x-form.textarea>

            <!-- Especialidad -->
           <x-form.input name="especialty" label="Áreas de Especialidad (Opcional)" placeholder="Ej: Funcional, CrossFit, Yoga, HIIT" value="{{ old('especialty') }}" />

            <!-- Fecha de Nacimiento -->
           <x-form.input type="date" name="birth" label="Fecha de Nacimiento *" max="{{ now()->subYears(18)->format('Y-m-d') }}" value="{{ old('birth') }}" />

            <!-- Foto de Perfil -->
           <x-form.input type="file" name="profile_pic" label="Foto de Perfil (Opcional)" />

            <!-- Apto Médico -->
           <x-form.input type="file" name="medical_fit" label="Apto Médico (Opcional)" />

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
            <input type="hidden" id="rating" name="rating">
            <input type="hidden" id="reviews" name="reviews">

           

            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">Registrar como Entrenador</button>
            </div>
        </form>

        <div class="text-center mt-6">
            <p>¿Eres alumno? <a href="{{ route('register.student') }}" class="text-orange-500 hover:underline">Regístrate aquí</a></p>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="/js/mapas/showMap.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush
@endsection
