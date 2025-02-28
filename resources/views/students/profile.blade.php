@extends('layouts.main')

@section('title', 'Perfil de Usuario')

@section('content')

<div class="container mx-auto mt-8 p-6 bg-white shadow-lg rounded-lg max-w-lg">
    <!-- Encabezado -->
    <div class="bg-orange-500 text-white text-center py-4 rounded-t-lg">
        <h2 class="text-xl font-semibold">Perfil de Usuario</h2>
    </div>

    <div class="p-6 text-center">
        <!-- Imagen de Perfil -->
        <div class="mb-4">
            @if($user->profile_pic)
                <img src="{{ asset('storage/' . $user->profile_pic) }}" 
                     alt="Foto de perfil" 
                     class="w-32 h-32 rounded-full mx-auto border-4 border-gray-300 shadow-md">
            @else
                <img src="{{ asset('images/default-profile.png') }}" 
                     alt="Foto de perfil por defecto" 
                     class="w-32 h-32 rounded-full mx-auto border-4 border-gray-300 shadow-md">
            @endif
        </div>

        <!-- Información del Usuario -->
        <p class="text-gray-800"><strong>📛 Nombre:</strong> {{ $user->name }}</p>
        <p class="text-gray-800"><strong>📧 Email:</strong> {{ $user->email }}</p>
        
        @if($user->role === 'entrenador')
            <p class="text-gray-800"><strong>🎓 Certificación:</strong> {{ $user->certification ?? 'No especificada' }}</p>
        @endif

        <p class="text-gray-800 font-semibold mt-4">📖 Biografía:</p>
        <p class="text-gray-600 italic">{{ $user->biography ?? 'No especificada' }}</p>
    </div>

    <!-- Botón de edición si el usuario autenticado es el dueño del perfil -->
    @if(auth()->id() === $user->id)
        <div class="bg-gray-100 text-center py-4 rounded-b-lg">
            <a href="{{ route('students.edit') }}" 
               class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600 transition">
                ✏️ Editar Perfil
            </a>
        </div>
    @endif
</div>

@endsection