@extends('layouts.main')

@section('title', 'Perfil del Entrenador')

@section('content')

<!-- Mensajes de éxito y error -->
@if (session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4 flex justify-between items-center">
        {{ session('success') }}
        <button type="button" class="text-green-900 font-bold" onclick="this.parentElement.remove();">
            &times;
        </button>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 text-red-800 p-4 rounded-md mb-4 flex justify-between items-center">
        {{ session('error') }}
        <button type="button" class="text-red-900 font-bold" onclick="this.parentElement.remove();">
            &times;
        </button>
    </div>
@endif

<div class="container mx-auto mt-5 px-4">

    <!-- Información del Entrenador -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-orange-500 text-white px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">Información del Entrenador</h2>
            @if(auth()->id() === $trainer->id)
                <a href="{{ route('trainer.edit') }}" class="bg-white text-orange-600 px-3 py-1 text-sm rounded-md">
                    Editar Perfil
                </a>
            @endif
        </div>
        <div class="p-6">
            <p><strong>Nombre:</strong> {{ $trainer->name }}</p>
            <p><strong>Correo Electrónico:</strong> {{ $trainer->email }}</p>
            <p><strong>Certificación:</strong> {{ $trainer->certification ?? 'No especificada' }}</p>
            <p><strong>Biografía:</strong> {{ $trainer->biography ?? 'No especificada' }}</p>

            @if($trainer->profile_pic)
                <div class="mt-4">
                    <img src="{{ Storage::url($trainer->profile_pic) }}" alt="Foto de perfil" class="w-36 h-36 object-cover rounded-full border-2 border-gray-300">
                </div>
            @endif
        </div>
    </div>

    <!-- Parques Asociados -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-orange-500 text-white px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">Parques Asociados</h2>
        </div>
        <div class="p-6">
            @if ($parks->isEmpty())
                <p class="text-gray-500">No tienes parques asociados.</p>
            @else
                <ul>
                    @foreach ($parks as $park)
                        <li class="border-b border-gray-200 py-4">
                            <strong>{{ $park->name }}</strong>
                            <p class="text-gray-700"><strong>Ubicación:</strong> {{ $park->location }}</p>
                            <p class="text-gray-700"><strong>Horario:</strong></p>

                            @if ($park->opening_hours)
                                @php
                                    $openingHours = json_decode($park->opening_hours, true);
                                @endphp
                                @if (is_array($openingHours))
                                    <ul class="list-disc ml-5">
                                        @foreach ($openingHours as $day => $hours)
                                            <li>{{ $day }}: {{ $hours }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>{{ $park->opening_hours }}</p>
                                @endif
                            @else
                                <p class="text-gray-500">No especificado</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Fotos de Entrenamientos -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-orange-500 text-white px-6 py-4">
            <h2 class="text-xl font-bold">Fotos de los Entrenamientos</h2>
        </div>
        <div class="p-6">
            @if ($trainingPhotos->isEmpty())
                <p class="text-gray-500">No hay fotos de entrenamientos aún.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($trainingPhotos as $photo)
                        <div class="bg-gray-100 p-2 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto de entrenamiento" class="w-full h-48 object-cover rounded-lg">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Mostrar Experiencias -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-orange-500 text-white px-6 py-4">
            <h2 class="text-xl font-bold">Experiencias</h2>
        </div>
        <div class="p-6">
            @if ($experiences->isEmpty())
                <p class="text-gray-500">No hay experiencias registradas.</p>
            @else
                <ul>
                    @foreach ($experiences as $experience)
                        <li class="border-b border-gray-200 py-4">
                            <strong>{{ $experience->role }}</strong>
                            <p class="text-gray-700"><strong>Empresa/Gimnasio:</strong> {{ $experience->company }}</p>
                            <p class="text-gray-700"><strong>Periodo:</strong> {{ $experience->year_start }} - 
                                @if($experience->currently_working)
                                    Actualmente
                                @else
                                    {{ $experience->year_end }}
                                @endif
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Formulario para agregar experiencias -->
    <h5 class="mt-5 text-lg font-semibold">Agregar Experiencias (Opcional)</h5>
    <form action="{{ route('trainer.storeExperience') }}" method="POST">
        @csrf
        <div id="experience-container">
            <div class="experience-item border rounded p-3 mb-3">
                <h6>Experiencia #1</h6>
                @foreach(['role', 'company', 'year_start', 'year_end'] as $field)
                    <label for="{{ $field }}-0" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}:</label>
                    <input 
                        type="text" 
                        name="experiences[0][{{ $field }}]" 
                        class="form-control @error('experiences.0.' . $field) is-invalid @enderror" 
                        value="{{ old('experiences.0.' . $field) }}">
                    @error('experiences.0.' . $field)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endforeach

                <div class="form-check mt-2">
                    <input 
                        type="hidden" 
                        name="experiences[0][currently_working]" 
                        value="0">
                    <input 
                        type="checkbox" 
                        name="experiences[0][currently_working]" 
                        class="form-check-input" 
                        id="currently-working-0" 
                        value="1">
                    <label for="currently-working-0" class="form-check-label">Actualmente trabajando aquí</label>
                </div>
            </div>
        </div>

        <button type="button" id="add-experience" class="btn btn-primary btn-sm">Agregar Otra Experiencia</button>

        <!-- Botón para enviar -->
        <button type="submit" class="btn btn-success mt-4">Guardar Experiencias</button>
    </form>
</div>

<script>
    // Add Experience functionality
    document.getElementById('add-experience').addEventListener('click', () => {
        const container = document.getElementById('experience-container');
        const index = container.children.length;
        const template = document.createElement('div');
        template.classList.add('experience-item', 'border', 'rounded', 'p-3', 'mb-3');
        template.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>Experiencia #${index + 1}</h6>
                <button type="button" class="btn btn-danger btn-sm remove-experience" data-index="${index}">Eliminar</button>
            </div>
            <label for="role-${index}" class="form-label">Rol:</label>
            <input type="text" name="experiences[${index}][role]" class="form-control">
            <label for="company-${index}" class="form-label">Empresa o Gimnasio:</label>
            <input type="text" name="experiences[${index}][company]" class="form-control">
            <label for="year-start-${index}" class="form-label">Año de Inicio:</label>
            <input type="number" name="experiences[${index}][year_start]" class="form-control">
            <label for="year-end-${index}" class="form-label">Año de Fin:</label>
            <input type="number" name="experiences[${index}][year_end]" class="form-control">
        `;
        container.appendChild(template);
        // Add delete functionality
        template.querySelector('.remove-experience').addEventListener('click', function () {
            template.remove();
        });
    });
</script>

@endsection