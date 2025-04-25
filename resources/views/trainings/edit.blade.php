@extends('layouts.main')

@section('title', 'Editar Horario de entrenamiento')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6">
    <div class="bg-white rounded-xl  md:shadow-xl md:mt-6  md:p-6 p-2 ">
        <h1 class="text-2xl font-bold mt-2 text-gray-800">Editar Entrenamiento para el {{ $selectedDate }}</h1>

        <form action="{{ route('trainings.update', $training->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" name="selected_date" value="{{ $selectedDate }}">
            <!-- Horarios -->
            <div class="">                
                <div id="schedule-container" class="space-y-3">
                    @foreach ($filteredSchedules as $schedule)
                        <input type="hidden" name="schedule_id[]" value="{{ $schedule->id }}">
                        <div class="">
                            <!-- Horario en una sola fila -->
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                    <div class="relative">
                                        <label for="start_time_{{ $schedule->id }}"  class="block text-sm text-gray-700 mb-1">Inicio del entrenamiento<span class="text-red-500">*</span></label>
                                        <input id="start_time_{{ $schedule->id }}" type="time" name="schedule[start_time][{{ $schedule->id }}]"  value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"  required
                                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500" />
                                    </div>
                                    <div class="relative">
                                        <label for="end_time_{{ $schedule->id }}" class="block text-sm text-gray-700 mb-1">Fin del entrenamiento<span class="text-red-500">*</span></label>
                                        <input id="end_time_{{ $schedule->id }}"type="time" name="schedule[end_time][{{ $schedule->id }}]"  value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"   required
                                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500" />
                                    </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="selected_date" value="{{ $selectedDate }}">
                <input type="hidden" name="selected_time" value="{{ $selectedTime }}">
                @if ($errors->has('schedule'))
                    <p class="text-sm text-red-600 ">
                        {{ $errors->first('schedule') }}
                    </p>
                @endif
            </div>
            
            <!-- Botones de acción -->
            <div class="flex justify-between border-t pt-4">
                <a href="{{ route('trainings.show', ['id' => $training->id, 'date' => $selectedDate, 'time' => $selectedTime]) }}" 
                class="bg-gray-500 text-white p-3 rounded-md">
                    Cancelar
                </a>
                <button type="submit"  class="bg-orange-500 text-white p-3 rounded-md hover:bg-orange-600 transition">Guardar Cambios</button>
            </div>
        </form>
    </div>
    <div class="text-center mt-6 underline">
        <a href="{{ route('trainings.show', ['id' => $training->id, 'date' => $selectedDate, 'time' => $selectedTime]) }}" class="text-gray-500 text-sm ">
            Volver al entrenamiento
        </a>
    </div>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const startInputs = document.querySelectorAll('input[name^="schedule[start_time]"]');
        const endInputs = document.querySelectorAll('input[name^="schedule[end_time]"]');

        form.addEventListener('submit', function (e) {
            let hasError = false;

            startInputs.forEach((startInput, index) => {
                const endInput = endInputs[index];

                const startTime = startInput.value;
                const endTime = endInput.value;

                // Limpiar errores previos visuales
                startInput.classList.remove('border-red-500');
                endInput.classList.remove('border-red-500');

                if (startTime >= endTime) {
                    startInput.classList.add('border-red-500');
                    endInput.classList.add('border-red-500');

                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('El horario de inicio debe ser menor que el de finalización en todos los bloques.');
            }
        });
    });
</script>