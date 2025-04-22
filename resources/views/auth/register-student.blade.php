@extends('layouts.main')

@section('title', 'Crear Usuario')

@section('content')
<div x-data="formHandler(@json(old()))" x-ref="formHandler" class="max-w-2xl mx-auto md:p-6 p-4 mt-6">
    <!-- Overlay de Carga (AGREGADO) -->
    <x-spinner wire:model="isLoading" message="Creando usuario..." />
    <div class="bg-white rounded-xl mt-6 md:shadow-xl md:mt-6  md:p-6 p-2 ">
        <!-- Indicador de Paso -->
        <h2 class="text-lg text-orange-500 font-semibold mt-2">
            Paso <span x-text="step"></span> de 2
        </h2>
        <p class="text-sm text-gray-500 mt-1">Los campos marcados con <span class="text-red-500 font-bold">*</span> son obligatorios.</p>
        <!-- Título de cada paso -->
        <h1 class="text-2xl font-bold mt-2 text-black-500">
            <span x-show="step === 1">Registro de Alumno</span>
            <span x-show="step === 2">Información adicional</span>
        </h1>
        <!-- Formulario -->
        <form action="{{ route('store.student') }}" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="handleSubmit">
            @csrf
             <!-- Paso 1: Datos personales -->
             <div x-show="step === 1" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input name="first_name" label="Nombre " placeholder="Tu nombre" x-model="first_name"  required />
                    <x-form.input name="last_name" label="Apellido" placeholder="Tu apellido" x-model="last_name"  required />
                </div>

                <input type="hidden" name="name" x-model="fullName" />

                <div class="relative flex items-center justify-center my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>

                <x-form.input type="email" name="email" label="Correo Electrónico" placeholder="ejemplo@correo.com" required />
                <x-form.input type="password" name="password" label="Contraseña" placeholder="Crea una contraseña"  required />
                <x-form.input type="password" name="password_confirmation" label="Confirmar Contraseña" placeholder="Repite tu contraseña"  required />
                <div class="relative flex items-center justify-center mt-4 mb-0">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>
                <div>
                    <p class="mb-2 text-sm text-gray-800">Fecha de nacimiento<span class="text-red-500">*</span></p>
                    <div class="grid grid-cols-3 sm:gap-4 gap-2">
                        <x-form.select 
                            name="day" 
                            label="Día *" 
                            :options="array_combine(range(1, 31), range(1, 31))"
                            :placeholder="'Día'"
                            x-model="day"
                            :selected="old('day')" 
                            label-hidden="true"
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
                            placeholder="Mes"
                            label-hidden="true"
                        />
                        <x-form.select 
                            name="year" 
                            label="Año *" 
                            :options="array_combine(range(date('Y'), 1900), range(date('Y'), 1900))"
                            x-model="year"
                            :selected="old('year')" 
                            placeholder="Año"
                            label-hidden="true"
                        />
                    </div>

                    <div x-show="errors.day" class="text-red-500 text-sm mt-2 ">
                        <span x-text="errors.day"></span>
                    </div>
                </div>

                <input type="hidden" name="birth" x-ref="birth" />

            </div>
            <!-- Paso 2: Información adicional -->
            <div x-show="step === 2" class="space-y-4">
                <x-form.textarea name="biography" label="Breve biografía" placeholder="Escribe algo sobre ti..." />
                <div>
                    <label class="block mb-2 text-sm text-gray-800">
                        Disciplinas en las que te especializás
                    </label>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($activities as $activity)
                            <label
                                x-data="{ checked: false }"
                                @click="checked = !checked"
                                :class="checked ? 
                                    'border-orange-500 bg-orange-100 text-orange-700' : 
                                    'border-gray-300 text-gray-700'"
                                class="cursor-pointer border rounded-xl p-4 text-center font-medium transition hover:border-orange-400 hover:bg-orange-50"
                            >
                                {{ $activity->name }}
                                <input 
                                    type="checkbox"
                                    name="activities[]"
                                    value="{{ $activity->id }}"
                                    @change="checked = $el.checked"
                                    class="hidden"
                                >
                            </label>
                        @endforeach
                    </div>
                    @error('activities')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- Botones de Navegación -->
            <div class="flex justify-between mt-4">
                <div>
                    <button type="button" @click="previousStep" x-show="step > 1" class="bg-gray-500 text-white p-3 rounded-md">
                        <x-lucide-arrow-left class="w-5 h-5 text-white" />
                    </button>
                </div>
                <div class="flex space-x-4">
                    <template x-if="step < 2">
                        <button type="button" @click="nextStep" class="bg-orange-500 text-white p-3 rounded-md hover:bg-orange-600 transition">
                            <x-lucide-arrow-right class="w-5 h-5 text-white" />
                        </button>
                    </template>
                    <template x-if="step === 2">
                        <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-md hover:bg-orange-600 transition">
                        Crear usuario
                        </button>
                    </template>
                </div>

            </div>
        </form>
    </div>
    
    <!-- Modal de Error -->
    <div 
        x-show="showErrorModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="bg-white rounded-lg shadow-lg w-96 p-6">
            <h2 class="text-xl font-bold text-red-600 mb-4">Error al crear el usuario</h2>
            <p class="text-black mb-4" x-text="errorMessage"></p>
            <button @click="showErrorModal = false" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                Cerrar
            </button>
        </div>
    </div>
    <div class="text-center mt-6 underline">
            <a href="{{ route('home') }}"  class="text-gray-500 text-sm ">
                Volver al inicio
            </a>
    </div>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formHandler', () => ({
            step: 1,
            errors: {},
            existingUserError: false,
            first_name: '',
            last_name: '',
            fullName: '',
            isLoading: false,
            day: '',
            month: '',
            year: '',
            birth: '',

            init() {
                // Vincular la actualización del nombre completo
                this.$watch('first_name', () => this.combineName());
                this.$watch('last_name', () => this.combineName());

                this.$watch('day', () => this.updateBirth());
                this.$watch('month', () => this.updateBirth());
                this.$watch('year', () => this.updateBirth());

                document.addEventListener('keydown', async (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Evitar que el formulario se envíe automáticamente

                        if (this.step < 2) {
                            await this.nextStep(); // Intentar avanzar al siguiente paso
                        } else {
                            this.submitForm(); // Enviar el formulario si es el último paso
                        }
                    }
                });

                // Esto se asegura de que el campo `birth` tenga un valor válido antes de enviar el formulario
                this.$watch('birth', () => {
                    document.querySelector('input[name="birth"]').value = this.birth;
                });
            },

            combineName() {
                this.fullName = `${this.first_name.trim()} ${this.last_name.trim()}`.trim();
                document.querySelector('[name=name]').value = this.fullName; // Actualizar el input hidden
            },

            updateBirth() {
                if (this.day && this.month && this.year) {
                    this.birth = `${this.year}-${String(this.month).padStart(2, '0')}-${String(this.day).padStart(2, '0')}`;
                    
                    console.log("Fecha de nacimiento generada:", this.birth); // 🔥 Verificación en consola

                    // Actualizar explícitamente el input oculto con el valor generado
                    this.$refs.birth.value = this.birth;
                } else {
                    console.error("No se han seleccionado correctamente día, mes o año.");
                }
            },

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

                if (this.step === 1) {
                    let firstName = document.querySelector('[name=first_name]').value.trim();
                    let lastName = document.querySelector('[name=last_name]').value.trim();
                    let email = document.querySelector('[name=email]').value.trim();
                    let password = document.querySelector('[name=password]').value.trim();
                    let passwordConfirmation = document.querySelector('[name=password_confirmation]').value.trim();

                    // Obtener día, mes y año seleccionados
                    let day = document.querySelector('[name=day]').value;
                    let month = document.querySelector('[name=month]').value;
                    let year = document.querySelector('[name=year]').value;

                    // Limpiar errores previos
                    this.errors = {};

                    // Validaciones de campos obligatorios
                    if (!firstName) this.errors.first_name = 'El nombre es obligatorio';
                    if (!lastName) this.errors.last_name = 'El apellido es obligatorio';
                    if (!email) this.errors.email = 'El correo es obligatorio';
                    if (!password) this.errors.password = 'La contraseña es obligatoria';
                    if (password.length < 8) this.errors.password = 'La contraseña debe tener al menos 8 caracteres';
                    if (password !== passwordConfirmation) this.errors.password_confirmation = 'Las contraseñas no coinciden';

                    // 🔥 Validaciones de fecha de nacimiento
                    if (!day || !month || !year) {
                        this.errors.day = 'La fecha de nacimiento es obligatoria. Completa día, mes y año.'; // ✅ El error se asigna al campo `day`
                    } else {
                        this.updateBirth(); // Generar el valor de `birth`
                    }

                    this.combineName(); // Generar fullName a partir de firstName y lastName

                    await this.checkIfUserExists(this.fullName, email);

                    if (Object.keys(this.errors).length > 0) return false;
                }

                return Object.keys(this.errors).length === 0;
            },

            async checkIfUserExists(name, email) {
                const response = await fetch('{{ route("check.user.exists") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name,
                        email
                    })
                });

                const result = await response.json();

                if (result.name) this.errors.first_name = 'El nombre completo ya está registrado.';
                if (result.email) this.errors.email = 'El correo ya está registrado.';
            },

            submitForm() {
                this.updateBirth(); // 🔥 Asegúrate de que se actualiza el campo oculto antes de enviar
                document.querySelector('form').submit(); // Enviar el formulario normalmente
            },

            async handleSubmit(event) {
                event.preventDefault(); // Evitar el envío automático

                this.updateBirth(); // 🔥 Se llama para actualizar el valor de 'birth'
                console.log("Valor de birth antes de enviar:", this.birth);
                if (!this.birth) {
                    console.error('La fecha de nacimiento no se ha generado correctamente. Intenta nuevamente.');
                    alert('La fecha de nacimiento no se ha generado correctamente. Intenta nuevamente.');
                    return;
                }

                console.log("Enviando formulario con birth:", this.birth); // 🔥 Confirmación en consola
                this.isLoading = true; 
                event.target.submit(); // Enviar el formulario normalmente
            },
        }));
    });
</script>

@endsection