@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')

<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10"  x-data="trainingsData()">
        <h2 class="text-2xl font-semibold mb-4">Mis Entrenamientos</h2>

        <!-- Calendario semanal -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-center">
                        @foreach(["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"] as $index => $day)
                            <th class="p-3 cursor-pointer "
                            :class="selectedDay === {{ $index + 1 }} ? 'bg-orange-500 text-white' : ''"
                            @click="selectedDay = {{ $index + 1 }}">
                            {{ $day }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Lista de entrenamientos para el día seleccionado -->   
        <div class="mt-6">
            <h3 class="text-xl font-semibold mb-4">Entrenamientos para el día seleccionado</h3>
            
            <template x-for="schedule in schedulesForDay()" :key="schedule.id">
    <div class="bg-white shadow-md rounded-lg p-4 border mb-4 border-gray-200 cursor-pointer"
        @click="openModal(schedule)">
        <h5 class="text-lg font-medium" x-text="schedule.training.title"></h5>
        <p><strong>Parque:</strong> <span x-text="schedule.training.park.name"></span></p>
        <p><strong>Actividad:</strong> <span x-text="schedule.training.activity.name"></span></p>

        <!-- 🔥 CUPOS DISPONIBLES MOSTRADOS -->
        <p><strong>Cupos Disponibles:</strong> 
            <span x-text="getAvailableSpots(schedule)"></span>
        </p>

        <h4 class="text-lg font-semibold mt-2">Horario:</h4>
        <span class="text-white px-3 py-1 rounded-lg"
              :class="schedule.is_exception ? 'bg-red-500' : 'bg-orange-500'"
              x-text="schedule.start_time + ' - ' + schedule.end_time">
        </span>

        <p x-show="schedule.is_exception" class="text-sm text-red-600 mt-1">Horario modificado para hoy</p>
    </div>
</template>
<!-- Modal para reservar clase -->
<div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" 
    x-transition.opacity>
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-semibold mb-2">Reservar Clase</h2>
        <p><strong>Entrenamiento:</strong> <span x-text="selectedTraining?.training.title"></span></p>
        <p><strong>Parque:</strong> <span x-text="selectedTraining?.training.park.name"></span></p>
        <p><strong>Actividad:</strong> <span x-text="selectedTraining?.training.activity.name"></span></p>
        <p><strong>Fecha:</strong> <span x-text="selectedTraining?.date"></span></p>
        <p><strong>Horario:</strong> <span x-text="selectedTraining?.start_time + ' - ' + selectedTraining?.end_time"></span></p>

        <!-- 🔥 CUPOS DISPONIBLES TAMBIÉN AQUÍ -->
        <p><strong>Cupos Disponibles:</strong> 
            <span x-text="getAvailableSpots(selectedTraining)"></span>
        </p>

        <form method="POST" x-bind:action="'/entrenamiento/' + selectedTraining.training.id + '/reserva'">
            @csrf
            <input type="hidden" name="training_id" x-bind:value="selectedTraining.training.id">
            <input type="hidden" name="date" x-bind:value="selectedTraining.date">
            <input type="hidden" name="time" x-bind:value="selectedTraining.start_time">

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full mt-4">
                Confirmar Reserva
            </button>
        </form>

        <button @click="showModal = false" class="mt-2 text-gray-600 hover:underline w-full text-center">
            Cancelar
        </button>
    </div>
</div>
<!-- Modal de error -->
<!-- Modal de error -->
<div x-show="showErrorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" 
    x-transition.opacity>
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-semibold mb-2 text-red-600">⚠️ No puedes hacer esta reserva</h2>
        <p x-text="errorMessage" class="text-gray-800"></p>

        <button @click="showErrorModal = false" class="mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded w-full">
            Cerrar
        </button>
    </div>
</div>

        </div>

        <!-- Lista de reservas activas -->
        <h2 class="text-2xl font-semibold mt-6 mb-4">Mis Reservas Activas</h2>
        @if($reservations->where('status', 'active')->isEmpty())
            <p class="text-gray-500">No tienes reservas activas aún.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-3">Entrenamiento</th>
                            <th class="p-3">Fecha</th>
                            <th class="p-3">Hora</th>
                            <th class="p-3">Cupos</th>
                            <th class="p-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($reservations->whereNotIn('status', ['completed']) as $reservation)
                    <tr class="border-b">
                        <td class="p-3">{{ $reservation->training->title }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y') }}</td>
                        <td class="p-3">{{ $reservation->time }}</td>
                        <td class="p-3">
                            @php
                                $totalReservations = \App\Models\TrainingReservation::where('training_id', $reservation->training->id)
                                    ->where('date', $reservation->date)
                                    ->where('time', $reservation->time)
                                    ->count();
                                $cuposRestantes = $reservation->training->available_spots - $totalReservations;
                            @endphp
                            {{ $cuposRestantes }} / {{ $reservation->training->available_spots }}
                        </td>
                        <td class="p-3">
                            @if($reservation->status === 'active')
                                <span class="bg-green-500 text-white px-2 py-1 rounded text-sm">Activa</span>
                            @elseif($reservation->status === 'completed')
                                <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Completada</span>
                            @elseif($reservation->status === 'no-show')
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">No asistió</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @php
                                $classDateTime = \Carbon\Carbon::parse("{$reservation->date} {$reservation->time}");
                                $now = \Carbon\Carbon::now();
                                $canCancel = $now->diffInHours($classDateTime, false) >= 4;
                            @endphp
    
                            @if($reservation->status === 'active')
                                @if ($canCancel)
                                    <form action="{{ route('cancel.reservation', $reservation->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
                                            Cancelar
                                        </button>
                                    </form>
                                @else
                                    <button class="bg-gray-400 text-white px-4 py-2 rounded text-sm" disabled>
                                        ❌ No puedes cancelar a menos de 4 horas
                                    </button>
                                @endif
                            @else
                                <span class="text-gray-500 text-sm">No modificable</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        @endif
    </div>
   
    
</div>


<script>
function trainingsData() 
{
    return {
        selectedDay: new Date().getDay() === 0 ? 7 : new Date().getDay(),
        showModal: false,
        showErrorModal: false,
        errorMessage: null,
        selectedTraining: null,
        trainings: {!! json_encode($trainings) !!},
        reservations: {!! json_encode($reservations) !!},

        dayMap: {
            "Lunes": 1,
            "Martes": 2,
            "Miércoles": 3,
            "Jueves": 4,
            "Viernes": 5,
            "Sábado": 6,
            "Domingo": 7
        },

        schedulesForDay() {
            return this.trainings
                .flatMap(training => training.schedules.map(schedule => ({
                    ...schedule,
                    training: training,
                    date: this.getDateForDay(this.selectedDay, schedule.start_time)
                })))
                .filter(schedule => 
                    this.dayMap[schedule.day] == this.selectedDay &&
                    (!schedule.training.status || schedule.training.status !== 'completed')
                )
                .sort((a, b) => a.start_time.localeCompare(b.start_time));
        },

        getAvailableSpots(schedule) {
    if (!schedule || !schedule.training) {
        console.error("❌ Error: schedule o schedule.training es null.", schedule);
        return "Cupos no disponibles";
    }

    let trainingId = schedule.training.id;
    let trainingDate = schedule.date ?? null;
    let trainingTime = schedule.start_time ?? null;

    if (!trainingId || !trainingDate || !trainingTime) {
        console.error("❌ Datos faltantes en getAvailableSpots():", { trainingId, trainingDate, trainingTime });
        return "Cupos no disponibles";
    }

    // **Filtrar reservas activas para este entrenamiento en esta fecha y horario**
    let filteredReservations = this.reservations.filter(reservation =>
        reservation.training_id == trainingId &&
        reservation.date == trainingDate &&
        reservation.time == trainingTime &&
        reservation.status === "active"
    );

    let reservedSpots = filteredReservations.length;
    let availableSpots = schedule.training.available_spots - reservedSpots;

    // **Si los cupos disponibles son negativos, mostrar 0**
    availableSpots = availableSpots < 0 ? 0 : availableSpots;

    console.log(`🟢 Cupos disponibles para ${trainingId} el ${trainingDate} a las ${trainingTime}:`, availableSpots);
    return `${availableSpots} / ${schedule.training.available_spots}`;
},

        openModal(training) {
            this.selectedTraining = training;
            this.errorMessage = null;

            let selectedDate = new Date(training.date);
            let today = new Date();
            let maxDate = new Date();
            maxDate.setDate(today.getDate() + 4); // 🔥 Máximo 4 días en el futuro

            if (selectedDate > maxDate) {
                this.errorMessage = "No puedes hacer reservas para clases a más de 4 días de anticipación.";
                this.showErrorModal = true;
                return; // 🔥 Evita abrir el modal de reserva
            }

            // **Verifica si el usuario puede reservar**
            fetch(`/entrenamiento/${training.training.id}/verificar-reserva`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    this.errorMessage = result.error;
                    this.showErrorModal = true;
                } else {
                    this.showModal = true;
                }
            })
            .catch(error => {
                console.error("Error verificando la reserva:", error);
                this.errorMessage = "Hubo un error al verificar la reserva.";
                this.showErrorModal = true;
            });
        },

        getDateForDay(dayNumber, startTime = null) {
            let today = new Date();
            let selectedDate = new Date();
            let currentDay = today.getDay() === 0 ? 7 : today.getDay(); // Ajuste para que Domingo sea 7

            let daysToAdd = dayNumber - currentDay;
            if (daysToAdd < 0) {
                daysToAdd += 7; // Mueve al próximo día disponible
            }

            selectedDate.setDate(today.getDate() + daysToAdd);

            // 🔥 Si la fecha es hoy y el horario ya pasó, mover la reserva a la próxima semana
            if (daysToAdd === 0 && startTime) {
                let [hours, minutes] = startTime.split(":").map(Number);
                let classTime = new Date(selectedDate);
                classTime.setHours(hours, minutes, 0);

                if (classTime < today) {
                    selectedDate.setDate(selectedDate.getDate() + 7); // Mueve a la próxima semana
                }
            }

            return selectedDate.toISOString().slice(0, 10);
        }
    };
}
</script>


@endsection