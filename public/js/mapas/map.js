let map, marker;
let userLat = null;
let userLng = null;
let userLocation = null;
let searchRadius = 5000;
let selectedActivity = '';
let markers = [];
let lastFetchedLocation = { lat: null, lng: null, radius: null, activityId: null };
let parksCache = {}; // 🔥 Cache para evitar llamadas repetidas
let debounceTimer;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.6037, lng: -58.3816 }, 
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

    setTimeout(() => {
        if (userLat && userLng) {
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        }
    }, 2000);
}

function getUserLocation(forceUpdate = false) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const newLat = position.coords.latitude;
                const newLng = position.coords.longitude;

                if (!forceUpdate && userLat === newLat && userLng === newLng) {
                    console.log("📍 Ubicación ya conocida, evitando llamada a la API.");
                    return;
                }

                userLat = newLat;
                userLng = newLng;
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
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;

        console.log("📍 Dirección seleccionada:", place.formatted_address);

        userLat = place.geometry.location.lat();
        userLng = place.geometry.location.lng();
    }, 500);
}

let userMarker = null;

function setMapLocation(lat, lng, radius, updateZoom = false) {
    if (!lat || !lng) {
        return;
    }

    map.setCenter({ lat, lng });

    if (updateZoom) {
        const newZoom = getZoomLevel(radius);
        map.setZoom(newZoom);
    }

    if (userMarker) {
        userMarker.setPosition({ lat, lng });
    } else {
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
    let cacheKey = `${lat},${lng},${radius},${activityId}`;
    
    // ✅ Si la consulta ya se hizo, usa la caché y evita la llamada innecesaria
    if (parksCache[cacheKey]) {
        console.log("⚡ Usando caché para evitar llamada a la API.");
        updateParksList(parksCache[cacheKey]);
        showParksOnMap(parksCache[cacheKey]);
        return;
    }

    // 🔄 Si ya se hizo esta consulta, evita repetir la llamada
    if (lastFetchedLocation.lat === lat && lastFetchedLocation.lng === lng &&
        lastFetchedLocation.radius === radius && lastFetchedLocation.activityId === activityId) {
        console.log("🔄 Misma consulta, evitando llamada a la API");
        return;
    }

    lastFetchedLocation = { lat, lng, radius, activityId };

    let url = `/api/nearby-parks?lat=${lat}&lng=${lng}&radius=${radius}`;
    if (activityId) {
        url += `&activity_id=${activityId}`;
    }

    // ✅ Mostrar mensaje de carga antes de hacer la solicitud
    updateParksList("loading");

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error("❌ No se encontraron parques.");
            return response.json();
        })
        .then(parks => {
            console.log(`✅ ${parks.length} parques encontrados.`);
            parksCache[cacheKey] = parks;

            // 🔄 No limpiar los marcadores antes de saber si hay parques
            clearMarkers();
            if (parks.length > 0) {
                showParksOnMap(parks);
                updateParksList(parks);
                setMapLocation(lat, lng, radius, true);
            } else {
                updateParksList([]); // Si no hay parques, se limpia la lista de manera controlada
            }
        })
        .catch(error => {
            console.error("❌ Error al obtener los parques:", error);
            clearMarkers();
            updateParksList([]); // Asegurar que si hay un error, la lista quede vacía
        });
}

function showParksOnMap(parks) {
    clearMarkers();

    parks.forEach(park => {
        if (!park.latitude || !park.longitude) {
            return;
        }

        let marker = new google.maps.Marker({
            position: { lat: parseFloat(park.latitude), lng: parseFloat(park.longitude) },
            map: map,
            title: park.name,
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/orange-dot.png",
                scaledSize: new google.maps.Size(40, 40),
            }
        });

        marker.addListener("click", () => {
            window.location.href = `/parques/${park.id}`;
        });

        markers.push(marker);
    });
}

function getZoomLevel(radius) {
    if (radius <= 1000) return 16;
    if (radius <= 2000) return 15;
    if (radius <= 3000) return 14;
    if (radius <= 5000) return 13;
    if (radius <= 7000) return 12;
    if (radius <= 10000) return 11;
    return 10;
}

window.initMap = initMap;