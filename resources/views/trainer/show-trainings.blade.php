@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')

<div class="flex justify-center min-h-screen text-black  mt-10">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
    <div class="gap-2 mb-4">
        <!-- Fila superior -->
        <div class="md:flex justify-between items-center">
            <!-- CONTENEDOR para el título y botones -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-2 ">

                <!-- Primera fila: h1 + botón de filtro -->
                <div class="flex items-center justify-between w-full ">
                    <h1 class="text-2xl font-semibold text-gray-900">Mis Entrenamientos</h1>
                    <div class="flex space-x-2">
                        <button id="add-training-button-desktop"
                            class="flex max-sm:hidden items-center bg-orange-500 text-white px-3 py-2 rounded-md hover:bg-orange-600 transition h-11">
                            <x-lucide-plus class="w-5 h-5" />
                        </button>
                        <!-- Botón: Abrir modal -->
                        <button id="openParkModal"
                            class="flex items-center bg-orange-500 text-white px-3 py-2 max-sm:py-3 rounded-md hover:bg-orange-600 transition ">
                            <x-lucide-sliders-horizontal class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <!-- Segunda fila solo en mobile: Botón borrar filtro -->
                <div class="w-full  sm:hidden">
                    <button id="clearFilterBtn"
                        class="hidden flex items-center bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition space-x-2 ">
                        <span id="selectedParkName" class="text-base font-medium"></span>
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                <button id="clearFilterBtnDesktop"
                    class="hidden flex items-center justify-between bg-gray-200 text-gray-700 px-4 py-3 rounded-md hover:bg-gray-300 transition ">
                    <span id="selectedParkNameDesktop" class="text-base font-medium truncate"></span>
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
     
        
        <div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="w-16 h-16 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        

        @if ($trainings->isEmpty())
            <div class="mt-6 ">
                <div id="trainings-list" class=" ">
                    <p class="text-gray-500 italic">Aún no tienes entrenamientos asociados.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                
                @foreach ($trainings as $training)
                    <!-- 🔗 Hacer que toda la card sea clickeable -->
                    <a href="{{ route('trainings.detail', $training->id) }}" class="block training-card" data-park-id="{{ $training->park_id }}">
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105 cursor-pointer">
                            
                            <!-- 📸 Imagen del entrenamiento -->
                            @if ($training->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                    class="w-full h-48 object-cover" 
                                    alt="Foto de entrenamiento">
                            @endif

                            <!-- 📝 Detalles del entrenamiento -->
                            <div class="p-4">
                                <h5 class="text-xl font-semibold text-gray-800">{{ $training->title }}</h5>

                                <p class="text-gray-600 text-sm">
                                    <strong>Ubicación:</strong> {{ $training->park->name }} <br>
                                    <strong>Actividad:</strong> {{ $training->activity->name }} <br>
                                    <strong>Nivel:</strong> {{ ucfirst($training->level) }}
                                </p>

                                <!-- 📅 Días con clases -->
                                <div class="mt-3">
                                    <strong class="text-gray-700">Días con Clases:</strong>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach ($training->schedules as $schedule)
                                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                {{ ucfirst($schedule->day) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
<div id="parkModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-end md:items-center z-50">
    <div id="parkModalContent" class="bg-[#1E1E1E] p-6 rounded-t-lg md:rounded-lg w-full max-w-md md:max-w-lg shadow-lg relative transform translate-y-full md:translate-y-0 transition-transform duration-300 ease-in-out">
        
        <!-- Barra para swipe en mobile -->
        <div class="h-1 w-12 bg-gray-500 rounded-full mx-auto mb-3 md:hidden"></div>

        <!-- Botón cerrar -->
        <button id="closeParkModal" class="absolute top-3 right-3 text-white hover:text-red-500">
            <x-lucide-x class="w-6 h-6" />
        </button>

        <h2 class="text-lg text-white mb-4">Filtrar por Parque</h2>

        <ul class="space-y-2 ">
            @foreach($parks as $park)
            <li class="p-2 font-medium cursor-pointer border rounded-xl hover:border-orange-400 hover:bg-orange-50 bg-gray-100 text-orange-500 hover:text-orange-600">
                    <button 
                        class="w-full text-left py-2 flex items-center rounded-md transition park-option"
                        data-park-id="{{ $park->id }}"
                        data-park-name="{{ $park->name }}">
                        <x-lucide-trees class="w-5 h-5 mr-2" />
                        {{ $park->name }}
                    </button>
                </li>
            @endforeach

            <li><hr class="border-gray-600  my-4"></li>
            <li>
                <a href="{{ route('parks.create') }}" 
                class="w-full block text-center px-4 py-3 rounded-md hover:bg-orange-600 bg-orange-500 text-white border border-orange-500 transition">
                    + Agregar Parque
                </a>
            </li>
        </ul>
    </div>
</div>

@endsection
<script>
document.addEventListener('DOMContentLoaded', () => {
    const openModalBtn = document.getElementById('openParkModal');
    const closeModalBtn = document.getElementById('closeParkModal');
    const modal = document.getElementById('parkModal');
    const modalContent = document.getElementById('parkModalContent');
    const swipeBar = modalContent.querySelector('.h-1');
    const parkOptions = document.querySelectorAll('.park-option');
    const clearFilterBtn = document.getElementById('clearFilterBtn'); // Mobile
    const clearFilterBtnDesktop = document.getElementById('clearFilterBtnDesktop'); // Desktop
    const trainingCards = document.querySelectorAll('.training-card');
    const selectedParkName = document.getElementById('selectedParkName');
    const selectedParkNameDesktop = document.getElementById('selectedParkNameDesktop');

    let selectedParkId = 'all';

    openModalBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');

        if (window.innerWidth < 768) { // Mobile
            setTimeout(() => {
                modalContent.classList.remove('translate-y-full');
                modalContent.classList.add('translate-y-0');
            }, 10);
        } else {
            modalContent.classList.remove('translate-y-full');
            modalContent.classList.add('translate-y-0');
        }
    });

    closeModalBtn.addEventListener('click', closeModal);

    function closeModal() {
        if (window.innerWidth < 768) { // Mobile
            modalContent.classList.remove('translate-y-0');
            modalContent.classList.add('translate-y-full');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        } else {
            modal.classList.add('hidden');
            modalContent.classList.remove('translate-y-full');
            modalContent.classList.add('translate-y-0');
        }
    }

    parkOptions.forEach(button => {
        button.addEventListener('click', () => {
            selectedParkId = button.dataset.parkId;
            const parkName = button.dataset.parkName;

            filterTrainings(selectedParkId);
            closeModal();

            if (selectedParkId !== 'all') {
                mostrarClearFilterBtns(parkName);
            } else {
                ocultarClearFilterBtns();
            }
        });
    });

    clearFilterBtn.addEventListener('click', () => {
        limpiarFiltro();
    });

    clearFilterBtnDesktop.addEventListener('click', () => {
        limpiarFiltro();
    });

    function limpiarFiltro() {
        selectedParkId = 'all';
        filterTrainings('all');
        ocultarClearFilterBtns();
    }

    function mostrarClearFilterBtns(parkName) {
        if (window.innerWidth >= 768) {
            clearFilterBtnDesktop.classList.remove('hidden');
            clearFilterBtn.classList.add('hidden');

            selectedParkNameDesktop.textContent = parkName;
            selectedParkName.textContent = '';
        } else {
            clearFilterBtn.classList.remove('hidden');
            clearFilterBtnDesktop.classList.add('hidden');

            selectedParkName.textContent = parkName;
            selectedParkNameDesktop.textContent = '';
        }
    }

    function ocultarClearFilterBtns() {
        clearFilterBtn.classList.add('hidden');
        clearFilterBtnDesktop.classList.add('hidden');

        selectedParkName.textContent = '';
        selectedParkNameDesktop.textContent = '';
    }

    function filterTrainings(parkId) {
        trainingCards.forEach(card => {
            const cardParkId = card.dataset.parkId;
            card.style.display = (parkId === 'all' || cardParkId === parkId) ? 'block' : 'none';
        });
    }

    window.addEventListener('click', e => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Swipe para mobile
    let touchStartY = 0;
    let touchEndY = 0;

    swipeBar.addEventListener('touchstart', e => {
        touchStartY = e.touches[0].clientY;
    });

    swipeBar.addEventListener('touchmove', e => {
        touchEndY = e.touches[0].clientY;
    });

    swipeBar.addEventListener('touchend', () => {
        if (touchEndY - touchStartY > 50) {
            closeModal();
        }
    });
});
</script>