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

            <!-- Confirmar Contraseña -->
           <x-form.input type="password" name="password_confirmation" label="Confirmar Contraseña *" placeholder="Repite tu contraseña" />

            <!-- Certificación Profesional -->
           <x-form.input name="certification" label="Certificación Profesional *" placeholder="Ej: Entrenador Personal Certificado" value="{{ old('certification') }}" />

            <!-- Foto de Certificación -->
           <x-form.input type="file" name="certification_pic" label="Foto de la Certificación (Opcional)" />

            <!-- Biografía -->
            <x-form.textarea name="biography" label="Breve Biografía (Opcional)" placeholder="Escribe una breve biografía (máximo 500 caracteres)">{{ old('biography') }}</x-form.textarea>

            <!-- Collector ID -->
           <x-form.input name="collector_id" label="Collector ID (Opcional)" placeholder="Tu ID de Mercado Pago" value="{{ old('collector_id') }}" />

            <!-- Especialidad -->
           <x-form.input name="especialty" label="Áreas de Especialidad (Opcional)" placeholder="Ej: Funcional, CrossFit, Yoga, HIIT" value="{{ old('especialty') }}" />

            <!-- Fecha de Nacimiento -->
           <x-form.input type="date" name="birth" label="Fecha de Nacimiento *" max="{{ now()->subYears(18)->format('Y-m-d') }}" value="{{ old('birth') }}" />

            <!-- Foto de Perfil -->
           <x-form.input type="file" name="profile_pic" label="Foto de Perfil (Opcional)" />

            <!-- Apto Médico -->
           <x-form.input type="file" name="medical_fit" label="Apto Médico (Opcional)" />

            <!-- Parque Preferido -->
           <x-form.input name="park_name" label="Parque de Preferencia *" placeholder="Escribe el nombre del parque" value="{{ old('park_name') }}" />

            <!-- Mapa -->
            <div id="map" class="h-64 border rounded-lg"></div>

            <!-- Campos Ocultos -->
            <input type="hidden" id="lat" name="latitude">
            <input type="hidden" id="lng" name="longitude">
            <input type="hidden" id="location" name="location">
            <input type="hidden" id="opening_hours" name="opening_hours">
            <input type="hidden" name="role" value="entrenador">
            <input type="hidden" id="photo_references" name="photo_references">

            <!-- Experiencia Laboral -->
            <div>
                <h5 class="text-lg font-semibold mb-2">Años de Experiencia (Opcional)</h5>
                <div id="experience-container" class="space-y-4">
                    <div class="border rounded-lg p-4 bg-gray-50 experience-item">
                        <h6 class="font-medium">Experiencia #1</h6>

                       <x-form.input name="experiences[0][role]" label="Rol" placeholder="Ej: Entrenador personal" value="{{ old('experiences.0.role') }}" />

                       <x-form.input name="experiences[0][company]" label="Empresa o Gimnasio" placeholder="Ej: Gimnasio XYZ" value="{{ old('experiences.0.company') }}" />

                       <x-form.input type="number" name="experiences[0][year_start]" label="Año de Inicio" placeholder="Ej: 2020" min="1900" max="{{ now()->year }}" value="{{ old('experiences.0.year_start') }}" />

                       <x-form.input type="number" name="experiences[0][year_end]" label="Año de Fin" placeholder="Ej: {{ now()->year }}" min="1900" max="{{ now()->year }}" value="{{ old('experiences.0.year_end') }}" />

                        <div class="flex items-center">
                            <input type="hidden" name="experiences[0][currently_working]" value="0">
                            <input type="checkbox" id="currently-working-0" name="experiences[0][currently_working]" value="1" {{ old('experiences.0.currently_working') ? 'checked' : '' }} class="h-4 w-4 text-orange-500 focus:ring-orange-500">
                            <label for="currently-working-0" class="ml-2 text-sm text-gray-700">Actualmente trabajando aquí</label>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-experience" class="mt-2 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                    Agregar Otra Experiencia
                </button>
            </div>

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

@endsection
