@extends('layouts.main')

@section('title', 'Crear Usuario')

@section('content')
<div x-data="{ step: 1 }" class="max-w-4xl mx-auto p-4 mt-6">
    

    <div class="bg-white rounded-lg mt-6 shadow-md p-4">
        <!-- Indicador de Paso -->
        <h2 class="text-lg text-orange-500 font-semibold mt-4">
            Paso <span x-text="step"></span> de 3
        </h2>

        <!-- Título de cada paso -->
        <h1 class="text-2xl font-bold mt-2 text-black-500">
            <span x-show="step === 1">Registro de Alumno</span>
            <span x-show="step === 2">Información adicional</span>
            <span x-show="step === 3">Tus preferencias</span>
        </h1>

        <!-- Formulario -->
        <form action="{{ route('store.student') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Paso 1: Datos básicos -->
            <div x-show="step === 1" class="space-y-6">
                
                <!-- Nombre -->
                <x-form.input name="name" label="Nombre completo *" placeholder="Tu nombre completo" />

                <!-- Correo Electrónico -->
                <x-form.input name="email" type="email" label="Correo Electrónico *" placeholder="ejemplo@correo.com" />

                <!-- Campo de Contraseña -->
                <x-form.input name="password" type="password" label="Contraseña *" placeholder="Escribe tu contraseña" />

                <!-- Confirmación de Contraseña -->
                <x-form.input name="password_confirmation" type="password" label="Confirmar Contraseña *" placeholder="Repite tu contraseña" />

            </div>

            
            <!-- Paso 2: Información adicional -->
            <div x-show="step === 2" class="space-y-6" >

                <!-- Fecha de Nacimiento -->
                <x-form.input name="birth" type="date" label="Fecha de Nacimiento *" />

                <!-- Foto de Perfil -->
                <div class="relative">
        <!-- Label flotante -->
        <label for="profile_pic" 
               class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
               Foto de Perfil
        </label>

        <!-- Input con diseño limpio y bordes dinámicos -->
        <input
            type="file"
            id="profile_pic"
            name="profile_pic"
            accept="image/*"
            class="w-full bg-gray-50 text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
            @error('profile_pic') border-red-500 @enderror"
        >

        <!-- Mensaje de error con ícono de advertencia -->
        @error('profile_pic')
            <div class="flex items-center mt-1 text-red-500 text-xs">
                <!-- Ícono de advertencia -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m-2-2a9 9 0 110-18 9 9 0 010 18z" />
                </svg>
                <p>⚠️ {{ $message }}</p>
            </div>
        @enderror
    </div>

                <!-- Biografía -->
                <x-form.textarea name="biography" label="Breve biografía (Opcional)" placeholder="Escribe algo sobre ti..." />

                <!-- Apto Médico -->
                <div class="relative">
        <!-- Label flotante -->
        <label for="medical_fit" 
               class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
               Apto Médico
        </label>

        <!-- Input con diseño limpio y bordes dinámicos -->
        <input
            type="file"
            id="medical_fit"
            name="medical_fit"
            accept="image/*"
            class="w-full bg-gray-50 text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
            @error('medical_fit') border-red-500 @enderror"
        >

        <!-- Mensaje de error con ícono de advertencia -->
        @error('medical_fit')
            <div class="flex items-center mt-1 text-red-500 text-xs">
                <!-- Ícono de advertencia -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m-2-2a9 9 0 110-18 9 9 0 010 18z" />
                </svg>
                <p>⚠️ {{ $message }}</p>
            </div>
        @enderror
    </div>

            </div>


            <!-- Paso 3: Selección de Actividades -->
            <div x-show="step === 3" class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-700">Selecciona tus actividades</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($activities as $activity)
                        <label class="cursor-pointer flex items-center space-x-2 bg-gray-100 p-3 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-200 transition">
                            <input type="checkbox" name="activities[]" value="{{ $activity->id }}" />
                            <span class="text-gray-700 font-semibold">{{ $activity->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            
            <!-- Botones de Navegación -->
            <div class="flex justify-between">
                <button type="button" 
                        @click="if(step > 1) step--" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-md"
                        x-show="step > 1">
                    Anterior
                </button>

                <button type="button" 
                        @click="if(step < 3) step++" 
                        class="bg-orange-500 text-white px-4 py-2 rounded-md"
                        x-show="step < 3">
                    Siguiente
                </button>

                
                <button type="submit"
                    class="bg-orange-500 text-white text-md px-6 py-3 rounded-md  hover:bg-orange-600 transition"
                        x-show="step === 3"
                        x-bind:disabled="submitting"
                        @click="submitting = true">
                    Crear usuario
                </button>
            </div>
            
        </form>
        <div class="text-center mt-4">
            <p class="text-sm">¿Eres entrenador? <a href="{{ route('register.trainer') }}" class="text-orange-500 hover:underline">Regístrate aquí</a></p>
        </div>
    </div>
</div>
@endsection
       