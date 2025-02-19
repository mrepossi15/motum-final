@extends('layouts.main')

@section('title', 'Dashboard del Entrenador')

@section('content')

<div class="flex justify-center min-h-screen bg-white text-black">
    <div class="grid grid-cols-6 w-full lg:px-6">
        <!-- Espacio vacío izquierda en pantallas grandes y medianas -->
        <div class="hidden md:block col-span-1"></div>

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
            <div class="flex justify-between items-center px-6 py-4">
                <!-- Dropdown de Parques -->
                <div class="relative">
                    <button id="parkDropdown" class="bg-orange-500 text-white px-4 py-2 rounded-sm flex items-center justify-between hover:bg-orange-600 transition">
                        <span id="dropdownText">Mis Parques</span>
                        <svg id="dropdownIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <ul id="parkDropdownMenu" class="absolute bg-white border border-gray-300 mt-2 rounded-md hidden shadow-lg z-10">
                        @foreach($parks as $park)
                            <li>
                                <a href="#" data-value="{{ $park->id }}" class="block px-4 py-2 text-black hover:bg-orange-500 hover:text-white">
                                    {{ $park->name }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="#" data-value="all" class="block px-4 py-2 text-black hover:bg-orange-500 hover:text-white">
                                Todos
                            </a>
                        </li>
                        <li><hr class="border-gray-300 my-1"></li>
                        <li>
                            <a href="{{ route('trainer.add.park') }}" class="block px-4 py-2 text-orange-500 hover:bg-orange-500 hover:text-white">
                                Agregar Parque
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Botón Agregar Entrenamiento -->
                <button id="add-training-button" class="bg-orange-500 text-white px-4 py-2 rounded-sm hover:bg-orange-600 transition">
                    <i class="bi bi-plus"></i> Agregar Entrenamiento
                </button>
            </div>

            <!-- Encabezado del Calendario -->
            <div class="text-center mb-2">
                <h2 id="month-title" class="text-2xl text-orange-500 font-semibold"></h2>
            </div>

            <!-- Navegación Semanal -->
            <div class="flex justify-between items-center px-6 mb-3">
                <button id="prev-week" class="border border-orange-500 text-orange-500 px-3 py-1 rounded-md hover:bg-orange-500 hover:text-white transition">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <span id="week-range" class="text-gray-700 font-medium"></span>
                <button id="next-week" class="border border-orange-500 text-orange-500 px-3 py-1 rounded-md hover:bg-orange-500 hover:text-white transition">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>

            <!-- Encabezado de Días -->
            <div class="grid grid-cols-7 text-center text-gray-500 font-semibold mb-3">
                <div>Lunes</div>
                <div>Martes</div>
                <div>Miércoles</div>
                <div>Jueves</div>
                <div>Viernes</div>
                <div>Sábado</div>
                <div>Domingo</div>
            </div>

            <!-- Contenedor del Calendario -->
            <div class="grid grid-cols-7 gap-2 px-6" id="calendar-container">
                <!-- Los días y entrenamientos se renderizan dinámicamente -->
            </div>

            <!-- Detalles de Entrenamientos -->
            <div class="mt-6 px-6">
                <div id="trainings-list" class=" rounded-md p-4">
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
