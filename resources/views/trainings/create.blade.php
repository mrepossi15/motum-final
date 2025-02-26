@extends('layouts.main')

@section('title', 'Crear Entrenamiento')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-center text-2xl font-semibold text-orange-500 mb-6">Crear Entrenamiento</h1>

        <!-- Formulario -->
        <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
         

            <!-- Título -->
            <x-form.input name="title" label="Título *" placeholder="Ej: Clase de Yoga" />
            
            <input type="hidden" name="park_id" value="{{ $selectedParkId }}">

<!-- Seleccionar Parque -->
<x-form.select 
    name="park_id" 
    label="Parque *" 
    :options="$parks->pluck('name', 'id')" 
    :selected="old('park_id', $selectedParkId)" 
/>
            
            <!-- Tipo de Actividad -->
            <x-form.select name="activity_id" label="Tipo de Actividad *" :options="$activities->pluck('name', 'id')" :selected="old('activity_id')" />

            <!-- Nivel -->
            <x-form.radio-group 
                name="level"
                label="Nivel *"
                :options="['Principiante' => 'Principiante', 'Intermedio' => 'Intermedio', 'Avanzado' => 'Avanzado']"
                :checked="old('level')"
            />

            <!-- Descripción -->
            <x-form.textarea name="description" label="Descripción" placeholder="Escribe una breve descripción (opcional)" />

            <!-- Días y Horarios -->
            <div class="mb-3">
                <div class="flex justify-between items-center">
                    <h5 class="font-semibold text-gray-700">Días y Horarios</h5>
                    <x-form.button type="button" id="add-schedule" color="orange">Agregar Día y Horario</x-form.button>
                </div>

                <div id="schedule-container" class="mt-3">
                    @php $schedules = old('schedule.days', [[]]); @endphp
                    @foreach ($schedules as $index => $scheduleDays)
                        <div class="border rounded p-3 mb-3 schedule-item">
                            <x-form.checkbox-group 
                                name="schedule[days][{{ $index }}][]" 
                                label="Días:" 
                                :options="['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']"
                                :selected="old('schedule.days.' . $index, [])"
                            />
                            
                            <x-form.input type="time" name="schedule[start_time][{{ $index }}]" label="Hora de Inicio *" required />
                            <x-form.input type="time" name="schedule[end_time][{{ $index }}]" label="Hora de Fin *" required />

                            <div class="text-end">
                                <x-form.button type="button" class="remove-schedule" color="red">Eliminar</x-form.button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Cupos -->
            <x-form.input name="available_spots" type="number" label="Cupos por semana *" placeholder="Ej: 15" required />

            <!-- Precios -->
            <div class="mb-3">
                <h5 class="font-semibold text-gray-700">Precios por Sesiones Semanales</h5>
                <div id="prices" class="mt-3">
                    @if(old('prices.weekly_sessions'))
                        @foreach (old('prices.weekly_sessions') as $index => $session)
                            <div class="border rounded p-3 mb-3">
                                <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces por Semana *" required />
                                <x-form.input type="number" name="prices[price][]" label="Precio *" required />
                            </div>
                        @endforeach
                    @else
                        <div class="border rounded p-3 mb-3">
                            <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces por Semana *" required />
                            <x-form.input type="number" name="prices[price][]" label="Precio *" required />
                        </div>
                    @endif
                </div>
                <x-form.button type="button" id="add-price-button" color="orange">Agregar Precio</x-form.button>
            </div>

            <!-- Fotos del Entrenamiento -->
            <div id="photo-upload-section" class="space-y-3">
                <div class="flex items-center gap-4">
                    <x-form.file name="photos[]" label="Fotos del Entrenamiento" accept="image/*" multiple />
                    <x-form.input name="photos_description[]" label="Descripción de la Foto (Opcional)" placeholder="Ej: Clase de Yoga al aire libre" />
                </div>
            </div>
            <!-- Botones -->
            <div class="flex justify-end gap-2">
            <x-form.button type="button" color="gray" onclick="window.location='{{ route('trainer.calendar') }}'">Cancelar</x-form.button>
                <x-form.button type="submit" color="orange">Guardar Entrenamiento</x-form.button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/entrenamientos/create.js') }}"></script>
@endpush

@endsection