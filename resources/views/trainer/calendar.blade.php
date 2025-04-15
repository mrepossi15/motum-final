@extends('layouts.main')

@section('title', 'Dashboard del Entrenador')

@section('content')

<div class="flex justify-center min-h-screen text-black bg-gray-100 mt-10" x-data="initTabs()" x-init="init()">
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
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Mis Entrenamientos</h1>

        <!-- Botón Agregar Entrenamiento -->
        <div class="flex items-center space-x-2">
            <!-- ✅ Solo ícono en móvil -->
            <a href="{{ route('trainings.create') }}"
               id="add-training-button-mobile"
               class="md:hidden bg-orange-500 text-white p-2 rounded-md hover:bg-orange-600 transition">
                <x-lucide-pencil class="w-5 h-5" />
                <span class="sr-only">Agregar entrenamiento</span>
            </a>

            <!-- ✅ Ícono + texto en desktop -->
            <button id="add-training-button-desktop"
                    class="hidden md:flex bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 transition items-center">
                <x-lucide-plus class="w-5 h-5 mr-2" />
                Agregar Entrenamiento
            </button>
        </div>
    </div>


        <!-- Contenido principal -->
        <div class="col-span-6 md:col-span-4">
            <!-- Loader -->
            <div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="w-16 h-16 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- Barra de Acciones -->
            <div class="flex justify-between items-center ">
                <!-- Dropdown de Parques -->
                <div class="relative w-full md:w-auto">
                    <!-- Botón del Dropdown -->
                    <button id="parkDropdown" class=" bg-orange-500  p-2 rounded-md hover:bg-orange-600 transition  text-white px-4 py-2 flex items-center justify-between  w-auto">
                        <span id="dropdownText">Mis Parques</span>
                        <svg id="dropdownIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Menú del Dropdown -->
                    <ul id="parkDropdownMenu" class="absolute bg-white border border-gray-300 mt-2 rounded-sm hidden shadow-lg z-10  min-w-max w-auto">
                        @foreach($parks as $park)
                            <li>
                                <a href="#" data-value="{{ $park->id }}" class="block px-4 py-2 text-black hover:bg-orange-500 hover:text-white whitespace-nowrap">
                                    {{ $park->name }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="#" data-value="all" class="block px-4 py-2 text-black hover:bg-orange-500 hover:text-white whitespace-nowrap">
                                Todos
                            </a>
                        </li>
                        <li><hr class="border-gray-300 my-1"></li>
                        <li>
                            <a href="{{ route('parks.create') }}" class="block px-4 py-2 text-orange-500 hover:bg-orange-500 hover:text-white whitespace-nowrap">
                                Agregar Parque
                            </a>
                        </li>
                    </ul>
                </div>

    
                
            </div>

            <!-- Encabezado del Calendario -->
           

            <!-- Navegación Semanal -->
            <div class="flex items-center justify-between my-6">
                <!-- Botón Anterior -->
                <button id="prev-week" class="border border-orange-500 text-orange-500 px-3 py-2 rounded-md hover:bg-orange-500 hover:text-white transition flex items-center">
                    <x-lucide-chevron-left class="w-5 h-5" />
                </button>

                <!-- Contenedor flexible para centrar el mes -->
                <div class="flex-1 flex justify-center">
                    <h2 id="month-title" class="text-2xl text-orange-500 font-semibold"></h2>
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


@push('scripts')
<script src="{{ asset('js/entrenador/calendar.js') }}"></script>

@endpush
@endsection
