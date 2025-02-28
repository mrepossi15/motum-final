@extends('layouts.main')

@section('title', 'Lista de Asistencia')

@section('content')
<main class="container mx-auto mt-6 px-4">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">
        Lista de Asistencia - {{ $training->title }} ({{ $date }} - {{ $selectedTime }})
    </h2>

    @if ($reservations->isEmpty())
        <p class="text-center text-gray-500">No hay participantes en este horario.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="px-4 py-2 text-left">Alumno</th>
                        <th class="px-4 py-2 text-center">Estado</th>
                        <th class="px-4 py-2 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservations as $reservation)
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 text-gray-800">
                                {{ $reservation->user->name }} 
                                <span class="text-sm text-gray-500">({{ $reservation->user->email }})</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($reservation->status == 'active')
                                    <span class="px-2 py-1 text-sm font-semibold bg-yellow-200 text-yellow-800 rounded">
                                        Pendiente
                                    </span>
                                @elseif($reservation->status == 'completed')
                                    <span class="px-2 py-1 text-sm font-semibold bg-green-200 text-green-800 rounded">
                                        Asistió
                                    </span>
                                @elseif($reservation->status == 'no-show')
                                    <span class="px-2 py-1 text-sm font-semibold bg-red-200 text-red-800 rounded">
                                        No Asistió
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <!-- Botón Asistió -->
                                    <form action="{{ route('reservations.updateStatus', $reservation->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="px-3 py-1 text-white bg-green-500 rounded-md text-sm hover:bg-green-600 transition"
                                            {{ $reservation->status == 'completed' ? 'disabled' : '' }}>
                                            ✔️ Asistió
                                        </button>
                                    </form>

                                    <!-- Botón No Asistió -->
                                    <form action="{{ route('reservations.updateStatus', $reservation->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="no-show">
                                        <button type="submit" class="px-3 py-1 text-white bg-red-500 rounded-md text-sm hover:bg-red-600 transition"
                                            {{ $reservation->status == 'no-show' ? 'disabled' : '' }}>
                                            ❌ No Asistió
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</main>
@endsection