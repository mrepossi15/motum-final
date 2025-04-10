@extends('layouts.main')

@section('title', 'Registro de Entrenador')

@section('content')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
<div x-data="formHandler()" x-ref="formHandler" class="max-w-xl mx-auto  p-6 mt-6">
    <!-- Overlay -->
    <x-spinner wire:model="isLoading" message="Registrando entrenador..." />
    <div class="bg-white rounded-xl md:shadow-xl md:mt-6 mt-2 p-6">
        <h2 class="text-lg text-orange-500 font-semibold mt-2">
            Paso <span x-text="step"></span> de 4
        </h2>

        <h1 class="text-2xl font-bold mt-2 text-black-500">
            <span x-show="step === 1">Datos personales</span>
            <span x-show="step === 2">Información profesional</span>
            <span x-show="step === 3">Ubicación preferida</span>
            <span x-show="step === 4">Experiencia laboral</span>
            <span x-show="step === 5">Datos adicionales</span>
        </h1>

        <form action="{{ route('store.trainer') }}" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="handleSubmit">
            @csrf
            <input type="hidden" name="role" value="entrenador">

            <!-- Paso 1: Datos personales -->
            <div x-show="step === 1" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input name="first_name" label="Nombre *" placeholder="Tu nombre" x-model="first_name" />
                    <x-form.input name="last_name" label="Apellido *" placeholder="Tu apellido" x-model="last_name" />
                </div>

                <input type="hidden" name="name" x-model="fullName" />
                <div class="relative flex items-center justify-center my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>

                <x-form.input type="email" name="email" label="Correo Electrónico *" placeholder="ejemplo@correo.com" />
                <x-form.input type="password" name="password" label="Contraseña *" placeholder="Crea una contraseña" />
                <x-form.input type="password" name="password_confirmation" label="Confirmar Contraseña *" placeholder="Repite tu contraseña" />
                <div class="relative flex items-center justify-center mt-4 mb-0">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>
                <div>
                    <p class="mb-2 text-sm text-gray-800">Fecha de nacimiento</p>
                    <div class="grid grid-cols-3 sm:gap-4 gap-2">
                        <x-form.select 
                            name="day" 
                            label="Día *" 
                            :options="array_combine(range(1, 31), range(1, 31))"
                            x-model="day"
                            :selected="old('day')" 
                        />
                        <x-form.select 
                            name="month" 
                            label="Mes *" 
                            :options="[
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ]"
                            x-model="month"
                            :selected="old('month')" 
                        />
                        <x-form.select 
                            name="year" 
                            label="Año *" 
                            :options="array_combine(range(date('Y'), 1900), range(date('Y'), 1900))"
                            x-model="year"
                            :selected="old('year')" 
                        />
                    </div>

                    <div x-show="errors.day" class="text-red-500 text-sm">
                        <span x-text="errors.day"></span>
                    </div>
                </div>

                <input type="hidden" name="birth" x-ref="birth" />
            </div>

             <!-- Paso 2: Información profesional -->
             <div x-show="step === 2" class="space-y-4">
                <x-form.input name="certification" label="Título habilitante *" placeholder="Ej: Entrenador Personal Certificado" />
                <x-form.input type="file" name="certification_pic" label="Foto del título"  class="p-4"  />
                <span class="text-sm text-gray-400">Solo formato jpeg,png,jpg </span>
                <div class="relative flex items-center justify-center my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>
                <div>
                
                    <h2 class="mb-2 text-md text-black">Disciplinas en las que te especializás</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($activities as $activity)
                            <label
                                    x-data="{ checked: false }"
                                    @click="checked = !checked"
                                    :class="checked ? 'bg-orange-400 text-white' : 'bg-gray-50 text-black border-gray-500'"
                                    class="cursor-pointer border hover:border-orange-500 rounded-md px-3 py-3 flex items-center justify-between transition focus-within:ring-1 focus-within:ring-orange-500 w-full sm:w-auto"
                                >
                                <div class="flex items-center space-x-3">
                                    <input
                                        type="checkbox"
                                        name="activities[]"
                                        value="{{ $activity->id }}"
                                        @change="checked = $el.checked"
                                        class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500 focus:border-orange-500 cursor-pointer transition"
                                    />
                                    <span :class="checked ? 'text-white' : 'text-black'">{{ $activity->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

             <!-- Paso 3: Ubicación preferida -->
             <div x-show="step === 3" class="space-y-4">
                <div class="relative">
                    <label for="park_search" 
                        class="absolute top-0 left-3 -mt-2 px-1 bg-white text-gray-600 text-sm transition-all duration-200">
                        Buscar un parque
                    </label>

                    <input 
                        type="text"
                        id="park-search"
                        name="park_search"
                        placeholder="Escribe el nombre del parque"
                        required
                        class="w-full px-4 py-2 text-black border hover:border-orange-500 border-gray-500 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                    >
                    <p x-show="errors.park_search" x-text="errors.park_search" class="text-red-500 text-sm mt-1"></p>
                </div>
            
                <div id="map" class="w-full h-96 rounded-md border border-gray-400"></div>

                <!-- Campos ocultos de ubicación -->
                <input type="hidden" name="park_name" id="park_name" />
                <input type="hidden" name="latitude" id="lat" />
                <input type="hidden" name="longitude" id="lng" />
                <input type="hidden" name="location" id="location" />
                <input type="hidden" name="opening_hours" id="opening_hours" />
                <input type="hidden" name="photo_references" id="photo_references" />
                <input type="hidden" name="rating" id="rating" />
                <input type="hidden" name="reviews" id="reviews" />
            </div>
            <!-- Paso 4: Experiencia laboral -->
            <div x-show="step === 4" class="space-y-4">
               <template x-for="(experience, index) in experiences" :key="index">
                    <div class="relative mb-6 space-y-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-md font-semibold text-orange-600">
                                Experiencia <span x-text="index + 1"></span>
                            </h3>
                            <button 
                                type="button" 
                                @click="removeExperience(index)" 
                                class="text-sm text-red-500 hover:text-red-600 flex items-center gap-1 transition"
                            >
                                Eliminar
                                <x-lucide-x class="w-4 h-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Rol -->
                            <div class="relative">
                                <label 
                                    :for="'experiences[' + index + '][role]'" 
                                    class="absolute top-0 left-3 -mt-2 px-1 bg-white text-gray-600 text-sm transition-all duration-200"
                                >
                                    Rol
                                </label>
                                <input 
                                    type="text" 
                                    :id="'experiences[' + index + '][role]'" 
                                    x-bind:name="'experiences[' + index + '][role]'" 
                                    x-model="experience.role"
                                    class="w-full px-4 py-2 text-black border hover:border-orange-500 border-gray-500 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Ej: Profesor de Educación Física"
                                />
                            </div>

                            <!-- Empresa -->
                            <div class="relative">
                                <label 
                                    :for="'experiences[' + index + '][company]'" 
                                    class="absolute top-0 left-3 -mt-2 px-1 bg-white text-gray-600 text-sm transition-all duration-200"
                                >
                                    Empresa
                                </label>
                                <input 
                                    type="text" 
                                    :id="'experiences[' + index + '][company]'" 
                                    x-bind:name="'experiences[' + index + '][company]'" 
                                    x-model="experience.company"
                                    class="w-full px-4 py-2 text-black border hover:border-orange-500 border-gray-500 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Ej: Club Atlético X" 
                                />
                            </div>

                            <!-- Año de inicio -->
                            <div class="relative">
                                <label 
                                    :for="'experiences[' + index + '][year_start]'" 
                                    class="absolute top-0 left-3 -mt-2 px-1 bg-white text-gray-600 text-sm transition-all duration-200"
                                >
                                    Año de Inicio *
                                </label>
                                <input 
                                    type="number" 
                                    :id="'experiences[' + index + '][year_start]'" 
                                    x-bind:name="'experiences[' + index + '][year_start]'" 
                                    x-model="experience.year_start"
                                    class="w-full px-4 py-2 text-black border hover:border-orange-500 border-gray-500 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Ej: 2020" 
                                />
                            </div>

                            <!-- Año de fin -->
                            <div class="relative" x-show="!experience.currently_working">
                                <label 
                                    :for="'experiences[' + index + '][year_end]'" 
                                    class="absolute top-0 left-3 -mt-2 px-1 bg-white text-gray-600 text-sm transition-all duration-200"
                                >
                                    Año de Fin
                                </label>
                                <input 
                                    type="number" 
                                    :id="'experiences[' + index + '][year_end]'" 
                                    x-bind:name="'experiences[' + index + '][year_end]'" 
                                    x-model="experience.year_end"
                                    class="w-full px-4 py-2 text-black border hover:border-orange-500 border-gray-500 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Ej: 2023" 
                                />
                            </div>
                        </div>

                        <!-- Checkbox: Actualmente trabajando -->
                        <div class="flex items-center gap-2">
                            <input type="hidden" x-bind:name="'experiences[' + index + '][currently_working]'" value="0" />
                            <input 
                                type="checkbox" 
                                x-bind:name="'experiences[' + index + '][currently_working]'" 
                                value="1"
                                x-model="experience.currently_working"
                                class="text-orange-500 focus:ring-orange-500 border-gray-300 rounded" 
                            />
                            <label class="text-sm text-gray-700">Actualmente trabajando aquí</label>
                        </div>
                        <div class="relative flex items-center justify-center my-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Botón agregar experiencia -->
                <div class="flex justify-end">
                    <button 
                        type="button" 
                        @click="addExperience()" 
                        class="bg-orange-100 text-orange-700 px-4 py-2 rounded-md hover:bg-orange-200 transition"
                    >
                        + Agregar
                    </button>
                </div>
            </div>

            <!-- Paso 5: Datos adicionales -->
            <div x-show="step === 5" class="space-y-4">
                <x-form.input name="phone" type="number" label="Número de teléfono *" placeholder="Ej:1155661572" x-model="phone" value="{{ old('phone') }}" />
                <x-form.textarea name="biography" label="Breve Biografía (Opcional)" placeholder="Máximo 500 caracteres" />
            </div>

            <!-- Navegación entre pasos -->
            <div class="flex justify-between mt-4">
                <!-- Botón de volver alineado a la izquierda -->
                <div>
                    <button type="button" @click="previousStep" x-show="step > 1" class="bg-gray-500 text-white p-3 rounded-md">
                        <x-lucide-arrow-left class="w-5 h-5 text-white" />
                    </button>
                </div>

                <!-- Botones de avanzar / omitir / registrar alineados a la derecha -->
                <div class="flex space-x-4">
                    <template x-if="step === 4">
                        <button type="button" @click="nextStep" class="bg-orange-200 text-orange-700 px-4 py-2 rounded-md hover:bg-orange-300 transition">
                            Omitir
                        </button>
                    </template>

                    <template x-if="step < 5">
                        <button type="button" @click="nextStep" class="bg-orange-500 text-white p-3 rounded-md hover:bg-orange-600 transition">
                            <x-lucide-arrow-right class="w-5 h-5 text-white" />
                        </button>
                    </template>

                    <template x-if="step === 5">
                        <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-md hover:bg-orange-600 transition">
                            Registrar como Entrenador
                        </button>
                    </template>
                </div>
            </div>
        </form>
    </div>
    <div x-show="showAgeModal" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full text-center">
            <h2 class="text-lg font-bold text-red-600 mb-4">¡Atención!</h2>
            <p class="text-gray-700 mb-6">Debés tener al menos 18 años para registrarte como entrenador.</p>
            <button 
                @click="showAgeModal = false"
                class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 transition">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formHandler', () => ({
            // === STATE ===
            step: 1,
            errors: {},
            existingUserError: false,
            isLoading: false,
            first_name: '',
            last_name: '',
            fullName: '',
            day: '',
            month: '',
            year: '',
            birth: '',
            showAgeModal: false,
            experiences: [
                {
                    role: '',
                    company: '',
                    year_start: '',
                    year_end: '',
                    currently_working: false
                }
            ],

            // === INIT ===
            init() {
                this.$watch('first_name', () => this.combineName());
                this.$watch('last_name', () => this.combineName());

                this.$watch('day', () => this.updateBirth());
                this.$watch('month', () => this.updateBirth());
                this.$watch('year', () => this.updateBirth());

                this.$watch('birth', () => {
                    document.querySelector('input[name="birth"]').value = this.birth;
                });

                document.addEventListener('keydown', async (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        if (this.step < 3) {
                            await this.nextStep();
                        } else {
                            this.submitForm();
                        }
                    }
                });
            },
            combineName() {
                this.fullName = `${this.first_name.trim()} ${this.last_name.trim()}`.trim();
                document.querySelector('[name=name]').value = this.fullName;
            },

            updateBirth() {
                if (this.day && this.month && this.year) {
                    this.birth = `${this.year}-${String(this.month).padStart(2, '0')}-${String(this.day).padStart(2, '0')}`;
                    console.log("Fecha de nacimiento generada:", this.birth);
                    this.$refs.birth.value = this.birth;
                } else {
                    console.error("No se han seleccionado correctamente día, mes o año.");
                }
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
                if (this.step === 5) return await this.validateStepFive();

                return true;
            },

            async validateStepOne() {
                const fields = {
                    first_name: document.querySelector('[name=first_name]').value.trim(),
                    last_name: document.querySelector('[name=last_name]').value.trim(),
                    email: document.querySelector('[name=email]').value.trim(),
                    password: document.querySelector('[name=password]').value.trim(),
                    password_confirmation: document.querySelector('[name=password_confirmation]').value.trim(),
                    day: document.querySelector('[name=day]').value,
                    month: document.querySelector('[name=month]').value,
                    year: document.querySelector('[name=year]').value
                };

                if (!fields.first_name) this.errors.first_name = 'El nombre es obligatorio';
                if (!fields.last_name) this.errors.last_name = 'El apellido es obligatorio';
                if (!fields.email) this.errors.email = 'El correo es obligatorio';
                if (!fields.password) this.errors.password = 'La contraseña es obligatoria';
                if (fields.password.length < 8) this.errors.password = 'La contraseña debe tener al menos 8 caracteres';
                if (fields.password !== fields.password_confirmation) this.errors.password_confirmation = 'Las contraseñas no coinciden';
                if (!fields.day || !fields.month || !fields.year) {
                    this.errors.day = 'La fecha de nacimiento es obligatoria';
                } else {
                    this.updateBirth();

                    // Validar edad mínima
                    const birthDate = new Date(fields.year, fields.month - 1, fields.day);
                    const today = new Date();
                    const age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();

                    const isUnder18 = (age < 18) || (age === 18 && m < 0) || (age === 18 && m === 0 && today.getDate() < birthDate.getDate());

                    if (isUnder18) {
                        this.showAgeModal = true;
                        return false;
                    }
                }

                this.combineName();
                await this.checkIfNameOrEmailExists(this.fullName, fields.email);

                if (Object.keys(this.errors).length > 0) {
                    return false;
                }

                return true;
            },
            async validateStepTwo(){
                const certification = document.querySelector('[name=certification]').value.trim();
                const certificationPic = document.querySelector('[name=certification_pic]').files[0];
                if (!certification) {
                    this.errors.certification = 'El título habilitante es obligatorio';
                }
                // Validar la imagen solo si cargaron una
                if (certificationPic) {
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!allowedTypes.includes(certificationPic.type)) {
                        this.errors.certification_pic = 'La imagen debe ser JPG o PNG.';
                    }
                }

                if (Object.keys(this.errors).length > 0) {
                    return false;
                }

                return true;
            },
            async validateStepThree() {
                this.errors = {};
                const park_search = document.querySelector('[name=park_search]').value.trim();
                const latitude = document.querySelector('[name=latitude]').value.trim();
                const longitude = document.querySelector('[name=longitude]').value.trim();

                if (!park_search) {
                    this.errors.park_search = 'El parque es obligatorio.';
                }

                if (!latitude || !longitude) {
                    this.errors.park_search = 'Debés seleccionar un parque.';
                }

                return Object.keys(this.errors).length === 0;
            },
            async validateStepFive() {
                this.errors = {};
                const phone = document.querySelector('[name=phone]').value.trim();

                if (!phone) return true;

                const exists = await this.checkIfPhoneExists(phone);

                if (exists) {
                    this.errors.phone = 'El número ya está registrado. Por favor, ingresá otro.';
                    return false;
                }

                return true;
            },

            // === API CALLS ===
            async checkIfNameOrEmailExists(name, email) {
                const response = await fetch('{{ route("check.user.exists") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name, email })
                });

                const result = await response.json();
                if (result.name) this.errors.first_name = 'El nombre completo ya está registrado.';
                if (result.email) this.errors.email = 'El correo ya está registrado.';
            },

            async checkIfPhoneExists(phone) {
                const response = await fetch('{{ route("check.user.exists") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone })
                });

                const result = await response.json();
                return result.phone === true; // <- Esto es clave
            },

            // === EXPERIENCIAS ===
            addExperience() {
                this.experiences.push({
                    role: '',
                    company: '',
                    year_start: '',
                    year_end: '',
                    currently_working: false
                });
            },

            removeExperience(index) {
                this.experiences.splice(index, 1);
            },

            // === SUBMIT ===
            submitForm() {
                this.updateBirth();
                document.querySelector('form').submit();
            },

            async handleSubmit(event) {
                event.preventDefault();
                this.updateBirth();

                // Validar último paso
                const isValid = await this.validateStepFive();
                if (!isValid) return;

                this.isLoading = true;
                event.target.submit();
            }
            
        }));
    });
</script>

<script src="/js/mapas/showMap.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
@endsection