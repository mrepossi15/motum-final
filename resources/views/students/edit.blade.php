@extends('layouts.main')

@section('title', 'Editar Perfil')

@section('content')

<div class="container mx-auto mt-8 p-6 bg-white shadow-lg rounded-lg max-w-lg">
    <!-- Encabezado -->
    <div class="bg-orange-500 text-white text-center py-4 rounded-t-lg">
        <h2 class="text-xl font-semibold">Editar Perfil</h2>
    </div>

    <div class="p-6">
        <!-- Mensaje de error -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>❌ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de Edición de Perfil -->
        <form action="{{ route('students.updateProfile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Foto de Perfil -->
            <div class="text-center mb-6">
                @if ($user->profile_pic)
                    <img src="{{ asset('storage/' . $user->profile_pic) }}" 
                         alt="Foto de perfil" 
                         class="w-32 h-32 rounded-full mx-auto border-4 border-gray-300 shadow-md">
                @else
                    <img src="{{ asset('images/default-profile.png') }}" 
                         alt="Foto de perfil por defecto" 
                         class="w-32 h-32 rounded-full mx-auto border-4 border-gray-300 shadow-md">
                @endif
                <div class="mt-3">
                    <label for="profile_pic" class="block text-sm font-semibold text-gray-700">Actualizar Foto de Perfil</label>
                    <input type="file" id="profile_pic" name="profile_pic" 
                           class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
            </div>

            <!-- Nombre -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nombre</label>
                <input type="text" id="name" name="name" value="{{ $user->name }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ $user->email }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>

            <!-- Biografía -->
            <div class="mb-4">
                <label for="biography" class="block text-sm font-semibold text-gray-700">Biografía</label>
                <textarea id="biography" name="biography" rows="4" placeholder="Escribe algo sobre ti..."
                          class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-orange-400 focus:outline-none">{{ $user->biography }}</textarea>
            </div>

            <!-- Apto Médico -->
            <div class="mb-4">
                <label for="medical_fit" class="block text-sm font-semibold text-gray-700">Apto Médico</label>
                <input type="file" id="medical_fit" name="medical_fit" accept="image/*"
                       class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-orange-400 focus:outline-none @error('medical_fit') border-red-500 @enderror">
                @error('medical_fit')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botón Guardar Cambios -->
            <div class="text-center">
                <button type="submit" 
                        class="w-full bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600 transition">
                    💾 Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <!-- Botón Volver -->
    <div class="bg-gray-100 text-center py-4 rounded-b-lg">
        <a href="{{ route('students.profile', ['id' => $user->id]) }}" 
           class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition">
            ⬅️ Volver
        </a>
    </div>
</div>

@endsection