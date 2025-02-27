@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')
<div class="container mx-auto mt-5 px-4">
    <h2 class="mb-4 text-2xl font-bold text-gray-800">Mis Entrenamientos</h2>

    @if ($trainings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($trainings as $training)
                <div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105">
                    @if ($training->photos->isNotEmpty())
                        <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                             class="w-full h-48 object-cover" 
                             alt="Foto de entrenamiento">
                    @endif
                    <div class="p-4">
                        <h5 class="text-xl font-semibold text-gray-800">{{ $training->title }}</h5>
                        <p class="text-gray-600 text-sm">
                            <strong>Ubicación:</strong> {{ $training->park->name }} <br>
                            <strong>Actividad:</strong> {{ $training->activity->name }} <br>
                            <strong>Nivel:</strong> {{ $training->level }}
                        </p>
                        <div class="mt-3">
                            <strong class="text-gray-700">Días con Clases:</strong>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach ($training->schedules as $schedule)
                                    <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                        {{ $schedule->day }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('trainings.detail', $training->id) }}"
                               class="block text-center bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                                Ver Detalle
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center">No tienes entrenamientos creados.</p>
    @endif
</div>
@endsection