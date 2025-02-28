@extends('layouts.main')

@section('title', "Perfil de {$trainer->name}")

@section('content')
<main class="container mx-auto mt-6">
    <!-- Tarjeta de información del entrenador -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-orange-500 text-white px-6 py-4 flex items-center">
            <img src="{{ $trainer->profile_photo ? asset('storage/' . $trainer->profile_photo) : asset('images/default-profile.png') }}" 
                 alt="Foto de perfil" 
                 class="w-24 h-24 rounded-full border-4 border-white shadow-md">
            <div class="ml-4">
                <h2 class="text-xl font-semibold">{{ $trainer->name }}</h2>
                <p class="text-sm">{{ $trainer->email }}</p>
                <p class="text-sm">{{ $trainer->role === 'entrenador' ? 'Entrenador certificado' : 'Administrador' }}</p>
            </div>
        </div>

        <div class="p-6">
            <h3 class="font-semibold text-lg text-gray-700">📖 Biografía</h3>
            <p class="text-gray-600">{{ $trainer->biography ?? 'No especificada' }}</p>

            <h3 class="font-semibold text-lg text-gray-700 mt-4">🎓 Experiencia</h3>
            <ul class="list-disc list-inside text-gray-700">
                @forelse ($experiences as $experience)
                    <li>{{ $experience->title }} en {{ $experience->company }} ({{ $experience->years }} años)</li>
                @empty
                    <li class="text-gray-500">No hay experiencia registrada.</li>
                @endforelse
            </ul>

            <h3 class="font-semibold text-lg text-gray-700 mt-4">🏋️ Entrenamientos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($trainings as $training)
                    <div class="bg-gray-100 p-4 rounded shadow-md">
                        <h4 class="text-orange-500 font-semibold">{{ $training->title }}</h4>
                        <p class="text-gray-700"><strong>Parque:</strong> {{ $training->park->name }}</p>
                        <p class="text-gray-700"><strong>Actividad:</strong> {{ $training->activity->name }}</p>
                        <p class="text-gray-700"><strong>Nivel:</strong> {{ ucfirst($training->level) }}</p>
                        <a href="{{ route('trainings.show', $training->id) }}" class="text-blue-500 hover:underline">Ver detalles →</a>
                    </div>
                @empty
                    <p class="text-gray-500">No hay entrenamientos registrados.</p>
                @endforelse
            </div>

            <h3 class="font-semibold text-lg text-gray-700 mt-4">📍 Parques donde entrena</h3>
            <ul class="list-disc list-inside text-gray-700">
                @forelse ($parks as $park)
                    <li>{{ $park->name }} ({{ $park->location }})</li>
                @empty
                    <li class="text-gray-500">No hay parques registrados.</li>
                @endforelse
            </ul>

            <h3 class="font-semibold text-lg text-gray-700 mt-4">⭐ Reseñas</h3>
            @if($trainer->reviews->isEmpty())
                <p class="text-gray-500">No hay reseñas disponibles.</p>
            @else
                @foreach($trainer->reviews as $review)
                    <div class="border p-3 rounded shadow-sm mt-2">
                        <p><strong>⭐ Calificación:</strong> {{ $review->rating }} / 5</p>
                        <p><strong>Comentario:</strong> {{ $review->comment }}</p>
                        <p><small><strong>Autor:</strong> {{ $review->user->name }}</small></p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</main>
@endsection