@extends('layouts.main')

@section('title', '')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6" x-data="{ selectedTab: 'active_payments' }">
    <div>
    <h2 class="text-2xl font-semibold mb-4">Historial de pagos</h2>
        <div class="flex border-b mb-6 space-x-6">
            <button @click="selectedTab = 'active_payments'" 
                class="pb-2 font-semibold"
                :class="selectedTab === 'active_payments' ? 'border-b-4 text-orange-600 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:border-orange-600 transition'">
                Pagos Activos
            </button>

            <button @click="selectedTab = 'expired_payments'" 
                class="pb-2 font-semibold"
                :class="selectedTab === 'expired_payments' ? 'border-b-4 text-orange-600 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:border-orange-600 transition'">
                Pagos Vencidos
            </button>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 mt-6">
            <ul class="divide-y divide-gray-200" x-show="selectedTab === 'active_payments'" x-cloak>
                @foreach($payments as $payment)
                    @if($payment->created_at->diffInMonths(now()) < 1)
                        <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                            <div>
                                <p class="text-gray-900 font-semibold">{{ $payment->training->title }}</p>
                                <p class="text-gray-500 text-sm">Entrenador: {{ $payment->training->trainer->name }}</p>
                                <p class="text-gray-500 text-sm">Sesiones: <strong>{{ $payment->weekly_sessions }} {{ $payment->weekly_sessions == 1 ? 'vez' : 'veces' }} por semana</strong></p>
                                <p class="text-gray-500 text-sm">Fecha de pago: {{ $payment->created_at->format('d/m/Y') }}</p>
                                <p class="text-gray-500 text-sm">Estado: Activo</p>
                                <a href="{{ route('trainings.selected', $payment->training_id) }}" class="text-orange-500 hover:underline">Ver entrenamiento</a>
                            </div>
                            <div class="text-right mt-4 sm:mt-0">
                                <p class="text-orange-600 font-bold text-lg">
                                    ${{ number_format($payment->total_amount, 2) }}
                                </p>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
            
            <ul class="divide-y divide-gray-200" x-show="selectedTab === 'expired_payments'" x-cloak>
                @foreach($payments as $payment)
                    @if($payment->created_at->diffInMonths(now()) >= 1)
                        <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                            <div>
                                <p class="text-gray-900 font-semibold">{{ $payment->training->title }}</p>
                                <p class="text-gray-500 text-sm">Entrenador: {{ $payment->training->trainer->name }}</p>
                                <p class="text-gray-500 text-sm">Sesiones: <strong>{{ $payment->weekly_sessions }} {{ $payment->weekly_sessions == 1 ? 'vez' : 'veces' }} por semana</strong></p>
                                <p class="text-gray-500 text-sm">Fecha de pago: {{ $payment->created_at->format('d/m/Y') }}</p>
                                <a href="{{ route('trainings.selected', $payment->training_id) }}" class="text-orange-500 hover:underline">Ver entrenamiento</a>
                                <p class="text-gray-500 text-sm">Estado: Vencido</p>
                            </div>
                            <div class="text-right mt-4 sm:mt-0">
                                <p class="text-orange-600 font-bold text-lg">
                                    ${{ number_format($payment->total_amount, 2) }}
                                </p>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    <div class="mt-6 text-center">
        <a href="{{ route('students.profile', ['id' => auth()->id()]) }}" class="text-orange-500 hover:underline font-semibold">
            Volver a mi perfil
        </a>
    </div>
</div>
@endsection
