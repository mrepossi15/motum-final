@extends('layouts.main')

@section('title', 'Login')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-center text-2xl font-semibold text-orange-500 mb-6">Iniciar Sesión</h1>


       

        <!-- Enlaces adicionales -->
        <div class="text-center mt-4">
            <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="text-center mt-3">
            <p class="text-sm">¿Eres nuevo? <a href="{{ route('register.student') }}" class="text-orange-500 hover:underline">Regístrate aquí</a></p>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush
@endsection
