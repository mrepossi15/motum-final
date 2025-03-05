@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')

<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10 relative">
        
        <!-- 🏋️‍♂️ Título -->
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Mis Entrenamientos</h1>
        <div class="absolute top-4 right-4">
            <!-- 📱 Versión para Móvil (botón negro con icono naranja) -->
            <a href="{{ route('trainings.create') }}"
                class="sm:hidden flex items-center space-x-2  text-white px-2 py-2 rounded-lg transition hover:bg-gray-800">
                <x-lucide-pencil class="w-5 h-5 text-orange-500" />
                <span class="sr-only">Agregar entrenamiento</span>
            </a>

            <!-- 🖥️ Versión para Tablet y Computadora (link con subrayado en hover) -->
            <a href="{{ route('trainings.create') }}"
                class="hidden sm:flex text-orange-500 px-4 py-2 items-center space-x-2 hover:underline transition">
                <x-lucide-pencil class="w-4 h-4" />
                <span>Agregar entrenamiento</span>
            </a>
        </div>

        @if ($trainings->isEmpty())
            <p class="text-gray-500 text-center text-lg">Aún no tienes entrenamientos asociados. 🏞️</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($trainings as $training)

                    <!-- 🔗 Hacer que toda la card sea clickeable -->
                    <a href="{{ route('trainings.detail', $training->id) }}" class="block">
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105 cursor-pointer">
                            
                            <!-- 📸 Imagen del entrenamiento -->
                            @if ($training->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                    class="w-full h-48 object-cover" 
                                    alt="Foto de entrenamiento">
                            @endif

                            <!-- 📝 Detalles del entrenamiento -->
                            <div class="p-4">
                                <h5 class="text-xl font-semibold text-gray-800">{{ $training->title }}</h5>

                                <p class="text-gray-600 text-sm">
                                    <strong>Ubicación:</strong> {{ $training->park->name }} <br>
                                    <strong>Actividad:</strong> {{ $training->activity->name }} <br>
                                    <strong>Nivel:</strong> {{ ucfirst($training->level) }}
                                </p>

                                <!-- 📅 Días con clases -->
                                <div class="mt-3">
                                    <strong class="text-gray-700">Días con Clases:</strong>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach ($training->schedules as $schedule)
                                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                {{ ucfirst($schedule->day) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection




