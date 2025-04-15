@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')

<div class=" flex justify-center min-h-screen text-black bg-gray-100" x-data="initTabs()" x-init="init()">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10" x-data="{ selectedTab: 'trainings' }">
        <h2 class="text-2xl font-semibold mb-4">Mis Entrenamientos</h2>
        @if(session('success'))
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 3000)" 
                x-show="show" 
                class="fixed bottom-10 left-1/2 transform -translate-x-1/2 bg-orange-500 text-white text-center px-6 py-3 rounded-lg shadow-xl font-rubik text-lg transition-all duration-500 opacity-500"
                x-transition:leave="opacity-100"
                x-transition:leave-end="opacity-0">
                {{ session('success') }}
            </div>
        @endif
        <!-- 🔥 Botones de navegación -->
        <div class="flex border-b mb-6 space-x-6">
            <button @click="selectedTab = 'trainings'" 
                class="pb-2 font-semibold"
                :class="selectedTab === 'trainings' ? 'border-b-4 text-orange-600 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:border-orange-600 transition'">
                Entrenamientos
            </button>

            <button @click="selectedTab = 'reservations'" 
                class="pb-2 font-semibold"
                :class="selectedTab === 'reservations' ? 'border-b-4 text-orange-600 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:border-orange-600 transition'">
                Reservas Activas
            </button>
        </div>

        <!-- 🔥 Sección de Entrenamientos -->
        <div x-show="selectedTab === 'trainings'">
                <div x-data="trainingsData()">
                    
                    <!-- Calendario semanal -->
                <div class="overflow-x-auto">
                    <table class="min-w-full  overflow-hidden border-separate border-spacing-1">
                        <thead>
                            <tr class="bg-gray-100 text-center border-b border-gray-800">
                                @foreach([
                                    ["Lunes", "Lun", "L"], 
                                    ["Martes", "Mar", "M"], 
                                    ["Miércoles", "Mié", "M"], 
                                    ["Jueves", "Jue", "J"], 
                                    ["Viernes", "Vie", "V"], 
                                    ["Sábado", "Sáb", "S"], 
                                    ["Domingo", "Dom", "D"]
                                ] as $index => $day)
                                    <th class="p-3 cursor-pointer border border-black rounded-lg"
                                        :class="selectedDay === {{ $index + 1 }} ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : 'hover:bg-gray-200 transition'"
                                        @click="selectedDay = {{ $index + 1 }}">
                                        
                                        <!-- 🖥 Versión de escritorio -->
                                        <span class="hidden lg:inline">{{ $day[0] }}</span>
                                        
                                        <!-- 📱 Versión de tablet -->
                                        <span class="hidden md:inline lg:hidden">{{ $day[1] }}</span>
                                        
                                        <!-- 📱 Versión de móvil -->
                                        <span class="inline md:hidden">{{ $day[2] }}</span>

                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                    </table>
                </div>

                    <!-- Lista de entrenamientos para el día seleccionado -->   
                    <div class="mt-6">
                        <template x-if="schedulesForDay().length === 0">
                            <p class="text-gray-600 italic">No hay entrenamientos para este día.</p>
                        </template>
                        <template x-for="schedule in schedulesForDay()" :key="schedule.id">
                            <div class="bg-white shadow-md mb-4 rounded-lg p-6 border border-gray-200 cursor-pointer hover:shadow-lg transition"
                                @click="openModal(schedule)">
                                <h5 class="text-xl font-semibold mb-2" x-text="schedule.training.title"></h5>
                                <p class="text-gray-700"><strong>Parque:</strong> <span x-text="schedule.training.park.name"></span></p>
                                <p class="text-gray-700"><strong>Actividad:</strong> <span x-text="schedule.training.activity.name"></span></p>
                                <p class="text-gray-700"><strong>Entrenador:</strong> <span x-text="schedule.training.trainer.name"></span></p>

                                <div class="mt-1">
                                    <p class="text-gray-700"><strong>Cupos Disponibles:</strong> <span x-text="getAvailableSpots(schedule)"></span></p>
                                </div>

                                <div class="mt-2">
                                    <h4 class="text-lg font-semibold">Horario:</h4>
                                    <span class="text-white px-3 py-1 rounded-lg" 
                                        :class="schedule.is_exception ? 'bg-red-500' : 'bg-orange-500'"
                                        x-text="schedule.start_time.slice(0,5) + ' - ' + schedule.end_time.slice(0,5)">
                                    </span>
                                    <p x-show="schedule.is_exception" class="text-sm text-red-600 mt-1">Horario modificado para hoy</p>
                                </div>
                            </div>
                        </template>
                    </div>
                
                    <!-- Modal para reservar clase -->
                    <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
                        x-transition.opacity>
                        <div class="bg-[#1E1E1E] p-6 rounded-lg w-full max-w-md md:max-w-lg h-[90vh] md:h-auto shadow-lg relative overflow-y-auto">
        
                        <!-- ❌ Botón de cerrar -->
                            <button @click="showModal = false" class="absolute top-4 right-4 text-white hover:text-red-500">
                                <x-lucide-x class="w-6 h-6" />
                            </button>

                            <!-- 🏷️ Título -->
                            <h2 class="text-lg text-white font-semibold mb-6 text-center">Reservar Clase</h2>

                            <!-- 📌 Información de la reserva -->
                            <div class="text-white space-y-3">
                                <p><strong>Entrenamiento:</strong> <span x-text="selectedTraining?.training.title"></span></p>
                                <p><strong>Parque:</strong> <span x-text="selectedTraining?.training.park.name"></span></p>
                                <p><strong>Actividad:</strong> <span x-text="selectedTraining?.training.activity.name"></span></p>
                                <p><strong>Fecha:</strong> <span x-text="selectedTraining?.date"></span></p>
                                <p><strong>Horario:</strong> <span x-text="selectedTraining?.start_time + ' - ' + selectedTraining?.end_time"></span></p>
                                <p><strong>Cupos Disponibles:</strong> <span x-text="getAvailableSpots(selectedTraining)"></span></p>
                            </div>

                            <!-- 🟠 Formulario de reserva -->
                            <form method="POST" x-bind:action="'/entrenamiento/' + selectedTraining.training.id + '/reserva'" class="mt-6">
                                @csrf
                                <input type="hidden" name="training_id" x-bind:value="selectedTraining.training.id">
                                <input type="hidden" name="date" x-bind:value="selectedTraining.date">
                                <input type="hidden" name="time" x-bind:value="selectedTraining.start_time">
                                <input type="hidden" name="end_time" x-bind:value="selectedTraining.end_time">

                                <button type="submit" class="bg-orange-500 text-white text-md font-semibold px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                                    Confirmar Reserva
                                </button>
                            </form>

                            <!-- ❌ Botón de cancelar -->
                            <button @click="showModal = false" 
                                    class="mt-4 text-gray-400 hover:text-white hover:underline w-full text-center transition">
                                Cancelar
                            </button>
                        </div>
                </div>
                        <!-- Modal de error -->
                        <div x-show="showErrorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" 
                                x-transition.opacity>
                                
                                <div class="bg-[#1E1E1E] rounded-lg shadow-lg w-96 p-6">
                                
                                    <h2 class="text-lg font-semibold  text-orange-500"> No puedes hacer esta reserva</h2>
                                    
                                    <p x-text="errorMessage" class="text-white"></p>

                                    <button @click="showErrorModal = false"  class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 jsutify-end transition">
                                        Cerrar
                                    </button>
                                </div>
                        </div>
                        
                    </div>
            </div>

        <!-- 🔥 Sección de Reservas Activas -->
 
        <div x-show="selectedTab === 'reservations'" x-data="trainingsData()">
            <!-- 🔥 Calendario semanal -->
            <div class="overflow-x-auto">
                    <table class="min-w-full  overflow-hidden border-separate border-spacing-1">
                        <thead>
                            <tr class="bg-gray-100 text-center border-b border-gray-800">
                                @foreach([
                                    ["Lunes", "Lun", "L"], 
                                    ["Martes", "Mar", "M"], 
                                    ["Miércoles", "Mié", "M"], 
                                    ["Jueves", "Jue", "J"], 
                                    ["Viernes", "Vie", "V"], 
                                    ["Sábado", "Sáb", "S"], 
                                    ["Domingo", "Dom", "D"]
                                ] as $index => $day)
                                    <th class="p-3 cursor-pointer border border-black rounded-lg"
                                        :class="selectedDay === {{ $index + 1 }} ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : 'hover:bg-gray-200 transition'"
                                        @click="selectedDay = {{ $index + 1 }}">
                                        
                                        <!-- 🖥 Versión de escritorio -->
                                        <span class="hidden lg:inline">{{ $day[0] }}</span>
                                        
                                        <!-- 📱 Versión de tablet -->
                                        <span class="hidden md:inline lg:hidden">{{ $day[1] }}</span>
                                        
                                        <!-- 📱 Versión de móvil -->
                                        <span class="inline md:hidden">{{ $day[2] }}</span>

                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                    </table>
            </div>

            <!-- 🔥 Lista de reservas activas filtradas por día -->
            <div class="mt-6">
                <template x-if="activeReservationsForDay().length === 0">
                    <p class="text-gray-600 italic">No hay reservas para este día.</p>
                </template>
                <template x-for="reservation in activeReservationsForDay()" :key="reservation.id">
    <div class="relative bg-white shadow-md mb-4 rounded-lg p-6 border border-gray-200 cursor-pointer hover:shadow-lg transition">
        
        <!-- ❌ Botón de eliminar en la esquina superior derecha -->
        <div class="absolute top-4 right-4" x-data="{ canCancel: checkIfCanCancel(reservation.date, reservation.time), showConfirmModal: false, selectedReservation: null }">
            
            <template x-if="canCancel">
            <button @click="selectedReservation = reservation; showConfirmModal = true"
                class="bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded text-sm items-center">
                <x-lucide-trash class="w-5 h-5 " />
            </button>
            </template>
            
            <template x-if="!canCancel">
                <button class="bg-gray-400 text-white px-3 py-1 rounded text-sm cursor-not-allowed" disabled>
                     No puedes cancelar a menos de 4 horas
                </button>
            </template>
            

            <!-- 🛑 Modal de Confirmación -->
            <x-modal 
                open="showConfirmModal" 
                title="Confirmar eliminación" 
                description="¿Estás seguro de que deseas cancelar esta reserva?"
            >
                <form :action="'/entrenamiento/' + selectedReservation.id + '/delete'" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-red-400 transition">
                        Sí, cancelar reserva
                    </button>
                </form>
                <button @click="showConfirmModal = false" class="mt-4 text-gray-400 hover:text-white hover:underline w-full text-center transition">
                    No, volver atrás
                </button>
            </x-modal>
        </div>

        <!-- 📌 Datos de la reserva -->
        <h5 class="text-xl font-semibold mb-2" x-text="reservation.training.title"></h5>
        <p class="text-gray-700"><strong>Parque:</strong> <span x-text="reservation.training.park?.name || 'No disponible'"></span></p>
        <p class="text-gray-700"><strong>Actividad:</strong> <span x-text="reservation.training.activity?.name || 'No disponible'"></span></p>
        <p class="text-gray-700"><strong>Entrenador:</strong> <span x-text="reservation.training.trainer?.name || 'No disponible'"></span></p>
        <p class="text-gray-700"><strong>Cupos Disponibles:</strong> 
            <span x-text="reservation.available_spots !== undefined ? reservation.available_spots : 'No disponible'"></span>
        </p>
        <div class="mt-2">
            <h4 class="text-lg font-semibold">Horario:</h4>
            <span class="text-white px-3 py-1 rounded-lg bg-orange-500"  
                x-text="reservation.time + ' - ' + reservation.end_time">
            </span>
        </div>
        <p class="text-gray-700"><strong>Fecha:</strong> <span x-text="formatDate(reservation.date)"></span></p>
    </div>
</template>
            </div>
        </div>
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

                // ✅ 🔥 FILTRA SOLAMENTE RESERVAS ACTIVAS SEGÚN EL DÍA SELECCIONADO
                activeReservationsForDay() {
    let selectedDate = this.getDateForDay(this.selectedDay);
    console.log(`📆 Día seleccionado: ${this.selectedDay} | Fecha filtrada: ${selectedDate}`);

    let filteredReservations = this.reservations
        .filter(reservation => {
            let reservationDate = reservation.date;
            let isSameDay = reservationDate === selectedDate;
            let isActive = reservation.status === "active";
            return isSameDay && isActive;
        })
        .map(reservation => {
            return {
                ...reservation,
                training: {
                    ...reservation.training,
                    park: reservation.training.park ?? { name: "No disponible" },
                    activity: reservation.training.activity ?? { name: "No disponible" },
                    trainer: reservation.training.trainer ?? { name: "No disponible" }
                },
                end_time: reservation.end_time ?? "No definido",
                available_spots: reservation.available_spots ?? "No disponible" // 🔥 Agregado
            };
        });

    console.log("🎯 Reservas activas filtradas:", filteredReservations);
    return filteredReservations.sort((a, b) => a.time.localeCompare(b.time));
},
        // ✅ 🔥 FILTRA LOS ENTRENAMIENTOS SEGÚN EL DÍA SELECCIONADO
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

        // ✅ OBTENER CUPOS DISPONIBLES
        getAvailableSpots(item) {
            if (!item || !item.training) return "Cupos no disponibles";

            // 🔥 Detectar si es un `schedule` de training o una reserva activa
            let trainingId = item.training.id;
            let trainingDate = item.date ?? null;
            let trainingTime = item.start_time ?? item.time ?? null; // Usa start_time en trainings y time en reservations

            if (!trainingId || !trainingDate || !trainingTime) return "Cupos no disponibles";

            // 🔥 Filtrar reservas activas para este entrenamiento en esta fecha y horario
            let filteredReservations = this.reservations.filter(res => 
                res.training_id == trainingId && res.date == trainingDate &&
                res.time == trainingTime && res.status === "active"
            );

            let reservedSpots = filteredReservations.length;
            let availableSpots = item.training.available_spots - reservedSpots;

            return `${availableSpots < 0 ? 0 : availableSpots} / ${item.training.available_spots}`;
        },

        // ✅ FORMATEAR FECHA (DD/MM/YYYY)
        formatDate(dateString) {
            let date = new Date(dateString);
            return date.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },

        // ✅ FORMATEAR HORA (HH:MM)
        formatTime(timeString) {
            return timeString.slice(0, 5); // "22:30:00" → "22:30"
        },

        getDateForDay(dayNumber, startTime = null) {
            let today = new Date();
            let selectedDate = new Date();
            let currentDay = today.getDay() === 0 ? 7 : today.getDay();

            let daysToAdd = dayNumber - currentDay;
            if (daysToAdd < 0) {
                daysToAdd += 7;
            }

            selectedDate.setDate(today.getDate() + daysToAdd);

            if (daysToAdd === 0 && startTime) {
                let [hours, minutes] = startTime.split(":").map(Number);
                let classTime = new Date(selectedDate);
                classTime.setHours(hours, minutes, 0);

                if (classTime < today) {
                    selectedDate.setDate(selectedDate.getDate() + 7);
                }
            }

            return selectedDate.toISOString().slice(0, 10);
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
function checkIfCanCancel(date, time) {
    let reservationDateTime = new Date(`${date}T${time}`);
    let now = new Date();
    
    let diffInHours = (reservationDateTime - now) / (1000 * 60 * 60); // Convierte milisegundos a horas
    return diffInHours >= 4; // 🔥 Solo permite cancelar si faltan 4 horas o más
}
function initTabs() {
    return {
        selectedTab: 'trainings',

        init() {
            // Detecta si la URL tiene #reservas y abre la pestaña de reservas
            if (window.location.hash === '#reservas') {
                this.selectedTab = 'reservations';
            }
        }
    };
}
</script>


@endsection

