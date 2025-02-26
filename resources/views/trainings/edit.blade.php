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
   

      
        <!-- **Botones de Acción** -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('trainings.show', ['id' => $training->id, 'date' => $selectedDate]) }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</a>
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection