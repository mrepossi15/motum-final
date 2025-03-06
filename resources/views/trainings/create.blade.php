@extends('layouts.main')

@section('title', 'Crear Entrenamiento')

@section('content')
<div x-data="{ step: 1 }" class="max-w-4xl mx-auto p-4 mt-6">
<a href="{{ route('trainer.calendar') }}" 
   class="text-orange-500 font-medium">
    &lt; Volver a calendario
</a>

    <div class="bg-white rounded-lg mt-6 shadow-md p-4">
        <!-- Indicador de Paso -->
        <h2 class="text-lg text-orange-500 font-semibold mt-4">
            Paso <span x-text="step"></span> de 4
        </h2>

        <!-- Título de cada paso -->
        <h1 class="text-2xl font-bold mt-2 text-black-500">
            <span x-show="step === 1">Datos básicos del entrenamiento</span>
            <span x-show="step === 2">Información adicional</span>
            <span x-show="step === 3">Horarios y precios</span>
            <span x-show="step === 4">Imágenes del entrenamiento</span>
        </h1>

        <!-- Formulario -->
        <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Paso 1: Datos básicos -->
            <div x-show="step === 1" class="space-y-6">
                
                <!-- Nombre del entrenamiento (una fila completa) -->
                <div class="w-full">
                    <x-form.input name="title" label="Título *" placeholder="Ej: Clase de Yoga" required />
                </div>

                <!-- Parque y Actividad (en dos columnas en pantallas medianas o grandes) -->
                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                    <input type="hidden" name="park_id" value="{{ $selectedParkId }}">

                    <x-form.select 
                        name="park_id" 
                        label="Parque *" 
                        :options="$parks->pluck('name', 'id')" 
                        :selected="old('park_id', $selectedParkId)" 
                    />

                    <x-form.select 
                        name="activity_id" 
                        label="Tipo de Actividad *" 
                        :options="$activities->pluck('name', 'id')" 
                        :selected="old('activity_id')" 
                    />
                </div>

            </div>

            
            <!-- Paso 2: Información adicional -->
            <div x-show="step === 2" class="space-y-6" >

                <!-- Descripción (ocupa toda la fila) -->
                <div class="w-full">
                    <x-form.textarea  name="description" label="Descripción" placeholder="Escribe una breve descripción (opcional)" />
                </div>

                <!-- Contenedor para Nivel y Cupos alineados -->
                <div class="grid grid-cols-1 md:grid-cols-4 md:mt-4 ">
                    <!-- Nivel (ocupa 3 columnas en pantallas medianas/grandes) -->
                    <div class="md:col-span-3 mb-6">
                        <x-form.radio-group 
                            name="level"
                            label="Nivel *"
                            :options="['Principiante' => 'Principiante', 'Intermedio' => 'Intermedio', 'Avanzado' => 'Avanzado']"
                            :checked="old('level')"
                        />
                    </div>

                    <!-- Cupos (ocupa 1 columna en pantallas medianas/grandes) -->
                    <div class="md:col-span-1">
                        <x-form.input name="available_spots" type="number" label="Cupos *" placeholder="Ej: 15" required />
                    </div>
                </div>

            </div>


            <!-- Paso 3: Horarios y precios -->
            <div x-show="step === 3" class="space-y-6">
    
    <!-- Sección: Días y Horarios -->
    <div class="border-b border-gray-300  p-4">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold text-gray-700">Días y Horarios</h5>
            <button type="button" id="add-schedule" 
                class=" text-orange-500 px-4 py-2 rounded-md hover:underline transition">
                + Agregar Horario
            </button>
        </div>

        <div id="schedule-container" class="space-y-3">
            @php $schedules = old('schedule.days', [[]]); @endphp
            @foreach ($schedules as $index => $scheduleDays)
                <div class=" relative">
                    <!-- Días de la semana -->
                    <x-form.checkbox-group 
                    name="schedule[days][{{ $index }}][]" 
                    label="Días"
                    :options="['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']"
                    :selected="old('schedule.days.' . $index, [])"
                    hideLabel="true"
                />

                    <!-- Horario en una sola fila -->
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <x-form.input type="time" name="schedule[start_time][{{ $index }}]" label="Inicio *" required />
                        <x-form.input type="time" name="schedule[end_time][{{ $index }}]" label="Fin *" required />
                    </div>

                    
                </div>
            @endforeach
        </div>
    </div>

    <!-- Sección: Precios -->
    <div class=" p-4">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold text-gray-700">Precios por Sesiones Semanales</h5>
            <button type="button" id="add-price-button" 
            class=" text-orange-500 px-4 py-2 rounded-md hover:underline transition">
                + Agregar Precio
            </button>
        </div>

        <div id="prices" class="space-y-3">
            @if(old('prices.weekly_sessions'))
                @foreach (old('prices.weekly_sessions') as $index => $session)
                    <div class="border border-gray-200 rounded-md p-3 shadow-sm bg-gray-50">
                        <div class="grid grid-cols-2 gap-4">
                            <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces/Semana *" required />
                            <x-form.input type="number" name="prices[price][]" label="Precio *" required />
                        </div>
                    </div>
                @endforeach
            @else
                <div class="">
                    <div class="grid grid-cols-2 gap-4">
                        <x-form.input type="number" name="prices[weekly_sessions][]" label="Veces/Semana *" required />
                        <x-form.input type="number" name="prices[price][]" label="Precio *" required textarea="$" />
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

            <!-- Paso 4: Imágenes -->
            <div x-show="step === 4" class="space-y-4">
                <x-form.file name="photos[]" label="Fotos del Entrenamiento *" accept="image/*" multiple required />
                <x-form.input name="photos_description[]" label="Descripción de la Foto (Opcional)" placeholder="Ej: Clase de Yoga al aire libre" />
            </div>

            <!-- Botones de Navegación -->
            <div class="flex justify-between">
                <button type="button" 
                        @click="if(step > 1) step--" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-md"
                        x-show="step > 1">
                    Anterior
                </button>

                <button type="button" 
                        @click="if(step < 4) step++" 
                        class="bg-orange-500 text-white px-4 py-2 rounded-md"
                        x-show="step < 4">
                    Siguiente
                </button>

                
                <button type="submit" 
                        class="bg-green-500 text-white px-4 py-2 rounded-md"
                        x-show="step === 4">
                    Guardar Entrenamiento
                </button>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection