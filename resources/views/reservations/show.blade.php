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
                <div class="bg-white shadow-md rounded-lg p-4 border mb-4 border-gray-200">
                    <h5 class="text-lg font-medium" x-text="schedule.training.title"></h5>
                    <p><strong>Parque:</strong> <span x-text="schedule.training.park.name"></span></p>
                    <p><strong>Actividad:</strong> <span x-text="schedule.training.activity.name"></span></p>

                    <h4 class="text-lg font-semibold mt-2">Horario:</h4>
                    <span class="text-white px-3 py-1 rounded-lg"
                        :class="schedule.is_exception ? 'bg-red-500' : 'bg-orange-500'"
                        x-text="schedule.start_time + ' - ' + schedule.end_time">
                    </span>

                    <p x-show="schedule.is_exception" class="text-sm text-red-600 mt-1">Horario modificado para hoy</p>
                </div>
            </template>
        </div>

        <!-- Lista de reservas activas -->
        <h2 class="text-2xl font-semibold mt-6 mb-4">Mis Reservas Activas</h2>
        @if($reservations->isEmpty())
            <p class="text-gray-500">No tienes reservas activas aún.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-3">Entrenamiento</th>
                            <th class="p-3">Fecha</th>
                            <th class="p-3">Hora</th>
                            <th class="p-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                            <tr class="border-b">
                                <td class="p-3">{{ $reservation->training->title }}</td>
                                <td class="p-3">{{ $reservation->date }}</td>
                                <td class="p-3">{{ $reservation->time }}</td>
                                <td class="p-3">
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-sm">Activa</span>
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
function trainingsData() {
    return {
        selectedDay: new Date().getDay() === 0 ? 7 : new Date().getDay(), // Convertir Domingo (0) a 7
        trainings: {!! json_encode($trainings) !!},

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
            console.log("Filtrando entrenamientos para el día:", this.selectedDay);

            const filteredSchedules = this.trainings
                .flatMap(training => training.schedules.map(schedule => ({
                    ...schedule,
                    training: training
                })))
                .filter(schedule => this.dayMap[schedule.day] == this.selectedDay)
                .sort((a, b) => a.start_time.localeCompare(b.start_time));

            console.log("Entrenamientos encontrados para el día:", filteredSchedules);
            return filteredSchedules;
        }
    };
}
</script>

@endsection