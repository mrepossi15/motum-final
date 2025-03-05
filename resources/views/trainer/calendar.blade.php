@extends('layouts.main')

@section('title', 'Dashboard del Entrenador')

@section('content')

<div class=" flex justify-center min-h-screen text-black bg-gray-100" x-data="initTabs()" x-init="init()">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10" x-data="{ selectedTab: 'trainings' }">
        <h2 class="text-2xl font-semibold mb-4">Mis Entrenamientos</h2>

        <!-- Contenido principal -->
        <div class="col-span-6 md:col-span-4">
            <!-- Loader -->
            <div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="w-16 h-16 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- Mensajes de Éxito/Error -->
            @if (session('success'))
                <div class="bg-orange-500 text-white text-center py-2 px-4 rounded-md mb-4 mx-6">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-600 text-white text-center py-2 px-4 rounded-md mb-4 mx-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Barra de Acciones -->
            <div class="flex justify-between items-center ">
                <!-- Dropdown de Parques -->
                <div class="relative w-full md:w-auto">
                    <!-- Botón del Dropdown -->
                    <button id="parkDropdown" class="bg-orange-500 text-white px-4 py-2 rounded-sm flex items-center justify-between hover:bg-orange-600 transition  w-auto">
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

                <!-- Botón Agregar Entrenamiento -->
                <div class="md:hidden fixed bottom-0 left-0 w-full bg-white shadow-2xl border-t p-4 z-50">
                    <button id="add-training-button-mobile" 
                        class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-600 transition flex items-center justify-center">
                        <x-lucide-plus class="w-5 h-5 mr-2" /> Agregar Entrenamiento
                    </button>
                </div>

                <!-- Botón normal para pantallas grandes -->
                <button id="add-training-button-desktop"
                    class="hidden md:flex bg-orange-500 text-white px-4 py-2 rounded-sm hover:bg-orange-600 transition flex items-center justify-center">
                    <x-lucide-plus class="w-5 h-5 mr-2" /> Agregar Entrenamiento
                </button>
                
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
