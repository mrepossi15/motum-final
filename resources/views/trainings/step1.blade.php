@extends('layouts.main')

@section('title', 'Tu Información')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 ">
    <div x-show="step === 3" class="space-y-6">
              <!-- Sección: Días y Horarios -->
        <div class="border-b border-gray-300  p-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-semibold text-gray-700">Días y Horarios</h5>
                <button type="button" id="add-schedule" 
                        class=" text-orange-500  py-2 rounded-md hover:underline transition">
                        + Agregar 
                </button>
            </div>

            <div id="schedule-container" class="space-y-3">
                    @php $schedules = old('schedule.days', [[]]); @endphp
                    @foreach ($schedules as $index => $scheduleDays)
                        <div class=" pb-4">
                            <!-- Días de la semana -->
                            <x-form.checkbox-group 
                            name="schedule[days][{{ $index }}][]" 
                            label="Días"
                            :options="['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']"
                            :selected="old('schedule.days.' . $index, [])"
                            hideLabel="true"
                            />

                            <!-- Horario en una sola fila -->
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-form.input type="time" name="schedule[start_time][{{ $index }}]" label="Inicio *" required />
                                <x-form.input type="time" name="schedule[end_time][{{ $index }}]" label="Fin *" required />
                            </div>

                            
                        </div>
                    @endforeach
            </div>
        </div>

            <!-- Sección: Precios -->
        <div class=" px-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-semibold text-gray-700">Precios por Sesiones Semanales</h5>
                
                <button type="button" id="add-price-button" 
                    class="text-orange-500  py-2 rounded-md hover:underline transition whitespace-nowrap">
                    + Agregar
                </button>
            </div>

            <div id="prices" class="space-y-3">
                @if(old('prices.weekly_sessions'))
                    @foreach (old('prices.weekly_sessions') as $index => $session)
                        <div class="border border-gray-200 rounded-md p-3 shadow-sm bg-gray-50">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces/Semana *" required />
                                <x-form.input type="number" name="prices[price][]" label="Precio *" required />
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces/Semana *" required />
                            <x-form.input type="number" name="prices[price][]" label="Precio *" required textarea="$" />
                        </div>
                    </div>
                @endif
            </div>
        </div>

</div>



</div>
@push('scripts')
<script src="{{ asset('js/entrenamientos/create.js') }}"></script>
@endpush
@endsection

