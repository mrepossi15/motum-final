@extends('layouts.main')

@section('title', 'Mapa de Parques')

@section('content')

@if (session('error'))
    <div class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md z-50 flex items-center space-x-2">
        <span>⚠️ {{ session('error') }}</span>
        <button type="button" onclick="this.parentElement.style.display='none';" class="text-red-500 hover:text-red-700">✖</button>
    </div>
@endif

<!-- 📌 Contenedor Principal -->
<div class="relative h-screen w-full flex flex-col">

    <!-- 🗺️ Fila 1: Mapa (40% de la pantalla) -->
    <div class="relative w-full h-[40vh]">
        <div id="map" class="absolute inset-0 w-full h-full"></div>

        <!-- 📍 Botón flotante de ubicación -->
        <button id="recenter-btn" 
            class="absolute bottom-4 right-4 bg-white p-3 rounded-full shadow-lg transition hover:bg-gray-100 focus:ring-2 focus:ring-orange-300 z-50 flex items-center justify-center">
            <x-lucide-locate class="w-6 h-6 text-orange-500" />
        </button>
    </div>

    <!-- 📜 Fila 2: Lista de Parques y Filtros (60% de la pantalla) -->
    <div class="w-full h-[60vh] bg-[#1E1E1E]  flex flex-col shadow-lg">

        <!-- 🏷️ Encabezado con Botón de Filtros y Borrar Filtros -->
        <div class="flex items-center justify-between px-4 pt-4 ">
            <h2 class="text-md text-white">Parques Cercanos</h2>
            <div class="flex space-x-2">
            <button id="toggle-filters-btn" class="p-2 hover:bg-back transition">
                    <x-lucide-sliders-horizontal class="w-6 h-6 text-orange-500" />
            </button>
            <button id="clear-filters-btn" class="hidden bg-red-600 text-white py-1 px-2 rounded-sm hover:bg-red-500 transition flex items-center">
                <x-lucide-x class="w-5 h-5" />
            </button>
            </div>
        </div>

        <!-- 🎛️ Sección de Filtros (Inicialmente Oculta) -->
        <div id="filters-section" class="hidden flex-1 overflow-y-auto px-4 pb-4 pt-2 space-y-6">
    
            <!-- 📍 Input de Dirección con Autocomplete -->
            <div class="relative">
                <label class="absolute top-0 left-3 -mt-2 bg-gray-900 px-1 text-white text-sm">Buscador</label>
                <input type="text" id="address-input" class="w-full bg-black text-white border border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Buscar">
            </div>

         <!-- 🎭 Selección de Actividad -->
            <div class="relative mb-6">
                <label class="absolute top-0 left-3 -mt-2 bg-gray-900 px-1 text-white text-sm">Actividades</label>
                <select id="activity-select" class="w-full bg-black text-white border border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="">Todas las actividades</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 📏 Selección de Radio (slider con línea completa) -->
            <div class="relative mb-10 pt-5">
                <!-- 🔹 Movemos el label un poco más arriba con `top-[-10px]` y le damos `z-10` -->
                <label class="absolute top-[-10px] px-2 text-white text-sm z-10">Definir Radio</label>
                
                <div class="flex items-center space-x-4">
                    <!-- 🔹 Slider con margen superior para evitar que el label lo pise -->
                    <input type="range" id="radius-slider" min="1" max="10" value="5" 
                        class="w-full max-w-[300px] appearance-none bg-white rounded-lg h-1 cursor-pointer">

                    <!-- 🔹 Aseguramos que "5 km" siempre esté en una línea -->
                    <p id="radius-value" class="text-white text-sm whitespace-nowrap flex-shrink-0">5 km</p>
                </div>
            </div>

            <!-- 🛠️ Botón Aplicar Filtros -->
            <div class="mt-4 flex justify-center">
                <button id="apply-filters-btn" class="bg-orange-500 text-white  text-md px-6 py-3 rounded-md w-full hover:bg-orange-400 transition">
                    Aplicar
                </button>
            </div>
            
        </div>

        <!-- 📋 Lista de Parques (Oculta por defecto, aparece solo si hay parques) -->
        <div id="parks-list-container" class="hidden flex-1 overflow-y-auto px-4 pb-4 pt-2 space-y-3">
            <div id="parks-list"></div>
        </div>

    </div>
</div>

<!-- 🛠️ Scripts -->
<script src="{{ asset('js/mapas/map.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initMap" async defer></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("🚀 DOM completamente cargado");

    // 🌍 Elementos del DOM
    const radiusSlider = document.getElementById("radius-slider");
    const radiusValue = document.getElementById("radius-value");
    const applyFiltersBtn = document.getElementById("apply-filters-btn");
    const clearFiltersBtn = document.getElementById("clear-filters-btn");
    const toggleBtn = document.getElementById("toggle-filters-btn");
    const filtersSection = document.getElementById("filters-section");
    const parksListContainer = document.getElementById("parks-list-container");
    const recenterBtn = document.getElementById("recenter-btn");
    const addressInput = document.getElementById("address-input");

    let searchRadius = 5000; // Radio inicial en metros
    let selectedActivity = "";
    let filtersApplied = false;

    // ⚠️ Verificar que los elementos existen antes de agregar eventos
    if (!radiusSlider || !radiusValue || !applyFiltersBtn || !clearFiltersBtn || 
        !toggleBtn || !filtersSection || !parksListContainer || !recenterBtn || !addressInput) {
        console.error("❌ Error: Uno o más elementos no se encontraron en el DOM.");
        return;
    }

    // 📏 Evento para cambiar solo el valor visible del slider (SIN mover el mapa aún)
    radiusSlider.addEventListener("input", function () {
        radiusValue.textContent = `${this.value} km`;
    });

    // 📌 Aplicar filtros al hacer clic en "Aplicar"
    applyFiltersBtn.addEventListener("click", function () {
        searchRadius = radiusSlider.value * 1000; // Convertir km a metros
        selectedActivity = document.getElementById("activity-select").value;

        console.log(`✅ Aplicando filtros: Radio ${searchRadius}m, Actividad: ${selectedActivity}`);

        if (userLat && userLng) {
            setMapLocation(userLat, userLng, searchRadius, true);
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        } else {
            console.warn("⚠️ No se ha obtenido la ubicación del usuario todavía.");
        }

        filtersSection.classList.add("hidden");
        clearFiltersBtn.classList.remove("hidden");
    });

    // 📌 Limpiar filtros y restaurar valores por defecto
    clearFiltersBtn.addEventListener("click", function () {
        resetFilters();
    });

    // 📌 Mostrar filtros y ocultar lista de parques
    toggleBtn.addEventListener("click", function () {
        filtersSection.classList.toggle("hidden");
        parksListContainer.classList.add("hidden"); 
    });

    // 📌 Si presiono "Recentrar", restablece la ubicación
    recenterBtn.addEventListener("click", function () {
        console.log("📍 Recentrando ubicación...");
        resetFilters();
    });

});

// 📌 Función para restablecer filtros y volver a la ubicación original
function resetFilters() {
    console.log("🔄 Restableciendo filtros y volviendo a ubicación original...");

    // 🔴 Limpiar el input de búsqueda
    document.getElementById("address-input").value = "";

    // 🔴 Restaurar valores predeterminados de los filtros
    document.getElementById("radius-slider").value = 5;
    document.getElementById("radius-value").textContent = "5 km";
    document.getElementById("activity-select").value = "";
    selectedActivity = "";
    searchRadius = 5000;

    // 🔄 Restaurar coordenadas a la ubicación inicial del usuario
    if (userLocation) {
        userLat = userLocation.lat;
        userLng = userLocation.lng;
        selectedLat = null;
        selectedLng = null;
        console.log("📍 Ubicación restaurada a:", userLat, userLng);
    } else {
        console.warn("⚠️ No se encontró la ubicación del usuario, intentando obtenerla...");
        getUserLocation(true);
        return;
    }

    // 🔄 Recentrar y volver a cargar parques
    recenterMap();

    // 🔥 Ocultar botón de borrar filtros
    document.getElementById("clear-filters-btn").classList.add("hidden");

    console.log("✅ Filtros reseteados y ubicación restaurada.");
}
// 📌 Función para recentrar el mapa a la ubicación original
function recenterMap() {
    console.log("📍 Recentrando a la ubicación original...");

    if (userLocation) {
        userLat = userLocation.lat;
        userLng = userLocation.lng;
        selectedLat = null;
        selectedLng = null;
        console.log("✅ Ubicación restaurada a:", userLat, userLng);
    } else {
        console.warn("⚠️ Ubicación no disponible, intentando obtenerla nuevamente...");
        getUserLocation(true);
        return;
    }

    setMapLocation(userLat, userLng, searchRadius, true);
    fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
}
// Función para acortar la dirección y mostrar solo las primeras dos partes
function formatAddress(address) {
    if (!address) return "Ubicación desconocida";
    
    const parts = address.split(","); // Divide la dirección en partes
    return parts.slice(0, 2).join(","); // Toma solo las primeras 2 partes
}


// 📌 Función para actualizar la lista de parques en el HTML
function updateParksList(parks) {
    const parksList = document.getElementById("parks-list");
    const parksListContainer = document.getElementById("parks-list-container");

    parksList.innerHTML = ""; // 🔄 Limpiar la lista antes de agregar nuevos datos

    if (parks.length === 0) {
        parksListContainer.classList.add("hidden"); // ❌ Ocultar la lista si no hay parques
        return;
    }

    parksListContainer.classList.remove("hidden"); // ✅ Mostrar la lista si hay parques

    parks.forEach(park => {
        let parkElement = document.createElement("div");
        parkElement.className = "flex items-center space-x-4 bg-black p-3 rounded-md shadow-sm cursor-pointer transition border border-transparent hover:border-orange-500";
        parkElement.innerHTML = `
            <img src="${park.photo}" alt="${park.name}" class="w-16 h-16 rounded-sm object-cover">
            
            <!-- Línea divisoria + Contenedor de texto -->
            <div class="flex flex-col pl-3 border-l border-gray-300 flex-grow">
                <h3 class="text-sm text-white font-semibold">${park.name}</h3>
                <p class="text-xs text-gray-500">${formatAddress(park.location) || "Ubicación desconocida"}</p>
                <p class="text-xs text-white"><strong>${park.distance_km} km</strong></p>

                <!-- 🔥 Botón Ver más (redirige al parque) -->
                <div class="flex justify-end mt-2">
                    <button class="text-orange-500 text-xs font-semibold hover:underline ver-mas-btn" data-id="${park.id}">
                        Ver más
                    </button>
                </div>
            </div>
        `;

        // ✅ Evento al botón "Ver más" para redirigir a la URL correcta
        parkElement.querySelector(".ver-mas-btn").addEventListener("click", (e) => {
            e.stopPropagation(); // 🔥 Evita que el clic seleccione el contenedor del parque
            const parkId = e.target.getAttribute("data-id"); // Obtiene el ID del parque
            window.location.href = `/parques/${parkId}`; // Redirige a la página del parque
        });

        // ✅ Evento para centrar el mapa cuando se hace clic en la lista (SIN crear marcador)
        parkElement.addEventListener("click", () => {
            const lat = parseFloat(park.latitude);
            const lng = parseFloat(park.longitude);

            console.log(`📍 Seleccionando parque desde la lista: ${park.name}`);

            // 📌 Centrar mapa en el parque seleccionado
            map.setCenter({ lat, lng });
            map.setZoom(16);
        });

        parksList.appendChild(parkElement);
    });
}

</script>

@endsection