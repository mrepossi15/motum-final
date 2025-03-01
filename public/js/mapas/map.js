let map, marker;
let userLat = null;
let userLng = null;
let userLocation = null;
let searchRadius = 5000;
let selectedActivity = '';
let markers = [];

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.6037, lng: -58.3816 }, // ⚠️ Temporal, cambiará con la ubicación real
        zoom: getZoomLevel(searchRadius),
        gestureHandling: "auto",
    });

    getUserLocation();

    const input = document.getElementById('address-input');
    const autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
        componentRestrictions: { country: 'AR' },
    });

    autocomplete.addListener('place_changed', () => handleAddressSelection(autocomplete));

    document.getElementById('recenter-btn').addEventListener('click', () => {
        console.log("📍 Botón 'Recentrar' presionado");
        if (userLocation) {
            resetAutocomplete();
            userLat = userLocation.lat;
            userLng = userLocation.lng;
            setMapLocation(userLat, userLng);
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        } else {
            getUserLocation(true);
        }
    });

    // 🔥 Cargar parques automáticamente al iniciar
    setTimeout(() => {
        if (userLat && userLng) {
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        } else {
            console.warn("⚠️ Esperando obtener la ubicación del usuario...");
        }
    }, 2000); // ⏳ Espera 2s para asegurar que la geolocalización se obtenga antes
}

function getUserLocation(forceUpdate = false) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                userLocation = { lat: userLat, lng: userLng };

                setMapLocation(userLat, userLng);
                fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
            },
            (error) => console.warn("❌ No se pudo obtener la ubicación:", error),
            { enableHighAccuracy: true }
        );
    } 
}

function handleAddressSelection(autocomplete) {
    const place = autocomplete.getPlace();
    if (!place.geometry || !place.geometry.location) return;

    console.log("📍 Dirección seleccionada:", place.formatted_address);

    // 🔄 Guardar la nueva ubicación en variables PERO NO MOVER el mapa aún
    userLat = place.geometry.location.lat();
    userLng = place.geometry.location.lng();

    // ⚠️ NO llamamos a setMapLocation aquí para que el usuario deba presionar "Aplicar"
}
let userMarker = null; // 🔴 Variable global para el marcador de ubicación

function setMapLocation(lat, lng, radius, updateZoom = false) {
    if (!lat || !lng) {
        console.warn("⚠️ No se pueden actualizar las coordenadas porque no están definidas.");
        return;
    }

    console.log(`📍 Moviendo mapa a: lat ${lat}, lng ${lng}`);

    map.setCenter({ lat, lng });

    // 🔥 Ajustar zoom solo si se solicita
    if (updateZoom) {
        const newZoom = getZoomLevel(radius);
        map.setZoom(newZoom);
        console.log(`🔍 Zoom ajustado a: ${newZoom}`);
    }

    // 📍 Si ya existe el marcador de usuario, solo actualiza la posición
    if (userMarker) {
        userMarker.setPosition({ lat, lng });
    } else {
        // 📍 Crear marcador para la ubicación del usuario
        userMarker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: "Tu ubicación",
            icon: {
                url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                scaledSize: new google.maps.Size(40, 40),
            }
        });
    }
}

function resetAutocomplete() {
    document.getElementById('address-input').value = '';
}

function clearMarkers() {
    markers.forEach(marker => marker.setMap(null));
    markers = [];
}
function fetchNearbyParks(lat, lng, radius, activityId = '') {
    let url = `/api/nearby-parks?lat=${lat}&lng=${lng}&radius=${radius}`;

    // ✅ Agregar el filtro de actividad SOLO si se seleccionó una
    if (activityId && activityId !== '') {
        url += `&activity_id=${activityId}`;
    }

    console.log(`📡 Fetching parques desde: ${url}`);

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error("❌ No se encontraron parques.");
            return response.json();
        })
        .then(parks => {
            console.log(`✅ ${parks.length} parques encontrados.`);

            clearMarkers(); // 🧹 Limpiar los marcadores actuales
            updateParksList(parks); // 🔄 Actualizar la lista en el frontend

            if (parks.length > 0) {
                showParksOnMap(parks); // 📍 Dibujar los parques en el mapa
                setMapLocation(lat, lng, radius, true); // Ajustar zoom solo si hay parques
            } else {
                console.warn("⚠️ No se encontraron parques con esta actividad.");
            }
        })
        .catch(error => {
            console.error("❌ Error al obtener los parques:", error);
            clearMarkers();
            updateParksList([]); // ❌ Si no hay parques, limpiar la lista
        });
}

function showParksOnMap(parks) {
    console.log(`📌 Dibujando ${parks.length} marcadores de parques en el mapa...`);

    clearMarkers(); // 🧹 Limpiar los marcadores antes de dibujar nuevos

    parks.forEach(park => {
        if (!park.latitude || !park.longitude) {
            console.warn(`⚠️ Parque sin coordenadas: ${park.name}`);
            return;
        }

        let marker = new google.maps.Marker({
            position: { lat: parseFloat(park.latitude), lng: parseFloat(park.longitude) },
            map: map,
            title: park.name,
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/orange-dot.png", // 🟠 Icono naranja
                scaledSize: new google.maps.Size(40, 40),
            }
        });

        // 🎯 Evento: Al hacer clic en el marcador, centrar el mapa y hacer zoom
        marker.addListener("click", () => {
            console.log(`📍 Clic en marcador: ${park.name}`);
            map.setCenter(marker.getPosition());
            map.setZoom(16);
        });

        // 🔥 Guardar el marcador en el array para referencia
        markers.push(marker);
    });

    console.log(`✅ ${markers.length} marcadores de parques agregados.`);
}

function getZoomLevel(radius) {
    if (radius <= 1000) return 16;
    if (radius <= 2000) return 15;
    if (radius <= 3000) return 14;
    if (radius <= 5000) return 13;
    if (radius <= 7000) return 12;
    if (radius <= 10000) return 11;
    return 10; // Si el radio es mayor, alejar más el zoom
}

window.initMap = initMap;