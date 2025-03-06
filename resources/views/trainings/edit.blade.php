@extends('layouts.main')

@section('title', 'Editar Entrenamiento')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6">
    <a href="{{ route('trainer.calendar') }}" class="text-orange-500 font-medium">&lt; Volver a calendario</a>

    <div class="bg-white rounded-lg mt-6 shadow-md p-4">
        <h1 class="text-2xl font-bold text-black-500 mb-4">Editar Entrenamiento para el {{ $selectedDate }}</h1>

        <form action="{{ route('trainings.update', $training->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" name="selected_date" value="{{ $selectedDate }}">

            <!-- Horarios -->
            <div class="border-b border-gray-300 p-4">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-lg font-semibold text-gray-700">Días y Horarios</h5>
                    <button type="button" id="add-schedule" class="text-orange-500 py-2 rounded-md hover:underline transition">
                        + Agregar
                    </button>
                </div>
                
                <div id="schedule-container" class="space-y-3">
                    @foreach ($filteredSchedules as $schedule)
                        <input type="hidden" name="schedule_id[]" value="{{ $schedule->id }}">
                        <div class="pb-4">
                            <!-- Horario en una sola fila -->
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-form.input type="time" name="schedule[start_time][{{ $schedule->id }}]" 
                                    label="Inicio *" 
                                    value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" 
                                    required />
                                <x-form.input type="time" name="schedule[end_time][{{ $schedule->id }}]" 
                                    label="Fin *" 
                                    value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" 
                                    required />
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="selected_date" value="{{ $selectedDate }}">
                    <input type="hidden" name="selected_time" value="{{ $selectedTime }}">
            </div>

    

            <!-- Botones de acción -->
            <div class="flex justify-between">
            <a href="{{ route('trainings.show', ['id' => $training->id, 'date' => $selectedDate, 'time' => $selectedTime]) }}" 
   class="bg-gray-500 text-white px-4 py-2 rounded">
    Cancelar
</a>
                <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
