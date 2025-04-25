@extends('layouts.main')

@section('title', 'Dashboard del Entrenador')

@section('content')

<div class="flex justify-center min-h-screen text-black  mt-10" x-data="initTabs()" x-init="init()">
@if(session('success'))
    <div 
        x-data="{ show: false }" 
        x-init="
            setTimeout(() => { show = true; }, 100); // Espera medio segundo para mostrarse
            setTimeout(() => { show = false; }, 8000); 
        "
        x-show="show" 
        class="fixed bottom-10 left-1/2 transform -translate-x-1/2 bg-black text-orange-500 text-center px-6 py-3 rounded-lg shadow-xl font-rubik text-lg z-[9999]"
        x-transition:enter="transition ease-out duration-500"
        x-transition:leave="transition ease-in duration-500"
    >
        {{ session('success') }}
    </div>
@endif
<div class="w-full max-w-7xl mx-auto p-4 lg:px-10" x-data="{ selectedTab: 'trainings' }">
    <!-- Fila con título + botones -->
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
    <!-- Botón fijo en mobile, centrado con fondo blanco y sombra -->
    <div class="sm:hidden fixed bottom-0 left-0 w-full bg-white shadow-2xl border-t p-5 z-50">
        <a href="{{ route('trainings.create') }}"
        id="add-training-button-mobile"
        class="bg-orange-500 text-white text-base px-6 py-3 rounded-md w-full hover:bg-orange-600 transition flex items-center justify-center space-x-2">
            <x-lucide-plus class="w-5 h-5" />
            <span>Agregar entrenamiento</span>
        </a>
    </div>
    
        <!-- Contenido principal -->
        <div class="col-span-6 md:col-span-4">
            <!-- Loader -->
            <div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="w-16 h-16 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        
            <!-- Navegación Semanal -->
            <div class="flex items-center justify-between my-6">
                <!-- Botón Anterior -->
                <button id="prev-week" class="border border-orange-500 text-orange-500 px-3 py-2 rounded-md hover:bg-orange-500 hover:text-white transition flex items-center">
                    <x-lucide-chevron-left class="w-5 h-5" />
                </button>

                <!-- Contenedor flexible para centrar el mes -->
                <div class="flex-1 flex justify-center">
                    <h2 id="month-title" class="text-2xl text-black font-semibold"></h2>
                </div>

                <!-- Botón Siguiente -->
                <button id="next-week" class="border border-orange-500 text-orange-500 px-3 py-2 rounded-md hover:bg-orange-500 hover:text-white transition flex items-center">
                    <x-lucide-chevron-right class="w-5 h-5" />
                </button>
            </div>

                
               

            <!-- Contenedor del Calendario -->
            <div class="grid grid-cols-7 gap-2 " id="calendar-container">
                <!-- Los días y entrenamientos se renderizan dinámicamente -->
            </div>
            

            <!-- Detalles de Entrenamientos -->
            <div class="mt-6 ">
                <div id="trainings-list" class=" ">
                    <p class="text-gray-500 italic">Selecciona un día para ver los entrenamientos.</p>
                </div>
            </div>
        </div>

        <!-- Espacio vacío derecha en pantallas grandes y medianas -->
        <div class="hidden md:block col-span-1"></div>
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

@push('scripts')
<script src="{{ asset('js/entrenador/calendar.js') }}"></script>

@endpush
@endsection
