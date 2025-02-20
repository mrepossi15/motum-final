@extends('layouts.main')

@section('title', 'Registro de Alumno')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-xl bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-center text-2xl font-semibold text-orange-500 mb-6">Registro de Alumno</h1>

        <!-- Formulario -->
        <form action="{{ route('store.student') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="role" value="alumno">

            <!-- Nombre -->
            <x-form.input name="name" label="Nombre completo *" placeholder="Tu nombre completo" />

            <!-- Correo Electrónico -->
            <x-form.input name="email" type="email" label="Correo Electrónico *" placeholder="ejemplo@correo.com" />

            <!-- Campo de Contraseña -->
            <x-form.input name="password" type="password" label="Contraseña *" placeholder="Escribe tu contraseña" />

            <!-- Confirmación de Contraseña -->
            <x-form.input name="password_confirmation" type="password" label="Confirmar Contraseña *" placeholder="Repite tu contraseña" />


            <!-- Fecha de Nacimiento -->
            <x-form.input name="birth" type="date" label="Fecha de Nacimiento *" />

            <!-- Foto de Perfil -->
            <x-form.file name="profile_pic" label="Foto de Perfil" accept="image/*" />

            <!-- Biografía -->
            <x-form.textarea name="biography" label="Breve biografía (Opcional)" placeholder="Escribe algo sobre ti..." />

            <!-- Apto Médico -->
            <x-form.file name="medical_fit" label="Apto Médico" accept="image/*" />

            <!-- Botón de registro -->
            <div>
                <button type="submit"
                        class="w-full bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600">
                    Registrar como Alumno
                </button>
            </div>
        </form>

        <!-- Enlace para registro de entrenadores -->
        <div class="text-center mt-4">
            <p class="text-sm">¿Eres entrenador? <a href="{{ route('register.trainer') }}" class="text-orange-500 hover:underline">Regístrate aquí</a></p>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush


@endsection
