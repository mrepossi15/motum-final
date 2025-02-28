let map, marker;
let userLat = null;
let userLng = null;
let userLocation = null;
let markers = [];
let searchRadius = 5000;
let selectedActivity = '';

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.6037, lng: -58.3816 }, // ⚠️ Temporal, cambiará con la ubicación real
        zoom: getZoomLevel(searchRadius),
        gestureHandling: "auto", // Permitir zoom manual
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
            resetAutocomplete(); // 🔄 Borrar el input de autocompletado
            userLat = userLocation.lat; // 🔄 Reiniciar coordenadas a la ubicación real
            userLng = userLocation.lng;
            console.log("🌍 Coordenadas restauradas a la ubicación actual:", userLat, userLng);
            setMapLocation(userLat, userLng);
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        } else {
            getUserLocation(true);
        }
    });

    document.getElementById('radius-select').addEventListener('change', function () {
        searchRadius = parseInt(this.value);
        fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
    });

    document.getElementById('activity-select').addEventListener('change', function () {
        selectedActivity = this.value;
        fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
    });
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

    userLat = place.geometry.location.lat();
    userLng = place.geometry.location.lng();
    setMapLocation(userLat, userLng);
    fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
}

function setMapLocation(lat, lng) {
    map.setCenter({ lat, lng });
    map.setZoom(getZoomLevel(searchRadius));

    if (!marker) {
        marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: "Mi ubicación",
            icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
        });
    } else {
        marker.setPosition({ lat, lng });
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
    if (activityId) url += `&activity_id=${activityId}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error("No se encontraron parques con esta actividad.");
            }
            return response.json();
        })
        .then(parks => {
            clearMarkers();
            setMapLocation(lat, lng); // 🔹 Mantiene la ubicación del usuario siempre
            if (!Array.isArray(parks) || parks.length === 0) {
                return;
            }
            showParksOnMap(parks);
        })
        .catch(() => {
            clearMarkers();
            setMapLocation(lat, lng); // 🔹 Ajusta la ubicación aunque no haya parques
        });
}

function showParksOnMap(parks) {
    parks.forEach(park => {
        let marker = new google.maps.Marker({
            position: { lat: parseFloat(park.latitude), lng: parseFloat(park.longitude) },
            map: map,
            title: park.name,
            icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
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
    return 11;
}

window.initMap = initMap;