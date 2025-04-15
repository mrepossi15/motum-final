@extends('layouts.main')

@section('title', 'Crear Entrenamiento')

@section('content')
<div x-data="formHandler" x-init="init()" class="max-w-4xl mx-auto p-4 mt-6">
    <div class="bg-white rounded-lg mt-6 shadow-md px-8 py-4 ">
    <x-spinner wire:model="isLoading" message="Creando entrenamiento..." />
        <!-- Indicador de Paso -->
        <h2 class="text-lg text-orange-500 font-semibold mt-4 mb-2 ">
            Paso <span x-text="step"></span> de 6
        </h2>

        <!-- Título de cada paso -->
        <h1 class="text-2xl font-bold mb-2 text-gray-800">
            <span x-show="step === 1">Nombre y Parque</span>
            <span x-show="step === 2">Actividad</span>
            <span x-show="step === 3">Descripción, Nivel y Cupos</span>
            <span x-show="step === 6">Imágenes</span>
        </h1>

        <!-- Encabezados con botón a la derecha -->
        <div class="flex items-center justify-between mb-2" x-show="step === 4">
            <h1 class="text-2xl font-bold text-gray-800">Horarios</h1>
            <button type="button" id="add-schedule" class="text-orange-500 text-sm font-medium hover:underline transition">
                + Agregar
            </button>
        </div>

        <div class="flex items-center justify-between mb-2" x-show="step === 5">
            <h1 class="text-2xl font-bold text-gray-800">Precios</h1>
            <button type="button" id="add-price-button" class="text-orange-500 text-sm font-medium hover:underline transition">
                + Agregar
            </button>
        </div>

        <!-- Formulario -->
        <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data" class="mt-4"  @submit="handleSubmit">
            @csrf

            <!-- Paso 1: Datos básicos -->
            <div x-show="step === 1" class="">
                <x-form.input name="title" label="Título del entrenamiento *" placeholder="Ej: Running al atardecer" required />
                <div class="relative flex items-center justify-center my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>
                <div>
                    <x-form.select 
                        name="park_id" 
                        label="Seleccioná un parque *" 
                        :options="$parks->pluck('name', 'id')" 
                        :selected="old('park_id', $selectedParkId)"
                        x-on:change="updateMap($event.target.value)"
                    />

                    <!-- Mapa -->
                    <div class="h-64 w-full rounded-md overflow-hidden border border-gray-300">
                        <div id="map" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
             <!-- Paso 2: Datos básicos -->
            <div x-show="step === 2" x-data="{ selectedActivity: '{{ old('activity_id') }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">¿Qué tipo de actividad es?</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 ">
                    @foreach($activities as $activity)
                        <label 
                            class="cursor-pointer border rounded-xl p-4 text-center font-medium transition 
                                hover:border-orange-400 hover:bg-orange-50"
                            :class="{ 
                                'border-orange-500 bg-orange-100 text-orange-700': selectedActivity === '{{ $activity->id }}',
                                'border-gray-300 text-gray-700': selectedActivity !== '{{ $activity->id }}' 
                            }"
                            @click.prevent="selectedActivity = '{{ $activity->id }}'">
                            {{ $activity->name }}
                            <input type="radio" name="activity_id" value="{{ $activity->id }}" class="hidden"
                                :checked="selectedActivity === '{{ $activity->id }}'">
                        </label>
                    @endforeach
                </div>
                <p data-error="activity_id" class="text-red-500 text-sm mt-1 hidden"></p>

            </div>
             <!-- Paso 3: Datos básicos -->
            <div x-show="step === 3" class="space-y-6">
                <!-- Descripción -->
                <x-form.textarea name="description" label="Descripción" placeholder="¿Qué pueden esperar tus alumnos?" />
                <div class="relative flex items-center justify-center ">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>
                <div x-data="{ selectedLevel: '{{ old('level') }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nivel del entrenamiento</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach (['Principiante', 'Intermedio', 'Avanzado'] as $level)
                            <label @click="selectedLevel = '{{ $level }}'"
                                :class="{ 'border-orange-500 bg-orange-50 text-orange-600': selectedLevel === '{{ $level }}' }"
                                class="cursor-pointer border border-gray-300 rounded-xl px-6 py-4 text-center font-medium transition hover:border-orange-400 hover:bg-orange-100">
                                {{ $level }}
                                <input type="radio" name="level" value="{{ $level }}" class="hidden"
                                    :checked="selectedLevel === '{{ $level }}'">
                            </label>
                        @endforeach
                    </div>
                    <p data-error="level" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
                <div class="relative flex items-center justify-center my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>

                <!-- Cupos -->
                <div class="w-full md:w-1/3">
                    <x-form.input name="available_spots" type="number" label="Cupos" placeholder="Ej: 20" required />
                </div>
            </div>
            <div x-show="step === 4" class="space-y-6" x-data="{ scheduleCount: {{ count(old('schedule.days', [[]])) }} }">
                <div id="schedule-container" class="space-y-4">
                    @php $schedules = old('schedule.days', [[]]); @endphp
                    @foreach ($schedules as $index => $scheduleDays)
                        <div class="p-4 border border-gray-300 rounded-md shadow-sm bg-white ">
                            <div class="flex justify-between items-center ">
                                <h3 class="text-sm font-medium text-gray-700">
                                    Horario N° {{ $index + 1 }}
                                </h3>
                            </div>
                            <div class="pt-4">
                                <!-- Días -->
                                <x-form.checkbox-group 
                                    name="schedule[days][{{ $index }}][]" 
                                    label="Días"
                                    :options="['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']"
                                    :selected="old('schedule.days.' . $index, [])"
                                    hideLabel="true"
                                />
                                <p data-error="schedule_days" class="text-red-500 text-sm mt-1 hidden"></p>


                                <!-- Hora de inicio y fin -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div class="relative">
                                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Inicio *</label>
                                        <input type="time" name="schedule[start_time][{{ $index }}]" required
                                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500" />
                                    </div>

                                    <div class="relative">
                                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Fin *</label>
                                        <input type="time" name="schedule[end_time][{{ $index }}]" required
                                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500" />
                                    </div>
                                    <p data-error="schedule_time" class="text-red-500 text-sm mt-1 hidden"></p>
                                </div>
                                <p data-error="schedule_general" class="text-red-500 text-sm mt-2 hidden"></p>
                               
                            </div>
                        </div>
                        
                    @endforeach
                </div>
            </div>
            <!-- Paso 5: Precios -->
            <div x-show="step === 5" class="" x-data="{ priceCount: 1 }">
                <div id="prices" >
                    <div class="p-4 border border-gray-300 rounded-md shadow-sm bg-white">
                        
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-700">
                                Precio N° <span x-text="priceCount"></span>
                            </h3>
                        
                        </div>

                        <!-- Precio individual (este se clona con JS) -->
                        <div class="pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative">
                                    <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">
                                        Veces por semana *
                                    </label>
                                    <input type="number" name="prices[weekly_sessions][]" required
                                        class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                        <p data-error="weekly_sessions" class="text-red-500 text-sm mt-1 hidden"></p>
                                    </div>

                                <div class="relative">
                                    <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">
                                        Precio *
                                    </label>
                                    <input type="number" name="prices[price][]" required
                                        class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                        <p data-error="price" class="text-red-500 text-sm mt-1 hidden"></p>
                                    </div>
                                    <p data-error="prices_general" class="text-red-500 text-sm mt-2 hidden"></p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
                
            <!-- Paso 6: Imágenes -->
            <div x-show="step === 6"  x-data="photoPreview()">
                <div class="text-left">
                    <p class="text-md text-gray-700 mb-2">Vas a necesitar al menos una imagen. Podés agregar más o hacer cambios más adelante.</p>
                </div>
                <!-- Área de carga (se oculta si hay fotos) -->
                <div
                    :class="photos.length > 0 ? 'mb-4 flex justify-start' : 'border-2 border-dashed border-gray-300 rounded-xl p-8 flex flex-col items-center justify-center text-center transition'">
                    
                    <template x-if="photos.length === 0">
                        <x-lucide-image class="w-16 h-16 mb-4 text-orange-200" />
                    </template>
                    <label for="photos"
                        class="inline-block px-6 py-2 border border-orange-500 text-orange-500 rounded-md cursor-pointer hover:bg-orange-100 transition">
                        Agregá fotos
                    </label>
                    <input type="file" id="photos" name="photos[]" multiple accept="image/*"
                        @change="previewImages($event)" class="hidden">
                </div>
                <!-- Vista previa -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                    <template x-for="(photo, index) in photos" :key="index">
                        <div class="relative w-full h-24 rounded overflow-hidden shadow bg-white">
                            <img :src="photo.url" class="w-full h-full object-cover" />
                            <button @click="removeImage(index)"
                                class="absolute top-1 right-1 w-6 h-6 flex items-center justify-center bg-white rounded-full shadow">
                                <x-lucide-square-x class="w-4 h-4 text-red-500" />
                            </button>
                        </div>
                    </template>
                </div>
                <p data-error="photos" class="text-red-500 text-sm mt-2 hidden"></p>
            </div>
            <!-- Botones de Navegación -->
            <div class="flex justify-between mt-4">
                <!-- Botón de volver alineado a la izquierda -->
                <div>
                    <button type="button" @click="previousStep" x-show="step > 1" class="bg-gray-500 text-white p-3 rounded-md">
                        <x-lucide-arrow-left class="w-5 h-5 text-white" />
                    </button>
                </div>

                <!-- Botones de avanzar / omitir / registrar alineados a la derecha -->
                <div class="flex space-x-4">
                    
                    <template x-if="step < 6">
                        <button type="button" @click="nextStep" class="bg-orange-500 text-white p-3 rounded-md hover:bg-orange-600 transition">
                            <x-lucide-arrow-right class="w-5 h-5 text-white" />
                        </button>
                    </template>

                    <template x-if="step === 6">
                    <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-md hover:bg-orange-600 transition">
                        Guardar Entrenamiento
                    </button>
                    </template>
                </div>
            </div>
            
           
        </form>
    </div>
</div>
@php
    $parksArray = $parks->mapWithKeys(fn($p) => [
        $p->id => [
            'name' => $p->park_name,
            'lat' => $p->latitude,
            'lng' => $p->longitude,
        ]
    ])->toArray();
@endphp

<script>
    window.PARKS = @json($parksArray);
</script>
@push('scripts')
<script src="{{ asset('js/entrenamientos/create.js') }}"></script>
@endpush
<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&callback=initMap">
</script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formHandler', () => ({
            // === STATE ===
            step: 1,
            errors: {},
            existingUserError: false,
            isLoading: false,
            // === INIT ===
            init() {
                document.addEventListener('keydown', async (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        if (this.step < 6) {
                            await this.nextStep();
                        } else {
                            this.submitForm();
                        }
                    }
                });
            },
            formatErrors() {
                return 'Por favor corrige los siguientes errores:\n\n' + Object.values(this.errors).join('\n');
            },

            // === VALIDATION FLOW ===
            async nextStep() {
                const isValid = await this.validateStep();
                if (isValid) this.step++;
            },

            previousStep() {
                if (this.step > 1) this.step--;
            },

            async validateStep() {
                this.errors = {};
                this.existingUserError = false;

                if (this.step === 1) return await this.validateStepOne();
                if (this.step === 2) return await this.validateStepTwo();
                if (this.step === 3) return await this.validateStepThree();
                if (this.step === 4) return await this.validateStepFour();
                if (this.step === 5) return await this.validateStepFive();
                if (this.step === 6) return await this.validateStepSix();

                return true;
            },

            async validateStepOne() {
                const fields = {
                title: document.querySelector('[name="title"]').value.trim(),
                parkId: document.querySelector('[name="park_id"]').value
                }
                if (!fields.title) this.errors.title = 'El título es obligatorio';
                if (!fields.parkId) this.errors.park_id = 'Seleccionar un parque es obligatorio';
                

                if (Object.keys(this.errors).length > 0) {
                    return false;
                }

                return true;
            },
            async validateStepTwo() {
                this.errors = {};
                const activityId = document.querySelector('input[name="activity_id"]:checked');
                if (!activityId) this.errors.activity_id = 'La actividad es obligatoria';
         
               // Mostrar los errores en los elementos con data-error
               for (const key in this.errors) {
                    const el = document.querySelector(`[data-error="${key}"]`);
                    if (el) {
                        el.innerText = this.errors[key];
                        el.classList.remove('hidden');
                    }
                }

                return Object.keys(this.errors).length === 0;
            },
            async validateStepThree() {
    this.errors = {};

    const description = document.querySelector('[name="description"]').value.trim();
    const level = document.querySelector('input[name="level"]:checked');
    const spots = document.querySelector('[name="available_spots"]').value.trim();

    // Validar campos y registrar errores
    if (!description) this.errors.description = 'La descripción es obligatoria';
    if (!level) this.errors.level = 'Seleccioná un nivel de entrenamiento';
    if (!spots || isNaN(spots) || parseInt(spots) < 1) {
        this.errors.available_spots = 'Ingresá un número válido de cupos';
    }

    // 🧼 Ocultar todos los mensajes de error posibles (aunque no se activen en esta pasada)
    ['description', 'level', 'available_spots'].forEach(key => {
        const el = document.querySelector(`[data-error="${key}"]`);
        if (el) {
            el.innerText = '';
            el.classList.add('hidden');
        }
    });

    // Mostrar los errores activos
    for (const key in this.errors) {
        const el = document.querySelector(`[data-error="${key}"]`);
        if (el) {
            el.innerText = this.errors[key];
            el.classList.remove('hidden');
        }
    }

    return Object.keys(this.errors).length === 0;
},
            async validateStepFour() {
                this.errors = {};
                const scheduleBlocks = document.querySelectorAll('#schedule-container > div');

                if (scheduleBlocks.length === 0) {
                    this.errors.schedule_general = 'Debés agregar al menos un horario';
                    const el = document.querySelector('[data-error="schedule_general"]');
                    if (el) {
                        el.innerText = this.errors.schedule_general;
                        el.classList.remove('hidden');
                    }
                    return false;
                }

                let valid = true;

                scheduleBlocks.forEach((block, i) => {
                    const days = block.querySelectorAll(`input[name^="schedule[days][${i}][]"]:checked`);
                    const start = block.querySelector(`input[name="schedule[start_time][${i}]"]`)?.value;
                    const end = block.querySelector(`input[name="schedule[end_time][${i}]"]`)?.value;

                    const dayErrorEl = block.querySelector('[data-error="schedule_days"]');
                    const timeErrorEl = block.querySelector('[data-error="schedule_time"]');

                    // Ocultar mensajes anteriores
                    if (dayErrorEl) dayErrorEl.classList.add('hidden');
                    if (timeErrorEl) timeErrorEl.classList.add('hidden');

                    // Validar días
                    if (!days.length) {
                        if (dayErrorEl) {
                            dayErrorEl.innerText = `Seleccioná al menos un día `;
                            dayErrorEl.classList.remove('hidden');
                        }
                        valid = false;
                    }

                    // Validar horarios
                    if (!start || !end) {
                        if (timeErrorEl) {
                            timeErrorEl.innerText = `Completá las horas de inicio y fin `;
                            timeErrorEl.classList.remove('hidden');
                        }
                        valid = false;
                    } else if (start >= end) {
                        if (timeErrorEl) {
                            timeErrorEl.innerText = `La hora de fin debe ser posterior a la de inicio `;
                            timeErrorEl.classList.remove('hidden');
                        }
                        valid = false;
                    }
                });

                return valid;
            },
            async validateStepFive() {
                this.errors = {};
                const priceBlocks = document.querySelectorAll('#prices > div');
                const scheduleBlocks = document.querySelectorAll('#schedule-container > div');

                // Calcular cuántos días da clase en total
                let totalClassDays = 0;

                scheduleBlocks.forEach((block, i) => {
                    const selectedDays = block.querySelectorAll(`input[name^="schedule[days][${i}][]"]:checked`);
                    totalClassDays += selectedDays.length;
                });

                if (priceBlocks.length === 0) {
                    this.errors.prices_general = 'Debés agregar al menos una opción de precio';
                    const el = document.querySelector('[data-error="prices_general"]');
                    if (el) {
                        el.innerText = this.errors.prices_general;
                        el.classList.remove('hidden');
                    }
                    return false;
                }

                let isValid = true;

                priceBlocks.forEach((block, i) => {
                    const sessionsInput = block.querySelector('input[name="prices[weekly_sessions][]"]');
                    const priceInput = block.querySelector('input[name="prices[price][]"]');
                    const sessionVal = sessionsInput?.value;
                    const priceVal = priceInput?.value;

                    const sessionError = block.querySelector('[data-error="weekly_sessions"]');
                    const priceError = block.querySelector('[data-error="price"]');

                    if (sessionError) sessionError.classList.add('hidden');
                    if (priceError) priceError.classList.add('hidden');

                    if (!sessionVal || parseInt(sessionVal) <= 0) {
                        if (sessionError) {
                            sessionError.innerText = `La cantidad de veces por semana es obligatorio`;
                            sessionError.classList.remove('hidden');
                        }
                        isValid = false;
                    } else if (parseInt(sessionVal) > totalClassDays) {
                        if (sessionError) {
                            sessionError.innerText = `No podés ofrecer ${sessionVal} sesiones por semana si solo das clase ${totalClassDays} día(s)`;
                            sessionError.classList.remove('hidden');
                        }
                        isValid = false;
                    }

                    if (!priceVal || parseFloat(priceVal) < 0) {
                        if (priceError) {
                            priceError.innerText = `El precio es obligatorio`;
                            priceError.classList.remove('hidden');
                        }
                        isValid = false;
                    }
                });

                return isValid;
            },
            async validateStepSix() {
    this.errors = {};

    const fileInput = document.querySelector('#photos');
    const files = fileInput?.files;

    if (!files || files.length === 0) {
        this.errors.photos = 'Debés subir al menos una imagen para continuar.';
    } else {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSizeInBytes = 2 * 1024 * 1024; // 2MB

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            if (!allowedTypes.includes(file.type)) {
                this.errors.photos = `El archivo "${file.name}" no es una imagen válida. Usá JPG o PNG.`;
                break;
            }

            if (file.size > maxSizeInBytes) {
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                this.errors.photos = `El archivo "${file.name}" pesa ${sizeInMB}MB y supera el máximo de 2MB permitido.`;
                break;
            }
        }
    }

    // Mostrar los errores en los elementos con data-error
    for (const key in this.errors) {
        const el = document.querySelector(`[data-error="${key}"]`);
        if (el) {
            el.innerText = this.errors[key];
            el.classList.remove('hidden');
        }
    }

    return Object.keys(this.errors).length === 0;
},

            // === SUBMIT ===
            submitForm() {
                document.querySelector('form').submit();
            },

            async handleSubmit(event) {
                event.preventDefault();
        
                // Validar último paso
                const isValid = await this.validateStepSix();
                if (!isValid) return;

                this.isLoading = true;
                event.target.submit();
            }
            
        }));
    });
</script>
@endsection
