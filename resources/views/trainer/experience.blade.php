@extends('layouts.main')

@section('title', 'Mis Experiencias')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 relative">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Mis Experiencias</h2>

    <!-- Botón flotante en la esquina superior derecha -->
    <div class="absolute top-4 right-4">
        <button onclick="toggleModal('addExperienceModal')" class="text-orange-500 px-4 py-2 flex items-center  hover:underline transition">
            <x-lucide-plus class="w-4 h-4" />
            <span>Agregar</span>
        </button>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="fixed bottom-10 left-1/2 transform -translate-x-1/2 bg-orange-500 text-white text-center px-6 py-3 rounded-lg shadow-xl font-rubik text-lg transition-all duration-500 opacity-500"
            x-transition:leave="opacity-100"
            x-transition:leave-end="opacity-0">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-4">
        @if ($experiences->isEmpty())
            <p class="text-gray-500 text-center">No tienes experiencias registradas.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach ($experiences as $experience)
                    <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $experience->role }}</h3>
                            <p class="text-gray-600"><strong>Empresa/Gimnasio:</strong> {{ $experience->company ?? 'Freelance' }}</p>
                            <p class="text-gray-600"><strong>Periodo:</strong> {{ $experience->year_start }} - 
                                @if($experience->currently_working)
                                    <span class="text-green-500 font-semibold">Actualmente</span>
                                @else
                                    {{ $experience->year_end }}
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-2 mt-2 sm:mt-0">
                            <button onclick="loadExperience({{ $experience->id }})" class="text-orange-500 hover:underline"> Editar</button>
                            <form action="{{ route('trainer.experience.destroy', $experience->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    
    <div class="mt-6 text-center">
        <a href="{{ route('trainer.profile', ['id' => auth()->id()]) }}" class="text-orange-500 hover:underline font-semibold">
            Volver a mi perfil
        </a>
    </div>
</div>


<!-- Modal Agregar Experiencia -->
<div id="addExperienceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center md:items-center items-end z-50">
    <div id="addExperienceContent" class="bg-[#1E1E1E] p-6 rounded-t-lg md:rounded-lg w-full max-w-md md:max-w-lg shadow-lg relative transform transition-transform duration-300 ease-in-out">
        <div class="h-1 w-12 bg-gray-500 rounded-full mx-auto mb-3 md:hidden"></div> <!-- Barra de swipe en móviles -->

        <button onclick="toggleModal('addExperienceModal')" class="absolute top-3 right-3 text-white hover:text-red-500">
            <x-lucide-x class="w-6 h-6" />
        </button>

        <h5 class="text-lg text-white mb-4">Agregar Experiencia</h5>
        <form action="{{ route('trainer.experience.store') }}" method="POST">
            @csrf

            <label class="block text-white font-medium mt-2">Rol</label>
            <input type="text" name="role" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3" required>

            <label class="block text-white font-medium mt-2">Empresa</label>
            <input type="text" name="company" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3">

            <label class="block text-white font-medium mt-2">Año de Inicio</label>
            <input type="number" name="year_start" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3" required>

            <label class="block text-white font-medium mt-2">Año de Fin</label>
            <input type="number" name="year_end" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3">

            <div class="mt-6 mb-3 flex justify-center space-x-4">
                <button type="button" onclick="toggleModal('addExperienceModal')" class="bg-gray-700 text-white px-6 py-3 rounded-md w-full hover:bg-gray-600 transition">
                    Cancelar
                </button>
                <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Experiencia -->
<div id="editExperienceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center md:items-center items-end z-50">
    <div id="editExperienceContent" class="bg-[#1E1E1E] p-6 rounded-t-lg md:rounded-lg w-full max-w-md md:max-w-lg shadow-lg relative transform transition-transform duration-300 ease-in-out">
        <div class="h-1 w-12 bg-gray-500 rounded-full mx-auto mb-3 md:hidden"></div> <!-- Barra de swipe en móviles -->

        <button onclick="toggleModal('editExperienceModal')" class="absolute top-3 right-3 text-white hover:text-red-500">
            <x-lucide-x class="w-6 h-6" />
        </button>

        <h5 class="text-lg text-white mb-4">Editar Experiencia</h5>
        
        <form id="editExperienceForm" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" id="editExperienceId" name="id">

            <label class="block text-white font-medium mt-2">Rol</label>
            <input type="text" id="editRole" name="role" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3" required>

            <label class="block text-white font-medium mt-2">Empresa</label>
            <input type="text" id="editCompany" name="company" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3">

            <label class="block text-white font-medium mt-2">Año de Inicio</label>
            <input type="number" id="editYearStart" name="year_start" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3" required>

            <label class="block text-white font-medium mt-2">Año de Fin</label>
            <input type="number" id="editYearEnd" name="year_end" class="w-full bg-black text-white border border-gray-500 hover:border-orange-500 rounded-sm px-4 py-3">

            <div class="flex items-center mt-3">
                <input type="checkbox" id="editCurrentlyWorking" name="currently_working" value="1"
                    class="rounded border-gray-500 text-orange-500 shadow-sm focus:ring-orange-500">
                <label for="editCurrentlyWorking" class="ml-2 text-white">Actualmente trabajando aquí</label>
            </div>

            <div class="mt-6 mb-3 flex justify-center space-x-4">
                <button type="button" onclick="toggleModal('editExperienceModal')" class="bg-gray-700 text-white px-6 py-3 rounded-md w-full hover:bg-gray-600 transition">
                    Cancelar
                </button>
                <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script para los modales -->
<script>
    
     function toggleModal(modalId) {
        let modal = document.getElementById(modalId);
        let content = document.getElementById(modalId + 'Content');

        modal.classList.toggle('hidden');

    }
    function loadExperience(id) {
        fetch(`/trainer/experiences/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editExperienceId').value = data.id;
                document.getElementById('editRole').value = data.role;
                document.getElementById('editCompany').value = data.company ?? '';
                document.getElementById('editYearStart').value = data.year_start;
                document.getElementById('editYearEnd').value = data.year_end ?? '';
                document.getElementById('editCurrentlyWorking').checked = data.currently_working;

                document.getElementById('editExperienceForm').action = `/trainer/experiences/${data.id}`;

                toggleModal('editExperienceModal');
            });
    }
</script>

@endsection