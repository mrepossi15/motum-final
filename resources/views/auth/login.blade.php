@extends('layouts.main')

@section('title', 'Login')

@section('content')

<div class="min-h-screen flex max-sm:bg-white flex-col items-center justify-center ">
    <h1 class="sr-only">Inicia sesión en Motum</h1>
    <div class="w-full bg-white md:max-w-md max-w-sm p-6 md:rounded-lg md:shadow-lg space-y-6 pb-6">
        <div class="text-center mt-2 mb-6">
            <img src="img/motumLogo.png" alt="Motum Logo" class="w-20 mx-auto rounded-md mb-2">
            <h2 class="text-2xl font-bold text-orange-500">Inicia sesión en motum</h2>
        </div>
        <form action="/iniciar-sesion" method="POST" class="space-y-6">
            @csrf
            <!-- Correo Electrónico -->
            <x-form.input name="email" type="email" label="Mail" placeholder="ejemplo@correo.com"/>
            <!-- Contraseña -->
            <x-form.input name="password" type="password" label="Contraseña" placeholder="Escribe tu contraseña" />
            <!-- Spinner de carga -->
            <div class="text-center my-3 hidden" id="loading-spinner">
                <div class="inline-block h-6 w-6 animate-spin rounded-full border-4 border-orange-500 border-t-transparent"></div>
            </div>
            <button type="submit" class="w-full p-3 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition">Iniciar sesión</button>
        </form>
        <div class="text-center ">
            <a href="{{ route('password.request') }}" class="text-orange-600 underline">¿Olvidaste tu contraseña?</a>
        </div>
        <div class="text-center border-t mt-4 pt-6 pb-4">
            <p class="text-gray-500">¿No tenes una cuenta? <a href="{{ route('register') }}" class="text-orange-500 underline "> Registrate aquí</a></p>
        </div>    
    </div>
    <div class="text-center mt-6 underline">
            <a href="{{ route('home') }}"  class="text-gray-500 text-sm ">
                Volver al inicio
            </a>
    </div>
</div>

@endsection