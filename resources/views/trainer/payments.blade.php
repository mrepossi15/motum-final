@extends('layouts.main')

@section('title', 'Dashboard de Pagos')

@section('content')
<div class="max-w-6xl mx-auto p-4 mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard de Pagos</h2>
    <div class="bg-white shadow-lg rounded-lg p-6">
        @if ($payments->isEmpty())
            <p class="text-gray-500 text-center">No has recibido pagos aún.</p>
        @else
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-gray-700">Alumno</th>
                        <th class="p-3 text-gray-700">Entrenamiento</th>
                        <th class="p-3 text-gray-700">Sesiones</th>
                        <th class="p-3 text-gray-700">Día</th>
                        <th class="p-3 text-gray-700">Monto</th>
                        <th class="p-3 text-gray-700">Fecha de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr class="border-t">
                            <td class="p-3">{{ $payment->user->name }}</td>
                            <td class="p-3">
                                <a href="{{ route('trainings.show', $payment->training_id) }}" class="text-orange-500 hover:underline">
                                    {{ $payment->training->title }}
                                </a>
                            </td>
                            <td class="p-3">{{ $payment->weekly_sessions }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($payment->created_at)->format('l') }}</td>
                            <td class="p-3 font-semibold text-orange-600">${{ number_format($payment->trainer_amount, 2) }}</td>
                            <td class="p-3">{{ $payment->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
