@extends('layouts.main')

@section('title', 'Reservar Clase')

@section('content')
<main class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Reservar Clase para {{ $training->title }}</h2>

    <form action="{{ route('store.reservation', $training->id) }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
        @csrf

        <!-- Seleccionar Fecha -->
        <label for="date" class="block text-sm font-medium text-gray-700">📅 Selecciona una fecha:</label>
        <input type="date" name="date" id="date" required 
               class="mt-1 block w-full p-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
        <small class="text-red-500 hidden" id="date-warning">⚠️ No puedes reservar con más de 4 días de anticipación.</small>

        <!-- Seleccionar Horario -->
        <label for="time" class="block text-sm font-medium text-gray-700 mt-4">⏰ Selecciona un horario:</label>
        <select name="time" id="time" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" disabled>
            <option value="">Selecciona una fecha primero</option>
        </select>

        <input type="hidden" id="trainingId" value="{{ $training->id }}">

        <button type="submit" id="reserve-btn" disabled 
                class="w-full mt-4 bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 disabled:bg-gray-400">
            Reservar
        </button>
    </form>

    <a href="{{ url()->previous() }}" class="block text-center mt-4 text-orange-500 hover:underline">⬅️ Atrás</a>
</main>

<script defer>
document.addEventListener("DOMContentLoaded", function () {
    let dateInput = document.getElementById('date');
    let dateWarning = document.getElementById('date-warning');
    let timeSelect = document.getElementById('time');
    let reserveBtn = document.getElementById('reserve-btn');
    let trainingId = document.getElementById('trainingId')?.value; // Usa `?.value` para evitar errores si no existe

    if (!dateInput) {
        console.error("❌ El campo de fecha no se encontró en el DOM.");
        return;
    }

    dateInput.addEventListener('change', function() {
        let selectedDate = new Date(dateInput.value);
        let today = new Date();
        let maxDate = new Date();
        maxDate.setDate(today.getDate() + 4); // Solo permite hasta 4 días en el futuro

        if (selectedDate > maxDate) {
            dateWarning.classList.remove('hidden');
            timeSelect.innerHTML = '<option value="">Selecciona una fecha válida</option>';
            timeSelect.disabled = true;
            reserveBtn.disabled = true;
            return;
        } else {
            dateWarning.classList.add('hidden');
        }

        // 🔥 **Verifica que el ID de entrenamiento existe antes de hacer la petición**
        if (!trainingId) {
            console.error("❌ No se encontró el ID del entrenamiento.");
            return;
        }

        fetch(`/trainings/${trainingId}/available-times?date=${dateInput.value}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`❌ Error HTTP ${response.status}: No se encontraron horarios.`);
                }
                return response.json();
            })
            .then(data => {
                timeSelect.innerHTML = ''; // Limpiar opciones previas
                if (data.length > 0) {
                    data.forEach(schedule => {
                        let option = document.createElement('option');
                        option.value = schedule.start_time;
                        option.textContent = `${schedule.start_time} - ${schedule.end_time}`;
                        timeSelect.appendChild(option);
                    });
                    timeSelect.disabled = false;
                    reserveBtn.disabled = false;
                } else {
                    timeSelect.innerHTML = '<option value="">No hay horarios disponibles para esta fecha</option>';
                    timeSelect.disabled = true;
                    reserveBtn.disabled = true;
                }
            })
            .catch(error => {
                console.error('❌ Error obteniendo los horarios:', error);
                timeSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
                timeSelect.disabled = true;
                reserveBtn.disabled = true;
            });
    });
});
</script>

@endsection