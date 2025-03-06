@extends('layouts.main')

@section('title', 'Galería de Fotos')
@extends('layouts.main')

@section('title', 'Tu Información')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 ">
<a href="#" class="text-orange-500 font-medium">&lt; Anterior</a>
    <div class="bg-white rounded-lg mt-6 shadow-md p-4">

       
        
        <h2 class="text-lg text-orange-500 font-semibold mt-4">Paso 2 de 4</h2>
        <h1 class="text-2xl font-bold mt-2 text-black-500">Datos adicioneles</h1>
        <form action="{{ route('trainings.step2') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Nombre del entrenamiento (ocupa toda la fila) -->
                      <!-- Cupos -->
<x-form.input name="available_spots" type="number" label="Cupos por semana *" placeholder="Ej: 15" required />
            <x-form.radio-group 
                name="level"
                label="Nivel *"
                :options="['Principiante' => 'Principiante', 'Intermedio' => 'Intermedio', 'Avanzado' => 'Avanzado']"
                :checked="old('level')"
            />

            <x-form.textarea name="description" label="Descripción" placeholder="Escribe una breve descripción (opcional)" />

            <div class="flex justify-end gap-2">
                <x-form.button type="submit" color="orange">Siguiente</x-form.button>
            </div>
        </form>
                
    </div>
    


@endsection