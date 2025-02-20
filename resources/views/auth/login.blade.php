@extends('layouts.main')

@section('title', 'Login')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-center text-2xl font-semibold text-orange-500 mb-6">Iniciar Sesión</h1>


        <form action="/iniciar-sesion" method="POST" class="space-y-4">
            @csrf

            <!-- Email -->
            <!-- Correo Electrónico -->
            <x-form.input name="email" type="email" label="Correo Electrónico *" placeholder="ejemplo@correo.com" />

            <!-- Campo de Contraseña -->
            <x-form.input name="password" type="password" label="Contraseña *" placeholder="Escribe tu contraseña" />


            <!-- Spinner de carga -->
            <div class="text-center my-3 hidden" id="loading-spinner">
                <div class="inline-block h-6 w-6 animate-spin rounded-full border-4 border-orange-500 border-t-transparent"></div>
            </div>

            <!-- Botón de Iniciar Sesión -->
            <div>
                <button type="submit" 
                    class="w-full bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600">
                    Iniciar Sesión
                </button>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="text-center mt-4">
            <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="text-center mt-3">
            <p class="text-sm">¿Eres nuevo? <a href="{{ route('register.student') }}" class="text-orange-500 hover:underline">Regístrate aquí</a></p>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush
@endsection
