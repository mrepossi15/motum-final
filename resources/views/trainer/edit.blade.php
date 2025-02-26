@extends('layouts.main')

@section('title', 'Registro de Entrenador')

@section('content')

@if ($errors->any())
    <div class="bg-red-100 text-red-800 p-4 rounded-md mb-4">
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('trainer.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 shadow-md rounded-lg max-w-lg mx-auto">
    @csrf
    @method('PUT')

    <!-- Foto de Perfil -->
    <div class="mb-4">
        <label for="profile_pic" class="block text-gray-700 font-bold mb-2">Foto de Perfil</label>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/*" class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <!-- Nombre -->
    <div class="mb-4">
        <label for="name" class="block text-gray-700 font-bold mb-2">Nombre</label>
        <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" required
            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <!-- Correo Electrónico -->
    <div class="mb-4">
        <label for="email" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
        <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" required
            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <!-- Certificación -->
    <div class="mb-4">
        <label for="certification" class="block text-gray-700 font-bold mb-2">Certificación</label>
        <input type="text" id="certification" name="certification" value="{{ auth()->user()->certification }}"
            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <!-- Biografía -->
    <div class="mb-4">
        <label for="biography" class="block text-gray-700 font-bold mb-2">Biografía</label>
        <textarea id="biography" name="biography" rows="4"
            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">{{ auth()->user()->biography }}</textarea>
    </div>

    <!-- Apto Médico -->
    <div class="mb-4">
        <label for="medical_fit" class="block text-gray-700 font-bold mb-2">Apto Médico</label>
        <input type="file" id="medical_fit" name="medical_fit" accept="image/*"
            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300 @error('medical_fit') border-red-500 @enderror">
        @error('medical_fit')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Botón de Enviar -->
    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md font-bold hover:bg-blue-600 transition">
        Actualizar Perfil
    </button>
</form>

@endsection