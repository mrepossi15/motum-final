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

            <!-- Paso 4: Imágenes -->
            <div x-show="step === 4" class="space-y-4" x-data="photoPreview()">
                <div class="relative">
                    <!-- Label flotante -->
                    <label for="photos" 
                        class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">
                        Fotos del Entrenamiento *
                    </label>

                    <!-- Input con diseño limpio y bordes dinámicos -->
                    <input
                        type="file"
                        id="photos"
                        name="photos[]"
                        accept="image/*"
                        multiple
                        class="w-full bg-white text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500
                        @error('photos') border-red-500 @enderror"
                        @change="previewImages(event)"
                    >

                    <!-- Mensaje de error con ícono de advertencia -->
                    @error('photos')
                        <div class="flex items-center mt-1 text-red-500 text-xs">
                            <!-- Ícono de advertencia -->
                            <x-lucide-cross class="h-4 w-4 mr-1 text-red-500" />
                            <p>⚠️ {{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <!-- Vista previa de imágenes -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    <template x-for="(photo, index) in photos" :key="index">
                        <div class="relative w-full h-24 overflow-hidden rounded-sm shadow-sm ">
                            <img :src="photo.url" class="w-full h-full object-cover">
                            <!-- Botón para eliminar imagen -->
                            <button @click="removeImage(index)" class="absolute top-1 right-1 w-6 h-6 flex items-center justify-center ">
                                <x-lucide-square-x class="h-4 w-4 text-red-500" />
                            </button>
                        </div>
                    </template>
                </div>
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
         class="bg-orange-500 text-white text-md px-6 py-3 rounded-md  hover:bg-orange-600 transition"
            x-show="step === 4"
            x-bind:disabled="submitting"
            @click="submitting = true">
        Guardar Entrenamiento
    </button>
            </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/entrenamientos/create.js') }}"></script>
@endpush
@endsection



<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingPhoto;
use App\Models\TrainingSchedule;
use App\Models\TrainingPrice;
use App\Models\Park;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrainingCreatedMail;

class TrainingController extends Controller
{
    // Paso 1: Datos Fundamentales - Mostrar vista
    public function createFundamentals()
    {
        $parks = Auth::user()->parks;
        $activities = \App\Models\Activity::all();
        return view('trainings.fundamentals', compact('parks', 'activities'));
    }

    // Paso 1: Datos Fundamentales - Guardar en sesión y redirigir
    public function storeFundamentals(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'park_id' => 'required|exists:parks,id',
            'activity_id' => 'required|exists:activities,id',
        ]);

        // Validar pertenencia del parque
        $park = Park::find($request->park_id);
        if (!$park || !$park->users->contains(Auth::user())) {
            return back()->with('error', 'El parque no es válido o no está asociado a tu cuenta.');
        }

        session()->put('training.fundamentals', $request->only('title', 'description', 'park_id', 'activity_id'));
        return redirect()->route('trainings.create.technical');
    }

    // Paso 2: Datos Técnicos - Mostrar vista
    public function createTechnical()
    {
        return view('trainings.technical');
    }

    // Paso 2: Datos Técnicos - Guardar en sesión y redirigir
    public function storeTechnical(Request $request)
    {
        $request->validate([
            'level' => 'required|in:Principiante,Intermedio,Avanzado',
            'available_spots' => 'required|integer|min:1',
            'schedule.days' => 'required|array',
            'schedule.days.*' => 'required|array|min:1',
            'schedule.start_time.*' => 'required|date_format:H:i',
            'schedule.end_time.*' => 'required|date_format:H:i',
            'prices.weekly_sessions.*' => 'required|integer|min:1',
            'prices.price.*' => 'required|numeric|min:0',
        ]);

        // Validar horarios válidos
        foreach ($request->schedule['start_time'] as $index => $start) {
            $end = $request->schedule['end_time'][$index];
            if (strtotime($start) >= strtotime($end)) {
                return back()->with('error', "La hora de fin debe ser posterior a la de inicio en el horario #" . ($index + 1));
            }
        }

        session()->put('training.technical', $request->only(
            'level', 'available_spots', 'schedule', 'prices'
        ));

        return redirect()->route('trainings.create.images');
    }

    // Paso 3: Imágenes - Mostrar vista
    public function createImages()
    {
        return view('trainings.images');
    }

    // Paso 3: Imágenes - Guardar todo en DB
    public function storeImages(Request $request)
    {
        $request->validate([
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photos_description.*' => 'nullable|string|max:255',
        ]);

        $fundamentals = session('training.fundamentals');
        $technical = session('training.technical');

        if (!Auth::user()->medical_fit) {
            return redirect()->route('trainings.create.fundamentals')->with('error', 'Debes subir un apto médico antes de crear un entrenamiento.');
        }

        $training = Training::create([
            'trainer_id' => Auth::id(),
            'park_id' => $fundamentals['park_id'],
            'activity_id' => $fundamentals['activity_id'],
            'title' => $fundamentals['title'],
            'description' => $fundamentals['description'],
            'level' => $technical['level'],
            'available_spots' => $technical['available_spots'],
        ]);

        foreach ($technical['schedule']['days'] as $index => $dayGroups) {
            foreach ($dayGroups as $days) {
                foreach ($days as $day) {
                    TrainingSchedule::create([
                        'training_id' => $training->id,
                        'day' => $day,
                        'start_time' => $technical['schedule']['start_time'][$index],
                        'end_time' => $technical['schedule']['end_time'][$index],
                    ]);
                }
            }
        }

        foreach ($technical['prices']['weekly_sessions'] as $index => $sessions) {
            TrainingPrice::create([
                'training_id' => $training->id,
                'weekly_sessions' => $sessions,
                'price' => $technical['prices']['price'][$index],
            ]);
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $imagePath = $photo->store('training_photos', 'public');
                TrainingPhoto::create([
                    'training_id' => $training->id,
                    'photo_path' => $imagePath,
                    'training_photos_description' => $request->photos_description[$index] ?? 'Imagen del entrenamiento',
                ]);
            }
        }

        Mail::to(Auth::user()->email)->send(new TrainingCreatedMail(Auth::user(), $training));

        session()->forget('training');

        return redirect()->route('trainer.calendar')->with('success', 'Entrenamiento creado exitosamente.');
    }
}
