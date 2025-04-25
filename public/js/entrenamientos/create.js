document.addEventListener('DOMContentLoaded', function () {
    const scheduleContainer = document.getElementById('schedule-container');
    const addScheduleButton = document.getElementById('add-schedule');
    const pricesContainer = document.getElementById('prices');
    const addPriceButton = document.getElementById('add-price-button');
    let priceCount = 1;
    let sessionsCount = 1;
    
    
    addScheduleButton.addEventListener('click', () => {
        const index = scheduleContainer.children.length;
    
        const scheduleBlock = document.createElement('div');
        scheduleBlock.classList.add('p-4', 'border', 'border-gray-300', 'rounded-md', 'shadow-sm', 'bg-white');
    
        const dayOptions = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'].map(day => `
            <label 
                class="cursor-pointer border rounded-lg p-2 text-center font-medium transition
                    hover:border-orange-400 hover:bg-orange-50 text-gray-700"
                onclick="
                    const checkbox = this.querySelector('input');
                    checkbox.checked = !checkbox.checked;
                    if (checkbox.checked) {
                        this.classList.add('border-orange-500', 'bg-orange-100', 'text-orange-700');
                        this.classList.remove('border-gray-300', 'text-gray-700');
                    } else {
                        this.classList.remove('border-orange-500', 'bg-orange-100', 'text-orange-700');
                        this.classList.add('border-gray-300', 'text-gray-700');
                    }
                "
            >
                ${day}
                <input 
                    type="checkbox" 
                    name="schedule[days][${index}][0][]" 
                    value="${day}" 
                    class="hidden"
                >
            </label>
        `).join('');
    
        scheduleBlock.innerHTML = `
                <div class="flex justify-between items-center ">
                    <h3 class="text-sm font-medium text-gray-700">
                        Horario N° ${index + 1}
                    </h3>
                    <button type="button" class="text-red-500 hover:underline remove-schedule">
                        Eliminar
                    </button>
                </div>
            <div class="pt-4">
                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
                    ${dayOptions}
                </div>
                <p data-error="schedule_days" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
        
    
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="relative">
                     <label class="block text-sm text-gray-700 mb-1">Inicio del entrenamiento<span class="text-red-500">*</span></label>
                    <input type="time" name="schedule[start_time][${index}]" required
                        class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>
    
                <div class="relative">
                    <label class="block text-sm text-gray-700 mb-1">Fin del entrenamiento<span class="text-red-500">*</span></label>
                    <input type="time" name="schedule[end_time][${index}]" required
                        class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
    
            <p data-error="schedule_time" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
            </div>
            <p data-error="schedule_general" class="text-red-500 text-sm mt-2 hidden" aria-live="assertive"></p>
        `;
        // Evento de eliminación
    scheduleBlock.querySelector('.remove-schedule').addEventListener('click', () => {
        scheduleBlock.remove();
        
    });
    
        scheduleContainer.appendChild(scheduleBlock);
    });
   

    // Agregar Precio
    addPriceButton.addEventListener('click', () => {
        priceCount++;

        const priceBlock = document.createElement('div');
        priceBlock.classList.add('p-4', 'border', 'border-gray-300', 'rounded-md', 'shadow-sm', 'bg-white', 'mt-4');

        priceBlock.innerHTML = `
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-medium text-gray-700">
                    Precio N° ${priceCount}
                </h3>
                <button type="button" class="text-red-500 hover:underline remove-price">
                    Eliminar
                </button>
            </div>

            <div class="pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-sm text-gray-700 mb-1">Veces por semana<span class="text-red-500">*</span></label>
                        <input type="number" name="prices[weekly_sessions][]" required
                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                            <p data-error="weekly_sessions" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
                            </div>

                    <div class="relative">
                        <label class="block text-sm text-gray-700 mb-1">Precio<span class="text-red-500">*</span></label>
                        <input type="number" name="prices[price][]" required
                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        <p data-error="price" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
                    </div>
                </div>
            </div>
        `;

        // Evento para eliminar
        priceBlock.querySelector('.remove-price').addEventListener('click', () => {
            priceBlock.remove();
        });

        pricesContainer.appendChild(priceBlock);
    });
});
function photoPreview() {
    return {
        photos: [],
        deletedPhotos: [],

        loadExistingPhotos(existingPhotos) {
            this.photos = existingPhotos;
        },

        previewImages(event) {
            const files = event.target.files;
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSizeInBytes = 2 * 1024 * 1024; // 2MB

            // 🧼 Ocultar error si se intenta volver a subir
            this.clearError();

            // Podés comentar esta línea si querés permitir múltiples cargas acumulativas
            this.photos = [];

            for (let file of files) {
                // Validar tipo
                if (!allowedTypes.includes(file.type)) {
                    this.showError(`El archivo "${file.name}" no es un tipo válido. Usá JPG o PNG.`);
                    continue;
                }

                // Validar tamaño
                if (file.size > maxSizeInBytes) {
                    const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                    this.showError(`"${file.name}" pesa ${sizeInMB}MB. El máximo permitido es 2MB.`);
                    continue;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photos.push({ id: null, url: e.target.result, file });
                };
                reader.readAsDataURL(file);
            }
        },

        removeImage(index) {
            const photo = this.photos[index];
            if (photo.id) {
                this.deletedPhotos.push(photo.id);
            }
            this.photos.splice(index, 1);
        },

        showError(message) {
            const el = document.querySelector('[data-error="photos"]');
            if (el) {
                el.innerText = message;
                el.classList.remove('hidden');
            }
        },

        clearError() {
            const el = document.querySelector('[data-error="photos"]');
            if (el) {
                el.innerText = '';
                el.classList.add('hidden');
            }
        }
    };
}

let map;
let marker;

window.initMap = function () {
    const defaultLocation = { lat: -34.6037, lng: -58.3816 }; // Buenos Aires centro
    map = new google.maps.Map(document.getElementById("map"), {
        center: defaultLocation,
        zoom: 14,
    });

    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
    });

    const initialParkId = document.querySelector('[name="park_id"]')?.value;
    if (initialParkId) {
        updateMap(initialParkId);
    }
};

window.updateMap = function (parkId) {
    const park = window.PARKS?.[parkId];
    if (park) {
        const position = { lat: parseFloat(park.lat), lng: parseFloat(park.lng) };

        map.setCenter(position);
        marker.setPosition(position);
    }
};