@extends('layouts.main')

@section('title', "Entrenamientos de {$activity->name} en {$park->name}")

@section('content')
@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" aria-label="Close">✖</button>
    </div>
@endif

<!-- Encabezado -->
<div class="container mx-auto mt-4">
    
    <div class="bg-orange-500 text-white py-3 px-4 rounded shadow-sm flex justify-between items-center">
        <h1 class="mb-0 text-lg font-semibold">🏋️ {{ $activity->name }} en {{ $park->name }}</h1>

        <!-- Dropdown para filtros -->
        <div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="bg-white text-gray-700 px-4 py-2 rounded shadow-md hover:bg-gray-200">
        ⚙️ Filtros
    </button>
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 bg-white shadow-lg rounded w-72 p-4 z-10">
        <h6 class="font-semibold mb-2 text-black">Filtrar entrenamientos</h6>

        <!-- Filtrar por Día -->
        <div class="mb-3">
            <strong class="text-black">📅 Día:</strong>
            @foreach($daysOfWeek as $day)
                <div class="flex items-center mt-1">
                    <input class="mr-2 form-checkbox text-orange-500" type="checkbox" name="day[]" 
                           value="{{ $day }}" id="day-{{ $day }}" {{ in_array($day, $selectedDays ?? []) ? 'checked' : '' }}>
                    <label for="day-{{ $day }}" class="text-black">{{ $day }}</label>
                </div>
            @endforeach
        </div>

        <!-- Filtrar por Hora -->
        <div class="mb-3">
            <strong class="text-black">⏰ Hora:</strong>
            @for ($i = 6; $i <= 22; $i++)
                @php $hourFormatted = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                <div class="flex items-center mt-1">
                    <input class="mr-2 form-checkbox text-orange-500" type="checkbox" name="start_time[]" 
                           value="{{ $hourFormatted }}" id="hour-{{ $hourFormatted }}" 
                           {{ in_array($hourFormatted, $selectedHours ?? []) ? 'checked' : '' }}>
                    <label for="hour-{{ $hourFormatted }}" class="text-black">{{ $hourFormatted }}</label>
                </div>
            @endfor
        </div>

        <!-- Filtrar por Nivel -->
        <div class="mb-3">
            <strong class="text-black">🎚️ Nivel:</strong>
            @foreach($levels as $level)
                <div class="flex items-center mt-1">
                    <input class="mr-2 form-checkbox text-black" type="checkbox" name="level[]" 
                           value="{{ $level }}" id="level-{{ $level }}" 
                           {{ in_array($level, $selectedLevels ?? []) ? 'checked' : '' }}>
                    <label for="level-{{ $level }}" class="text-black">{{ ucfirst($level) }}</label>
                </div>
            @endforeach
        </div>

        <!-- Botón para aplicar filtros -->
        <button class="bg-orange-500 text-white px-4 py-2 rounded w-full mt-2 hover:bg-orange-600 transition" onclick="applyFilters()">
            Aplicar Filtros
        </button>
    </div>
</div>
    </div>
</div>

<!-- Lista de Entrenamientos -->
<div class="container mx-auto mt-4">
    @php
        $dayOrder = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    @endphp

    @if ($trainings->isEmpty())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded text-center">
            📌 No hay entrenamientos disponibles en este parque.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($trainings as $training)
                @php
                    // Ordenar los horarios del entrenamiento
                    $schedules = $training->schedules->sortBy(function ($schedule) use ($dayOrder) {
                        return array_search($schedule->day, $dayOrder);
                    });
                @endphp

                @if (!$schedules->isEmpty()) 
                    <div class="bg-white shadow-md rounded p-4">
                        <a href="{{ route('trainings.selected', $training->id) }}" class="block text-gray-800 hover:text-orange-500 transition">
                            <h4 class="text-orange-500 font-semibold">{{ $training->title }}</h4>
                            <p class="text-gray-600">{{ $training->description }}</p>
                            <p>
                                <strong>🏋️ Nivel:</strong> {{ ucfirst($training->level) }}<br>
                                <strong>👨‍🏫 Entrenador:</strong> {{ $training->trainer->name ?? 'N/A' }}
                            </p>
                            @foreach($training->prices as $price)
                                    
                                        <p class="text-gray-700"><strong>{{ $price->weekly_sessions }} x semana</strong></p>
                                        <p class="text-gray-500 text-sm">Precio: <span class="text-orange-600 font-semibold">${{ number_format($price->price, 2) }}</span></p>
                                   
                                @endforeach
                            <p>
                                <strong>📅 Horarios:</strong>
                                <ul class="list-disc list-inside text-gray-700">
                                    @foreach($schedules as $schedule)
                                        <li>✅ {{ $schedule->day }}: {{ $schedule->start_time }} - {{ $schedule->end_time }}</li>
                                    @endforeach
                                </ul>
                            </p>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

<!-- Botón para volver -->
<div class="container mx-auto text-center mt-4">
    <a href="{{ route('parks.show', $park->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
        ← Volver al parque
    </a>
</div>

<!-- Script para Filtrar -->
<script>
    function applyFilters() {
        const selectedDays = Array.from(document.querySelectorAll('input[name="day[]"]:checked'))
                                  .map(el => el.value)
                                  .join(',');
        const selectedTimes = Array.from(document.querySelectorAll('input[name="start_time[]"]:checked'))
                                  .map(el => el.value)
                                  .join(',');
        const selectedLevels = Array.from(document.querySelectorAll('input[name="level[]"]:checked'))
                                  .map(el => el.value)
                                  .join(',');

        let url = new URL(window.location.href);

        if (selectedDays) {
            url.searchParams.set('day', selectedDays);
        } else {
            url.searchParams.delete('day');
        }

        if (selectedTimes) {
            url.searchParams.set('start_time', selectedTimes);
        } else {
            url.searchParams.delete('start_time');
        }

        if (selectedLevels) {
            url.searchParams.set('level', selectedLevels);
        } else {
            url.searchParams.delete('level');
        }

        window.location.href = url.toString();
    }

    document.querySelectorAll('.form-checkbox').forEach(el => {
        el.addEventListener('change', applyFilters);
    });
</script>
@endsection