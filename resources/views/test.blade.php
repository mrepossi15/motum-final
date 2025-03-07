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
                <x-form.file name="profile_pic" label="Foto de Perfil" accept="image/*" />

                <!-- Biografía -->
                <x-form.textarea name="biography" label="Breve biografía (Opcional)" placeholder="Escribe algo sobre ti..." />

                <!-- Apto Médico -->
                <x-form.file name="medical_fit" label="Apto Médico" accept="image/*" />

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
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/entrenamientos/create.js') }}"></script>
@endpush
@endsection