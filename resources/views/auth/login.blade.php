@extends('layouts.main')

@section('title', 'Login')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-center text-2xl font-semibold text-orange-500 mb-6">Iniciar Sesión</h1>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        <form action="/iniciar-sesion" method="POST" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico *</label>
                <input type="email"
                       id="email"
                       name="email"
                       placeholder="ejemplo@correo.com"
                       value="{{ old('email') }}"
                       class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Escribí tu contraseña"
                       class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror">
                <button type="button"
                        class="absolute inset-y-0 right-3 top-7 text-gray-500 focus:outline-none toggle-password"
                        data-target="password"
                        aria-label="Mostrar/Ocultar contraseña">
                    <i class="bi bi-eye-slash" id="toggle-password-icon"></i>
                </button>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Spinner de carga -->
            <div class="text-center my-3 hidden" id="loading-spinner">
                <div class="inline-block h-6 w-6 animate-spin rounded-full border-4 border-orange-500 border-t-transparent"></div>
            </div>

            <!-- Botón de Iniciar Sesión -->
            <div>
                <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600">
                    Iniciar Sesión
                </button>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="text-center mt-4">
            <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="text-center mt-3">
            <p class="text-sm">¿Eres nuevo? <a href="{{ route('register.student') }}" class="text-orange-500 hover:underline">Regístrate aquí</a></p>
        </div>
    </div>
</div>

<script>
    // Mostrar/Ocultar contraseña
    document.querySelectorAll('.toggle-password').forEach(button => {
        const input = document.getElementById(button.getAttribute('data-target'));

        button.addEventListener('click', function (event) {
            event.preventDefault();
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }

            input.focus();
        });

        input.addEventListener('blur', function () {
            if (input.type === 'text') {
                input.type = 'password';
                const icon = button.querySelector('i');
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });

    // Spinner al enviar el formulario
    document.querySelector('form').addEventListener('submit', function (event) {
        const spinner = document.getElementById('loading-spinner');
        spinner.classList.remove('hidden');

        const submitButton = event.target.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Cargando...';
    });
</script>
@endsection
