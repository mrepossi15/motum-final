@extends('layouts.main')

@section('title', 'Detalle del Alumno')

@section('content')
<div class="flex justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h2 class="text-2xl font-semibold mb-4"> Detalle de {{ $student->name }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-24 md:pb-6"> 
            <!-- 🖼️ Info del Alumno -->
            <div class="bg-white shadow-lg rounded-lg p-4 md:sticky md:top-4 md:self-start w-full md:relative border-t md:border-none">
                <div class="border-b pb-4">
                    <div class="flex items-center gap-6">
                        <!-- Imagen de Perfil -->
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-orange-300 shadow-md">
                            @if($student->profile_pic)
                                <img src="{{ asset('storage/' . $student->profile_pic) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/default-profile.png') }}" alt="Foto de perfil por defecto" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">{{ $student->name }}</h2>
                            <p class="text-gray-500">{{ ucfirst($student->role) }}</p>
                            <p class="text-gray-700">
                                {{ $student->birth ? \Carbon\Carbon::parse($student->birth)->age . ' años' : 'Fecha de nacimiento no especificada' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($student->activities as $activity)
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-md text-sm">
                            {{ $activity->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- 📋 Información y entrenamientos comprados -->
            <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-4 px-6 relative ">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-user class="w-5 h-5 text-orange-500 mr-1" />
                        Biografía
                    </h3>
                <p class="text-gray-600 border-b pb-4">{{ $student->biography ?? 'Sin información' }}</p>

                <div class="mt-4 border-b pb-4 ">
               
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <x-lucide-dollar-sign class="w-5 h-5 text-orange-500 mr-1" />Último Pago</h3>

                    <p class="text-gray-600 mt-2">
                        {{ optional($student->payments()->latest()->first())->created_at ? optional($student->payments()->latest()->first())->created_at->format('d/m/Y') : 'No registrado' }}
                    </p>
                </div>

                <!-- 📌 Entrenamientos comprados -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-dumbbell class="w-5 h-5 text-orange-500 mr-2" />
                        Entrenamientos Comprados
                    </h3>
                    
                    @if($trainings->isEmpty())
                        <p class="text-gray-500">Este alumno no ha comprado entrenamientos.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-3 gap-6 mb-4">
                            @foreach($trainings as $training)
                                @if($training) {{-- Verifica si el entrenamiento aún existe --}}
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
                                                    <strong>Ubicación:</strong> {{ $training->park->name ?? 'No disponible' }} <br>
                                                    <strong>Actividad:</strong> {{ $training->activity->name ?? 'No disponible' }} <br>
                                                    <strong>Nivel:</strong> {{ ucfirst($training->level) ?? 'No especificado' }}
                                                </p>

                                                <!-- 📅 Días con clases -->
                                                <div class="mt-3">
                                                    <strong class="text-gray-700">Días con Clases:</strong>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @if ($training->schedules->isNotEmpty())
                                                            @foreach ($training->schedules as $schedule)
                                                                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                                    {{ ucfirst($schedule->day) }}
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <p class="text-gray-500 text-xs">No hay horarios disponibles.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <!-- 🔴 Si el entrenamiento ya no existe -->
                                    <div class="bg-gray-100 p-4 rounded-lg shadow-md border border-gray-300">
                                        <h5 class="text-xl font-semibold text-gray-800">Entrenamiento Eliminado</h5>
                                        <p class="text-gray-600 text-sm">Este entrenamiento fue comprado pero ya no está disponible.</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection