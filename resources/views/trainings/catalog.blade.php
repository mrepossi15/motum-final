@extends('layouts.main')

@section('title', "Entrenamientos de {$activity->name} en {$park->name}")

@section('content')
@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" aria-label="Close">✖</button>
    </div>
@endif

<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto lg:px-10 mb-20">
        <!-- Contenedor Principal (GRID con 1 Col y 3 Col) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 w-100">
            
            <!-- 📌 Columna 1 (25% de ancho en desktop, 100% en mobile) -->
            <aside class="col-span-1 border-r p-4 hidden md:block w-full">
                <h1 class="text-xl font-semibold text-gray-900 mb-1">
                    {{ $activity->name }} en {{ $park->name }}
                </h1>
                <p class="hidden md:block text-sm text-gray-700 mb-4">{{ $trainings->count() }} entrenamiento/s</p>
                
                <!-- 🎛️ Filtros -->
                <div>
                    <h2 class="font-semibold text-gray-700">Filtros</h2>

                    <!-- 📅 Filtrar por Día -->
                    <div class="mt-3 mb-1 border-b">
                        <strong class="text-black flex items-center">Día:</strong>
                        @foreach($daysOfWeek as $day)
                            <div class="flex items-center mt-1 mb-2">
                                <input class="mr-2 form-checkbox text-orange-500" type="checkbox" name="day[]" 
                                    value="{{ $day }}" id="day-{{ $day }}" {{ in_array($day, $selectedDays ?? []) ? 'checked' : '' }}>
                                <label for="day-{{ $day }}" class="text-black">{{ $day }}</label>
                            </div>
                        @endforeach
                    </div>

                    <!-- ⏰ Filtrar por Hora -->
                    <div class="mt-3 mb-1 border-b">
                        <strong class="text-black">Hora:</strong>
                        @for ($i = 6; $i <= 22; $i++)
                            @php $hourFormatted = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                            <div class="flex items-center mt-1 mb-2">
                                <input class="mr-2 form-checkbox text-orange-500" type="checkbox" name="start_time[]" 
                                    value="{{ $hourFormatted }}" id="hour-{{ $hourFormatted }}" 
                                    {{ in_array($hourFormatted, $selectedHours ?? []) ? 'checked' : '' }}>
                                <label for="hour-{{ $hourFormatted }}" class="text-black">{{ $hourFormatted }}</label>
                            </div>
                        @endfor
                    </div>

                    <!-- 🎚️ Filtrar por Nivel -->
                    <div class="mt-3 mb-1">
                        <strong class="text-black">Nivel:</strong>
                        @foreach($levels as $level)
                            <div class="flex items-center mt-1 mb-2">
                                <input class="mr-2 form-checkbox text-black" type="checkbox" name="level[]" 
                                    value="{{ $level }}" id="level-{{ $level }}" 
                                    {{ in_array($level, $selectedLevels ?? []) ? 'checked' : '' }}>
                                <label for="level-{{ $level }}" class="text-black">{{ ucfirst($level) }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

<!-- 📌 Mobile: Nombre del parque y botón de filtros -->
            <div class="md:hidden flex justify-between items-center pt-3 pb-2 w-full border-b bg-gray-100">
                <h1 class="text-lg font-semibold text-gray-900">{{ $activity->name }} en {{ $park->name }}</h1>

                <div class="flex">
                    <!-- Botón para abrir filtros -->
                    <button id="open-filters-btn" class="p-2 bg-white shadow-sm hover:bg-gray-200 transition rounded-sm">
                        <x-lucide-sliders-horizontal class="w-6 h-6 text-orange-500" />
                    </button>

                    <!-- Botón para limpiar filtros -->
                    <button id="clear-filters-btn" class="hidden bg-red-600 text-white py-1 px-2 rounded-sm ml-2 hover:bg-red-500 transition flex items-center">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- 📌 Modal de Filtros en Mobile -->

            <div id="filters-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
                <div id="filters-content" class="bg-[#1E1E1E] p-6 rounded-lg w-full max-w-md md:max-w-lg h-[90vh] md:h-auto shadow-lg relative overflow-y-auto">
                    

                    <!-- ❌ Botón para cerrar -->
                    <button id="close-filters-btn" class="absolute top-4 right-4 text-white hover:text-red-500">
                        <x-lucide-x class="w-6 h-6" />
                    </button>

                    <!-- 🏷️ Título -->
                    <h2 class="text-lg text-white font-semibold mb-6 ">Filtros</h2>

                    <!-- 📅 Filtrar por Día -->
                    <div class="mb-6">
                        <label class="block font-semibold text-white mb-2">Día</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2"> 
                            @foreach($daysOfWeek as $day)
                                <label class="flex items-center space-x-2 bg-black text-white px-4 py-2 rounded-md cursor-pointer w-full">
                                    <input type="checkbox" name="day[]" value="{{ $day }}" class="form-checkbox text-orange-500 day-filter">
                                    <span>{{ ucfirst($day) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- ⏰ Filtrar por Hora -->
                    <div class="mb-6">
                        <label class="block font-semibold text-white mb-2">Hora</label>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                            @for ($i = 6; $i <= 22; $i++)
                                @php $hourFormatted = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                                <label class="flex items-center space-x-2 bg-black text-white px-4 py-2 rounded-md cursor-pointer w-full">
                                    <input type="checkbox" name="start_time[]" value="{{ $hourFormatted }}" class="form-checkbox text-orange-500 hour-filter">
                                    <span>{{ $hourFormatted }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- 🎚️ Filtrar por Nivel -->
                    <div class="mb-6">
                        <label class="block font-semibold text-white mb-2">Nivel</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($levels as $level)
                                <label class="flex items-center space-x-2 bg-black text-white px-4 py-2 rounded-md cursor-pointer w-full">
                                    <input type="checkbox" name="level[]" value="{{ $level }}" class="form-checkbox text-orange-500 level-filter">
                                    <span>{{ ucfirst($level) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 🟠 Botón Aplicar -->
                    <div class="flex justify-center mb-4">
                        <button id="apply-filters-btn" class="bg-orange-500 text-white text-md font-semibold px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                            Aplicar
                        </button>
                    </div>
                </div>
            </div>


            <!-- 📋 Contenedor Principal (75% de ancho en desktop, 100% en mobile) -->
            <div class="col-span-3 pt-4 w-full mb-20">
                <!-- Grid con Contenido -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                        @foreach($trainings as $training)
                        <a href="{{ route('trainings.selected', $training->id) }}" 
                            class="group block bg-white shadow-md rounded-lg  overflow-hidden border w-full  mx-auto h-auto 
                                flex flex-col lg:flex-col md:flex-row md:w-full">
                            
                            <!-- 📷 Imagen -->
                            <div class="relative w-full h-56 md:w-3/5 lg:w-full order-first">
                                <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                    alt="Imagen de {{ $training->title }}"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                
                                <!-- ❤️ Favoritos -->
                                <button id="favorite-btn-{{ $training->id }}" 
                                    class="absolute top-4 right-4 p-2 rounded-full bg-white shadow-md opacity-0 group-hover:opacity-100 transition"
                                    data-id="{{ $training->id }}" 
                                    data-type="training"
                                    data-favorite="{{ in_array($training->id, $favorites) ? 'true' : 'false' }}">
                                    <x-lucide-heart :class="in_array($training->id, $favorites) ? 'w-6 h-6 text-orange-500 fill-current' : 'w-6 h-6 text-orange-500 stroke-current'" 
                                        id="favorite-icon-{{ $training->id }}" />
                                </button>
                            </div>

                            <!-- 📄 Contenido -->
                            <div class="p-4 flex flex-col md:w-2/5 lg:w-full">
                                <div class="flex flex-row md:flex-col items-start gap-x-3">
                                    <!-- 🏷️ Título del entrenamiento -->
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $training->title }}</h4>

                                    <!-- ⭐ Reviews -->
                                    <div class="flex mt-1 sm:mt-0 items-center md:items-start">
                                        <div class="flex">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <x-lucide-star class="w-4 h-4 {{ $i <= (rand(35, 50) / 10) ? 'text-orange-500 fill-current' : 'text-gray-300' }}" />
                                            @endfor
                                        </div>
                                        <span class="text-gray-600 text-sm font-semibold ml-2">{{ number_format(rand(35, 50) / 10, 1) }}</span>
                                    </div>
                                </div>

                                <!-- 📅 Días -->
                                <div class="mt-2 flex flex-wrap items-center">
                                    @foreach($training->schedules->pluck('day')->unique()->sort()->toArray() as $day)
                                        <span class="bg-gray-200 text-gray-800 px-2 py-1 text-xs rounded-md mr-1">
                                            {{ ucfirst($day) }}
                                        </span>
                                    @endforeach
                                </div>

                                <!-- 🎚️ Nivel -->
                                <div class="mt-2">
                                    <span class="bg-orange-500 text-white px-2 py-1 text-xs rounded">
                                        {{ ucfirst($training->level) }}
                                    </span>
                                </div>

                                <!-- 💰 Precio -->
                                <div class="mt-4 lg:mt-8">
                                    <p class="text-orange-600 font-bold text-lg">
                                        ${{ number_format($training->prices->avg('price'), 2) }} <span class="text-gray-500 text-sm font-medium">(Promedio)</span>
                                    </p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                </div>
            </div>

        </div>
        
        <div class="mt-6 flex justify-center">
    {{ $trainings->links('pagination::tailwind') }}
</div>
    </div>
</div>


<!-- Botón para volver -->


<!-- Script para Filtrar -->
<script>
    
document.addEventListener("DOMContentLoaded", function () {
    const openFiltersBtn = document.getElementById("open-filters-btn");
    const closeFiltersBtn = document.getElementById("close-filters-btn");
    const applyFiltersBtn = document.getElementById("apply-filters-btn");
    const filterModal = document.getElementById("filters-modal");
    const filterContent = document.getElementById("filters-content");
    const clearFiltersBtn = document.getElementById("clear-filters-btn");

    // 🌍 Función para detectar si es Mobile o Escritorio
    function isMobileView() {
        return window.innerWidth < 768; // Menor a 768px = Mobile
    }

    // 📌 Si es escritorio (md: en adelante), los filtros se aplican al hacer clic en un checkbox
    if (!isMobileView()) {
        document.querySelectorAll('.form-checkbox').forEach(el => {
            el.addEventListener('change', applyFilters);
        });
    }

    // 📌 En mobile, los filtros solo se aplican cuando se presiona "Aplicar"
    applyFiltersBtn.addEventListener("click", function () {
        applyFilters();
    });

    // 📌 Abrir modal
    openFiltersBtn.addEventListener("click", function () {
        filterModal.classList.remove("hidden");
        document.body.classList.add("overflow-hidden"); // Evita que el fondo se desplace
    });

    // 📌 Cerrar modal con botón
    closeFiltersBtn.addEventListener("click", function () {
        filterModal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    });

    // 📌 Cerrar modal con "Esc"
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            filterModal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
        }
    });

    // 📌 Mostrar botón "Clear Filters" solo si hay filtros activos
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("day") || urlParams.has("start_time") || urlParams.has("level")) {
        clearFiltersBtn.classList.remove("hidden");
        clearFiltersBtn.addEventListener("click", function () {
            window.location.href = window.location.pathname;
        });
    }
});

// ✅ APLICAR FILTROS SOLO CUANDO SE DEBE
function applyFilters() {
    const selectedDays = Array.from(document.querySelectorAll("input[name='day[]']:checked")).map(input => input.value);
    const selectedHours = Array.from(document.querySelectorAll("input[name='start_time[]']:checked")).map(input => input.value);
    const selectedLevels = Array.from(document.querySelectorAll("input[name='level[]']:checked")).map(input => input.value);

    let url = new URL(window.location.href);

    if (selectedDays.length) {
        url.searchParams.set("day", selectedDays.join(","));
    } else {
        url.searchParams.delete("day");
    }

    if (selectedHours.length) {
        url.searchParams.set("start_time", selectedHours.join(","));
    } else {
        url.searchParams.delete("start_time");
    }

    if (selectedLevels.length) {
        url.searchParams.set("level", selectedLevels.join(","));
    } else {
        url.searchParams.delete("level");
    }

    // Aplicar los filtros solo después de confirmar
    window.location.href = url.toString();
}
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[id^='favorite-btn-']").forEach(button => {
        let icon = button.querySelector("svg");
        
        button.addEventListener("click", async function(event) {
            event.preventDefault();
            if (button.dataset.processing === "true") return;
            button.dataset.processing = "true";

            let favoritableId = button.dataset.id;
            let favoritableType = button.dataset.type;
            let isCurrentlyFavorite = button.dataset.favorite === "true";

            // Cambiar visualmente el estado del botón
            button.classList.toggle("bg-black", !isCurrentlyFavorite);
            button.classList.toggle("text-orange-500", !isCurrentlyFavorite);
            button.classList.toggle("border-black", isCurrentlyFavorite);
            button.classList.toggle("text-black", isCurrentlyFavorite);
            icon.classList.toggle("fill-current", !isCurrentlyFavorite);
            icon.classList.toggle("stroke-current", isCurrentlyFavorite);
            button.dataset.favorite = isCurrentlyFavorite ? "false" : "true";

            try {
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
                if (!csrfToken) throw new Error("No se encontró el token CSRF en el HTML.");

                let response = await fetch("/favorites/toggle", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ favoritable_id: favoritableId, favoritable_type: favoritableType }),
                });

                if (!response.ok) throw new Error("Error en la respuesta del servidor");

                let data = await response.json();
                console.log("✅ Respuesta del servidor:", data);
            } catch (error) {
                console.error("❌ Error en la solicitud:", error);
                alert("Hubo un error al procesar la solicitud.");
                button.dataset.favorite = isCurrentlyFavorite ? "true" : "false"; // Restaurar estado en caso de error
            } finally {
                button.dataset.processing = "false";
            }
        });
    });
});
</script>
@endsection
