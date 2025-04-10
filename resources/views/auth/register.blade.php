@extends('layouts.main')

@section('title', 'Login')

@section('content')

<div class="min-h-screen flex flex-col items-center justify-center max-sm:bg-white">
    <h1 class="sr-only">¡Registrate en Motum!</h1>
    <div class="w-full bg-white md:max-w-md max-w-sm p-6 rounded-2xl md:shadow-xl space-y-6 pb-6">

        <!-- Logo -->
        <div class="text-center mt-2 mb-6">
            <img src="img/motumLogo.png" alt="Motum Logo" class="w-20 mx-auto rounded-md mb-2">
            <h2 class="text-2xl font-bold text-orange-500">Registrate en motum</h2>
        </div>

        <div class="space-y-4">
            <!-- Botón: Registrarse como Entrenador -->
            <a href="{{ route('register.trainer') }}" class="block p-4 border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition group bg-white hover:bg-orange-50">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="flex items-center space-x-3 whitespace-nowrap">
                        <x-lucide-dumbbell class="w-6 h-6 text-orange-500 group-hover:text-orange-600 transition" />
                        <h3 class="text-base md:text-lg font-semibold text-gray-800 group-hover:text-orange-600 truncate">Regístrate como Entrenador</h3>
                    </div>
                    <x-lucide-arrow-right class="w-6 h-6 text-gray-500 group-hover:text-orange-600 transition" />
                </div>
                <p class="text-sm text-gray-500 mt-1">Crea un perfil para ofrecer tus entrenamientos al aire libre.</p>
            </a>

            <!-- Línea divisoria con texto OR -->
            <div class="relative flex items-center justify-center my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative bg-white px-4 text-sm text-gray-500">o</div>
            </div>

            <!-- Botón: Registrarse como Usuario -->
            <a href="{{ route('register.student') }}" class="block p-4 border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition group bg-white hover:bg-orange-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <x-lucide-user class="w-6 h-6 text-orange-500 group-hover:text-orange-600 transition" />
                        <h3 class="text-base md:text-lg font-semibold text-gray-800 group-hover:text-orange-600">Regístrate como Usuario</h3>
                    </div>
                    <x-lucide-arrow-right class="w-6 h-6 text-gray-500 group-hover:text-orange-600 transition" />
                </div>
                <p class="text-sm text-gray-500 mt-1">Sumate y reservá tus entrenamientos favoritos cerca tuyo.</p>
            </a>
        </div>

        <!-- Link de inicio de sesión -->
        <div class="text-center border-t mt-6 pt-6 pb-2">
            <p class="text-gray-600">¿Ya tenes una cuenta? 
                <a href="{{ route('login') }}" class="text-orange-500 underline">Inicia sesión aquí.</a>
            </p>
        </div>
    </div>

    <!-- Link de retorno al home -->
    <div class="text-center mt-6">
        <a href="{{ route('home') }}" class="text-gray-500 text-sm underline">Volver al inicio</a>
    </div>
</div>

@endsection