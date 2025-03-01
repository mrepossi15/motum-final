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
            class="absolute bottom-4 right-4 bg-white p-3 rounded-full shadow-lg transition hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 z-50">
            📍
        </button>
    </div>

    <!-- 📜 Fila 2: Lista de Parques y Filtros (60% de la pantalla) -->
    <div class="w-full h-[60vh] bg-white rounded-t-3xl flex flex-col shadow-lg">

        <!-- 🏷️ Encabezado con Botón de Filtros y Borrar Filtros -->
        <div class="flex items-center justify-between p-4 border-b">
            <h2 class="text-lg font-bold text-gray-900">Parques Cerca</h2>
            <div class="flex space-x-2">
                <button id="toggle-filters-btn" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300 transition">
                    ⚙️ Filtros
                </button>
                <button id="clear-filters-btn" class="hidden bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition">
                    ❌
                </button>
            </div>
        </div>

        <!-- 🎛️ Sección de Filtros (Inicialmente Oculta) -->
        <div id="filters-section" class="hidden flex flex-col p-4 bg-gray-100 rounded-lg space-y-3">
            
            <!-- 📍 Input de Dirección con Autocomplete -->
            <label class="block text-gray-700 font-semibold">Buscar por Dirección:</label>
            <input type="text" id="address-input" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Ingresa una dirección">

            <!-- 🎭 Selección de Actividad -->
            <label class="block text-gray-700 font-semibold">Seleccionar Actividad:</label>
            <select id="activity-select" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <option value="">Todas las actividades</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                @endforeach
            </select>

            <!-- 📏 Selección de Radio -->
            <label class="block text-gray-700 font-semibold mt-2">Definir Radio:</label>
            <input type="range" id="radius-slider" min="1" max="10" value="5">
            <p id="radius-value" class="text-center text-sm text-gray-700">5 km</p>

            <!-- 🛠️ Botón Aplicar Filtros -->
            <div class="mt-4 flex justify-end">
                <button id="apply-filters-btn" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                    ✅ Aplicar
                </button>
            </div>
        </div>

        <!-- 📋 Lista de Parques (Oculta por defecto, aparece solo si hay parques) -->
        <div id="parks-list-container" class="hidden flex-1 overflow-y-auto p-4 space-y-3">
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
    if (!radiusSlider || !radiusValue || !applyFiltersBtn || !clearFiltersBtn) {
        console.error("❌ Error: Elementos del filtro no encontrados en el DOM.");
        return;
    }

    // 📏 Evento para cambiar solo el valor visible del slider (SIN mover el mapa aún)
    radiusSlider.addEventListener("input", function () {
        radiusValue.textContent = `${this.value} km`;
    });

    // 📌 Aplicar filtros al hacer clic en "Aplicar"
    applyFiltersBtn.addEventListener("click", function () {
    searchRadius = radiusSlider.value * 1000; // Convertir km a metros
    selectedActivity = document.getElementById("activity-select").value; // 📌 Capturar la actividad seleccionada

    console.log(`✅ Aplicando filtros: Radio ${searchRadius}m, Actividad: ${selectedActivity}`);

    if (userLat && userLng) {
        console.log("📍 Moviendo mapa y ajustando zoom...");

        // 🚀 Ajustar la ubicación del mapa y hacer zoom según el radio
        setMapLocation(userLat, userLng, searchRadius, true);

        // ✅ Llamar a la API con el filtro de actividad
        fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
    } else {
        console.warn("⚠️ No se ha obtenido la ubicación del usuario todavía.");
    }

    filtersSection.classList.add("hidden"); // Ocultar filtros después de aplicar
    clearFiltersBtn.classList.remove("hidden"); // Mostrar botón de borrar filtros
});

    // 📌 Limpiar filtros y restaurar valores por defecto
    clearFiltersBtn.addEventListener("click", function () {
        radiusSlider.value = 5;
        radiusValue.textContent = "5 km";
        document.getElementById("activity-select").value = "";
        addressInput.value = "";
        searchRadius = 5000;
        selectedActivity = "";
        filtersApplied = false;

        console.log("🔄 Filtros reseteados a valores por defecto.");

        fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);

        // Ocultar botón de borrar filtros
        clearFiltersBtn.classList.add("hidden");
    });

    // 📌 Mostrar filtros y ocultar lista de parques
    toggleBtn.addEventListener("click", function () {
        filtersSection.classList.toggle("hidden");
        parksListContainer.classList.add("hidden"); // Ocultar la lista de parques mientras se filtra
    });

    // 📌 Si presiono "Recentrar", se cierra el filtro automáticamente
    recenterBtn.addEventListener("click", function () {
    console.log("📍 Recentrando ubicación...");

    filtersSection.classList.add("hidden"); // Cerrar filtros si estaban abiertos
    resetFilters(); // 🔄 Resetear filtros antes de recentrar

    // Ocultar el botón de borrar filtros
    document.getElementById("clear-filters-btn").classList.add("hidden");

    if (userLocation) {
        setMapLocation(userLocation.lat, userLocation.lng, searchRadius, true);
        fetchNearbyParks(userLocation.lat, userLocation.lng, searchRadius, selectedActivity);
    } else {
        getUserLocation(true);
    }
});
});


function resetFilters() {
    console.log("🔄 Resetear filtros a valores predeterminados...");

    document.getElementById("radius-slider").value = 5;
    document.getElementById("radius-value").textContent = "5 km";
    document.getElementById("activity-select").value = "";
    document.getElementById("address-input").value = "";

    searchRadius = 5000;
    selectedActivity = "";
    filtersApplied = false;

    // Ocultar completamente el botón de borrar filtros
    const clearFiltersBtn = document.getElementById("clear-filters-btn");
    clearFiltersBtn.classList.add("hidden");

    console.log("✅ Filtros reseteados.");
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
        parkElement.className = "flex items-center space-x-4 bg-gray-100 p-3 rounded-lg shadow-sm cursor-pointer hover:bg-gray-200 transition";

        parkElement.innerHTML = `
            <img src="${park.photo}" alt="${park.name}" class="w-16 h-16 rounded-lg object-cover">
            <div>
                <h3 class="text-sm font-semibold">${park.name}</h3>
                <p class="text-xs text-gray-500">${park.location || "Ubicación desconocida"}</p>
            </div>
        `;

        // 🎯 Evento para mover el mapa al hacer clic en un parque
        parkElement.addEventListener("click", () => {
            const lat = parseFloat(park.latitude);
            const lng = parseFloat(park.longitude);

            console.log(`📍 Seleccionando parque: ${park.name}`);

            // 📌 Centrar mapa en el parque seleccionado
            map.setCenter({ lat, lng });
            map.setZoom(16);

            // 🔥 Verificar si el parque tiene un marcador, si no lo tiene, agregarlo
            let existingMarker = markers.find(m => m.id === park.id);
            if (!existingMarker) {
                console.log(`🚀 Creando nuevo marcador para ${park.name}`);
                let newMarker = new google.maps.Marker({
                    position: { lat, lng },
                    map: map,
                    title: park.name,
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/orange-dot.png", // 🟠 Icono naranja
                        scaledSize: new google.maps.Size(40, 40),
                    }
                });

                // Agregar a la lista de marcadores
                markers.push({ id: park.id, marker: newMarker });
            }
        });

        parksList.appendChild(parkElement);
    });
}

</script>

@endsection