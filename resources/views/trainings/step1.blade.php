@extends('layouts.main')

@section('title', 'Tu Información')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 ">
<a href="#" class="text-orange-500 font-medium">&lt; Anterior</a>
    <div class="bg-white rounded-lg mt-6 shadow-md p-4">

       
        
        <h2 class="text-lg text-orange-500 font-semibold mt-4">Paso 1 de 4</h2>
        <h1 class="text-2xl font-bold mt-2 text-black-500">Datos básicos del entrenamiento</h1>
        <form action="{{ route('trainings.step1') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Nombre del entrenamiento (ocupa toda la fila) -->
            <div class="w-full">
                <x-form.input name="title" label="Título del entrenamiento *" placeholder="Ej: Clase de Yoga" required />
            </div>

            <!-- Parque y Actividad en la misma fila -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.select name="park_id" label="Parque *" :options="$parks->pluck('name', 'id')" required />
                <x-form.select name="activity_id" label="Tipo de Actividad *" :options="$activities->pluck('name', 'id')" required />
            </div>

            <div class="flex justify-end gap-2">
                <x-form.button type="submit" color="orange">Siguiente</x-form.button>
            </div>
        </form>
                
    </div>

@endsection