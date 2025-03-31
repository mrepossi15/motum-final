@extends('layouts.main')

@section('title', 'Crear Usuario')

@section('content')
<div x-data="formHandler()" x-ref="formHandler" class="max-w-4xl mx-auto p-4 mt-6">
    <!-- Overlay de Carga (AGREGADO) -->
    <div 
        x-show="isLoading"
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50"
        style="display: none;"
    >
        <div class="flex flex-col items-center justify-center space-y-4"> <!-- Cambiado -->
            <h1  class="text-orange-500 font-semibold italic text-2xl">motum</h1>
            <p class="text-white text-lg mb-6">Creando el usuario...</p>
            <!-- Spinner -->
            <div class="loader"></div>
        </div>
    </div>
    <div class="bg-white rounded-lg mt-6 shadow-md p-4">
        <!-- Indicador de Paso -->
        <h2 class="text-lg text-orange-500 font-semibold mt-4">
            Paso <span x-text="step"></span> de 3
        </h2>
        <!-- Título de cada paso -->
        <h1 class="text-2xl font-bold mt-2 text-black-500">
            <span x-show="step === 1">Registro de Alumno</span>
            <span x-show="step === 2">Información adicional</span>
            <span x-show="step === 3">Tus preferencias</span>
        </h1>
        <!-- Formulario -->
        <!-- Modificado -->
<form action="{{ route('store.student') }}" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="handleSubmit">
            
            @csrf
            <!-- Paso 1: Datos básicos -->
            <div x-show="step === 1" class="space-y-6">
                <x-form.input name="name" label="Nombre completo *" placeholder="Tu nombre completo" />
                <x-form.input name="email" type="email" label="Correo Electrónico *" placeholder="ejemplo@correo.com" />
                <x-form.input name="password" type="password" label="Contraseña *" placeholder="Escribe tu contraseña" />
                <x-form.input name="password_confirmation" type="password" label="Confirmar Contraseña *" placeholder="Repite tu contraseña" />
            </div>
            <!-- Paso 2: Información adicional -->
            <div x-show="step === 2" class="space-y-6">
                <x-form.input name="birth" type="date" label="Fecha de Nacimiento *" />

                <x-form.textarea name="biography" label="Breve biografía (Opcional)" placeholder="Escribe algo sobre ti..." />

                <x-form.input name="profile_pic" type="file" label="Foto de Perfil" accept="image/*" />
                <x-form.input name="medical_fit" type="file" label="Apto Médico" accept="image/*" />
            </div>
            <!-- Paso 3: Selección de Actividades -->
            <div x-show="step === 3" class="space-y-6">
                 <h3 class="text-lg font-medium text-black mb-4">Selecciona tus actividades</h3>
    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($activities as $activity)
                            <label 
                                x-data="{ checked: false }" 
                                @click="checked = !checked"
                                :class="checked ? 'bg-orange-400 text-white' : 'bg-gray-50 text-black border-gray-500'" 
                                class="relative cursor-pointer border hover:border-orange-500 rounded-sm p-4 flex items-center justify-between transition focus-within:ring-1 focus-within:ring-orange-500"
                            >
                                <div class="flex items-center space-x-3">
                                    <input 
                                        type="checkbox" 
                                        name="activities[]" 
                                        value="{{ $activity->id }}"
                                        @change="checked = $el.checked"
                                        class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500 focus:border-orange-500 cursor-pointer transition"
                                    >
                                    <span :class="checked ? 'text-white' : 'text-black'" class="font-medium">{{ $activity->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
            </div>
            <!-- Botones de Navegación -->
            <div class="flex justify-end mt-4 space-x-4">
                <button type="button" @click="previousStep" class="bg-gray-500 text-white px-4 py-2 rounded-md"x-show="step > 1">
                    Anterior
                </button>

                <template x-if="step < 3">
                    <button type="button" @click="nextStep"
                        class="bg-orange-500 text-white px-6 py-3 rounded-md hover:bg-orange-600 transition">
                        Siguiente
                    </button>
                </template>

                <template x-if="step === 3">
                    <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-md hover:bg-orange-600 transition">
                        Crear usuario
                    </button>
                </template>
            </div>
        </form>
    </div>
</div>
<style>
    /* Spinner CSS (AGREGADO) */
.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #F97316;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formHandler', () => ({
            step: 1,
            errors: {},
            existingUserError: false,

            init() {
                document.addEventListener('keydown', async (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Evitar que el formulario se envíe automáticamente

                        if (this.step < 3) {
                            await this.nextStep(); // Intentar avanzar al siguiente paso
                        } else {
                            this.submitForm(); // Enviar el formulario si es el último paso
                        }
                    }
                });
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
                    let name = document.querySelector('[name=name]').value.trim();
                    let email = document.querySelector('[name=email]').value.trim();
                    let password = document.querySelector('[name=password]').value.trim();
                    let passwordConfirmation = document.querySelector('[name=password_confirmation]').value.trim();

                    if (!name) this.errors.name = 'El nombre es obligatorio';
                    if (!email) this.errors.email = 'El correo es obligatorio';
                    if (!password) this.errors.password = 'La contraseña es obligatoria';
                    if (password.length < 8) this.errors.password = 'La contraseña debe tener al menos 8 caracteres';
                    if (password !== passwordConfirmation) this.errors.password_confirmation = 'Las contraseñas no coinciden';

                    await this.checkIfUserExists(name, email);

                    if (Object.keys(this.errors).length > 0) return false;
                }

                if (this.step === 2) {
                    let birth = document.querySelector('[name=birth]').value.trim();
                    if (!birth) this.errors.birth = 'La fecha de nacimiento es obligatoria';
                }

                if (this.step === 3) {
                    let checkboxes = document.querySelectorAll('[name="activities[]"]:checked');
                    if (checkboxes.length === 0) this.errors.activities = 'Debes seleccionar al menos una actividad.';
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
                    body: JSON.stringify({ name, email })
                });

                const result = await response.json();

                if (result.name) this.errors.name = 'El nombre ya está registrado.';
                if (result.email) this.errors.email = 'El correo ya está registrado.';
            },

            submitForm() {
                document.querySelector('form').submit(); // Enviar el formulario de Laravel
            },
            isLoading: false, // (AGREGADO)

            // Nuevo método (AGREGADO)
            async handleSubmit(event) {
                this.isLoading = true; // Muestra el spinner
                event.target.submit(); // Enviar el formulario normalmente
            }
        }));
    });
</script>


@endsection