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
  <!-- Mostrar Experiencias -->
<div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
    <div class="bg-orange-500 text-white px-6 py-4 flex justify-between items-center">
        <h2 class="text-xl font-bold">Experiencias</h2>
        <button onclick="toggleAddExperience()" class="bg-white text-orange-600 px-3 py-1 text-sm rounded-md">
    ➕ Agregar
</button>
    </div>
    <div class="p-6">
        @if ($experiences->isEmpty())
            <p class="text-gray-500">No hay experiencias registradas.</p>
        @else
            <ul>
                @foreach ($experiences as $experience)
                    <li class="border-b border-gray-200 py-4 flex justify-between items-center">
                        <div>
                            <strong>{{ $experience->role }}</strong>
                            <p class="text-gray-700"><strong>Empresa/Gimnasio:</strong> {{ $experience->company ?? 'Freelance' }}</p>
                            <p class="text-gray-700"><strong>Periodo:</strong> {{ $experience->year_start }} - 
                                @if($experience->currently_working)
                                    Actualmente
                                @else
                                    {{ $experience->year_end }}
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Botón para Editar -->
                            <button onclick="toggleEditModal({{ $experience->id }})"
                                    class="text-blue-600 hover:underline">✏️ Editar</button>

                            <!-- Formulario para Eliminar -->
                            <form action="{{ route('trainer.experience.destroy', $experience->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">🗑️ Eliminar</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>


  
</div>
<!-- Modal Agregar Experiencia -->

<div id="addExperienceModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h5 class="text-lg font-semibold text-gray-800">Agregar Experiencia</h5>
        
        <form action="{{ route('trainer.storeExperience') }}" method="POST">
            @csrf

            <label class="block font-medium text-gray-700 mt-2">Rol</label>
            <input type="text" name="experiences[0][role]"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm">

            <label class="block font-medium text-gray-700 mt-2">Empresa</label>
            <input type="text" name="experiences[0][company]"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm">

            <label class="block font-medium text-gray-700 mt-2">Año de Inicio</label>
            <input type="number" name="experiences[0][year_start]"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm">

            <label class="block font-medium text-gray-700 mt-2">Año de Fin</label>
            <input type="number" name="experiences[0][year_end]"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm">
                   <div class="flex items-center mt-3">
    <!-- Campo oculto para que siempre se envíe un valor -->
    <input type="hidden" name="experiences[0][currently_working]" value="0">
    
    <input type="checkbox" id="currently_working_0" name="experiences[0][currently_working]" value="1"
           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
    <label for="currently_working_0" class="ml-2 text-gray-700">Actualmente trabajando aquí</label>
</div>

            <div class="flex justify-end space-x-4 mt-4">
                <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        onclick="toggleAddExperience()">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<!-- 🔥 Modal Editar Experiencia -->
<div id="editExperienceModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h5 class="text-lg font-semibold text-gray-800">Editar Experiencia</h5>
        
        <form id="editExperienceForm" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" id="editExperienceId">

            <label class="block font-medium text-gray-700 mt-2">Rol</label>
            <input type="text" id="editRole" name="role"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

            <label class="block font-medium text-gray-700 mt-2">Empresa</label>
            <input type="text" id="editCompany" name="company"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

            <label class="block font-medium text-gray-700 mt-2">Año de Inicio</label>
            <input type="number" id="editYearStart" name="year_start"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

            <label class="block font-medium text-gray-700 mt-2">Año de Fin</label>
            <input type="number" id="editYearEnd" name="year_end"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

            <!-- ✅ Checkbox "Actualmente Trabajando Aquí" -->
            <div class="flex items-center mt-3">
                <input type="checkbox" id="editCurrentlyWorking" name="currently_working"
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <label for="editCurrentlyWorking" class="ml-2 text-gray-700">Actualmente trabajando aquí</label>
            </div>

            <div class="flex justify-end space-x-4 mt-4">
                <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        onclick="toggleEditModal()">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
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
    function toggleAddExperience() {
    const modal = document.getElementById('addExperienceModal');
    modal.classList.toggle('hidden');
}
</script>
<script>
    function toggleYearEnd(checkbox) {
        let yearEndInput = checkbox.closest('form').querySelector(`#year_end_0`);
        
        if (checkbox.checked) {
            yearEndInput.disabled = true;
            yearEndInput.value = ''; // Vaciar el campo para evitar errores
        } else {
            yearEndInput.disabled = false;
        }
    }
</script>
<script>
    function toggleEditModal(id = null) {
        const modal = document.getElementById('editExperienceModal');
        modal.classList.toggle('hidden');

        if (id) {
            const experiences = @json(auth()->user()->experiences);
            const experience = experiences.find(exp => exp.id == id);

            if (experience) {
                document.getElementById('editExperienceId').value = experience.id;
                document.getElementById('editRole').value = experience.role;
                document.getElementById('editCompany').value = experience.company ?? '';
                document.getElementById('editYearStart').value = experience.year_start;
                document.getElementById('editYearEnd').value = experience.year_end ?? '';

                document.getElementById('editCurrentlyWorking').checked = experience.currently_working;
                document.getElementById('editYearEnd').disabled = experience.currently_working;

                document.getElementById('editExperienceForm').action = `/trainer/experience/${experience.id}`;
            }
        }
    }

    // Deshabilitar "Año de Fin" si "Actualmente Trabajando" está marcado
    document.getElementById('editCurrentlyWorking').addEventListener('change', function () {
        document.getElementById('editYearEnd').disabled = this.checked;
    });
</script>

@endsection