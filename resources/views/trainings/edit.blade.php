@extends('layouts.main')

@section('title', 'Editar Entrenamiento')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold text-orange-600 mb-4">Editar Entrenamiento para el {{ $selectedDate }}</h1>
    <div class="bg-gray-100 p-3 rounded">
    <h3 class="text-lg font-semibold">📝 Horarios Cargados para el {{ $selectedDate }}:</h3>
    <pre>{{ print_r($filteredSchedules, true) }}</pre>
</div>
    <form action="{{ route('trainings.update', $training->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="selected_date" value="{{ $selectedDate }}">

        <!-- **Datos Generales del Entrenamiento** -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Detalles Generales</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium">Título:</label>
                    <input type="text" name="title" value="{{ old('title', $training->title) }}" required class="w-full p-2 border rounded-md">
                </div>

                <div>
                    <label for="level" class="block text-sm font-medium">Nivel:</label>
                    <select name="level" class="w-full p-2 border rounded-md">
                        <option value="Principiante" {{ $training->level == 'Principiante' ? 'selected' : '' }}>Principiante</option>
                        <option value="Intermedio" {{ $training->level == 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="Avanzado" {{ $training->level == 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                </div>

                <div>
                    <label for="activity_id" class="block text-sm font-medium">Actividad:</label>
                    <select name="activity_id" class="w-full p-2 border rounded-md">
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}" {{ $training->activity_id == $activity->id ? 'selected' : '' }}>
                                {{ $activity->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="park_id" class="block text-sm font-medium">Parque:</label>
                    <select name="park_id" class="w-full p-2 border rounded-md">
                        @foreach ($parks as $park)
                            <option value="{{ $park->id }}" {{ $training->park_id == $park->id ? 'selected' : '' }}>
                                {{ $park->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="available_spots" class="block text-sm font-medium">Cupos Disponibles:</label>
                    <input type="number" name="available_spots" value="{{ old('available_spots', $training->available_spots) }}" required class="w-full p-2 border rounded-md">
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium">Descripción:</label>
                    <textarea name="description" rows="3" class="w-full p-2 border rounded-md">{{ old('description', $training->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- **Horario para la Fecha Seleccionada** -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
    <h2 class="text-lg font-semibold mb-4">Horario para el {{ $selectedDate }}</h2>

    @forelse ($filteredSchedules as $index => $schedule)
        <input type="hidden" name="schedule_id[]" value="{{ $schedule->id }}">

        <div class="border rounded-lg p-4 mb-4">
            <h3 class="text-md font-semibold mb-2">Horario #{{ $index + 1 }} 
                @if($schedule->is_exception)
                    <span class="text-red-500">(Modificado para esta fecha)</span>
                @endif
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time-{{ $index }}" class="block text-sm font-medium">Hora de Inicio:</label>
                    <input type="time" name="schedule[start_time][{{ $index }}]"
                           value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                           class="w-full p-2 border rounded-md" required>
                </div>

                <div>
                    <label for="end_time-{{ $index }}" class="block text-sm font-medium">Hora de Fin:</label>
                    <input type="time" name="schedule[end_time][{{ $index }}]"
                           value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                           class="w-full p-2 border rounded-md" required>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500">⚠️ No hay horarios disponibles para esta fecha.</p>
    @endforelse
</div>
   

        <!-- **Precios** -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Precios por Sesiones Semanales</h2>

            <div id="prices-container">
                @foreach ($training->prices as $index => $price)
                    <div class="flex items-center gap-4 mb-3">
                        <div>
                            <label for="prices[weekly_sessions][{{ $index }}]" class="block text-sm font-medium">Veces por Semana:</label>
                            <input type="number" name="prices[weekly_sessions][{{ $index }}]" value="{{ $price->weekly_sessions }}" min="1" class="p-2 border rounded-md">
                        </div>

                        <div>
                            <label for="prices[price][{{ $index }}]" class="block text-sm font-medium">Precio:</label>
                            <input type="number" name="prices[price][{{ $index }}]" value="{{ $price->price }}" step="0.01" class="p-2 border rounded-md">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- **Subir Nuevas Imágenes** -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Fotos del Entrenamiento</h2>

            <div class="mb-3">
                <label for="photos" class="block text-sm font-medium">Nuevas Fotos:</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="w-full p-2 border rounded-md">
            </div>

            <div>
                <label for="photos_description" class="block text-sm font-medium">Descripción de las Fotos:</label>
                <input type="text" name="photos_description[]" placeholder="Descripción opcional" class="w-full p-2 border rounded-md">
            </div>
        </div>

        <!-- **Botones de Acción** -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('trainings.show', ['id' => $training->id, 'date' => $selectedDate]) }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</a>
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection