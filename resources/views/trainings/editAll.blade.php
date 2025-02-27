@extends('layouts.main')

@section('title', 'Editar Entrenamiento')

@section('content')

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        {{ session('success') }}
        <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
            &times;
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
            &times;
        </button>
    </div>
@endif

<main class="container mx-auto mt-4 p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Editar Entrenamiento</h2>

        <form action="{{ route('trainings.updateAll', $training->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Título -->
            <div class="mb-4">
                <label for="title" class="block font-medium text-gray-700">Título</label>
                <input type="text" id="title" name="title" value="{{ old('title', $training->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Descripción -->
            <div class="mb-4">
                <label for="description" class="block font-medium text-gray-700">Descripción</label>
                <textarea id="description" name="description"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $training->description) }}</textarea>
            </div>

            <!-- Nivel -->
            <div class="mb-4">
                <label for="level" class="block font-medium text-gray-700">Nivel</label>
                <select id="level" name="level"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="Principiante" {{ old('level', $training->level) === 'Principiante' ? 'selected' : '' }}>Principiante</option>
                    <option value="Intermedio" {{ old('level', $training->level) === 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                    <option value="Avanzado" {{ old('level', $training->level) === 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                </select>
            </div>

            <!-- Actividad -->
            <div class="mb-4">
                <label for="activity_id" class="block font-medium text-gray-700">Actividad</label>
                <select id="activity_id" name="activity_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" {{ old('activity_id', $training->activity_id) == $activity->id ? 'selected' : '' }}>
                            {{ $activity->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Parque -->
            <div class="mb-4">
                <label for="park_id" class="block font-medium text-gray-700">Parque</label>
                <select id="park_id" name="park_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($parks as $park)
                        <option value="{{ $park->id }}" {{ old('park_id', $training->park_id) == $park->id ? 'selected' : '' }}>
                            {{ $park->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Horarios -->
            <div class="mb-4">
                <label class="block font-medium text-gray-700">Horarios</label>
                @forelse ($filteredSchedules as $index => $schedule)
    <div class="bg-gray-100 p-4 rounded-lg shadow-sm mb-4">
        <!-- 📅 Selección de Días -->
        <label class="block font-medium text-gray-700">Días</label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $day)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="schedule[days][{{ $index }}][]" value="{{ $day }}"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                           {{ in_array($day, old("schedule.days.$index", is_array($schedule->day) ? $schedule->day : [$schedule->day])) ? 'checked' : '' }}>
                    <span class="text-gray-700">{{ $day }}</span>
                </label>
            @endforeach
        </div>

        <!-- 🕒 Horario de Inicio -->
        <div class="mt-4">
            <label class="block font-medium text-gray-700">Hora de Inicio</label>
            <input type="time" name="schedule[start_time][{{ $index }}]" value="{{ old("schedule.start_time.$index", $schedule->start_time) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 🕒 Horario de Fin -->
        <div class="mt-4">
            <label class="block font-medium text-gray-700">Hora de Fin</label>
            <input type="time" name="schedule[end_time][{{ $index }}]" value="{{ old("schedule.end_time.$index", $schedule->end_time) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
@empty
    <p class="text-gray-500">No hay horarios disponibles para editar.</p>
@endforelse
            </div>

            <!-- Cupos -->
            <div class="mb-4">
                <label for="available_spots" class="block font-medium text-gray-700">Cupos Disponibles</label>
                <input type="number" id="available_spots" name="available_spots" value="{{ old('available_spots', $training->available_spots) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Precios -->
            <div class="mb-4">
                <label class="block font-medium text-gray-700">Precios</label>
                @foreach ($training->prices as $index => $price)
                    <div class="bg-gray-100 p-4 rounded-lg shadow-sm mb-2">
                        <label>Sesiones por Semana</label>
                        <input type="number" name="prices[weekly_sessions][{{ $index }}]" value="{{ $price->weekly_sessions }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        
                        <label class="mt-2">Precio</label>
                        <input type="number" name="prices[price][{{ $index }}]" value="{{ $price->price }}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                @endforeach
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Guardar Cambios</button>
                <
            </div>
        </form>
    </div>
</main>

@endsection