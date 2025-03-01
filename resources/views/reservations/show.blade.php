@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')
<main class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-semibold mb-4">Mis Entrenamientos</h2>
    
    <div class="space-y-4">
        @forelse($trainings as $training)
            @php
                $hasActiveReservation = $reservations->where('training_id', $training->id)
                    ->where('status', 'active')
                    ->isNotEmpty();
            @endphp
            
            <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
                <h5 class="text-lg font-medium">{{ $training->title }}</h5>
                <p><strong>Parque:</strong> {{ $training->park->name }}</p>
                <p><strong>Actividad:</strong> {{ $training->activity->name }}</p>
                <p>
                    <strong>Cupos Disponibles:</strong> 
                    {{ $training->available_spots - $training->reservations->count() }} / {{ $training->available_spots }}
                </p>
                
                @if ($hasActiveReservation)
                    <button class="bg-gray-400 text-white px-4 py-2 rounded mt-2" disabled>
                        🚫 Ya tienes una reserva activa
                    </button>
                @else
                    <a href="{{ route('reserve.training.view', $training->id) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mt-2 inline-block">
                        📅 Reservar Entrenamiento
                    </a>
                @endif
            </div>
        @empty
            <p class="text-gray-500">No has comprado entrenamientos aún.</p>
        @endforelse
    </div>

    <h2 class="text-2xl font-semibold mt-6 mb-4">Mis Reservas</h2>

    @if($reservations->isEmpty())
        <p class="text-gray-500">No tienes reservas aún.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-3">Entrenamiento</th>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Hora</th>
                        <th class="p-3">Cupos Disponibles</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $reservation)
                    <tr class="border-b">
                        <td class="p-3">{{ $reservation->training->title }}</td>
                        <td class="p-3">{{ $reservation->date }}</td>
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

    <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mt-4 inline-block">
        ⬅️ Atrás
    </a>
</main>
@endsection