document.addEventListener('DOMContentLoaded', function () {
    const scheduleContainer = document.getElementById('schedule-container');
    const addScheduleButton = document.getElementById('add-schedule');
    const pricesContainer = document.getElementById('prices');
    const addPriceButton = document.getElementById('add-price-button');
    let priceCount = 1;
    let sessionsCount = 1;
    

    // Agregar Horario
    addScheduleButton.addEventListener('click', () => {
        const index = scheduleContainer.children.length;
        sessionsCount++;

        const scheduleBlock = document.createElement('div');
        scheduleBlock.classList.add('p-4', 'border', 'border-gray-300', 'rounded-md', 'shadow-sm', 'bg-white', 'space-y-4');
        scheduleBlock.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-sm font-medium text-gray-700">
                Horario N° ${index + 1}
            </h3>
            <button type="button" class="text-red-500 hover:underline remove-schedule">
                Eliminar
            </button>
        </div>

        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
            ${['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'].map(day => `
                <label class="flex items-center gap-2 rounded-md hover:bg-gray-100 cursor-pointer transition">
                    <input type="checkbox" name="schedule[days][${index}][]" value="${day}" class="h-5 w-5 text-orange-500 focus:ring-orange-500">
                    <span class="text-black text-sm">${day}</span>
                </label>
            `).join('')}
             <p data-error="schedule_days" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
        </div>
        


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
                <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">Inicio *</label>
                <input type="time" name="schedule[start_time][${index}]" required
                    class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
            </div>

            <div class="relative">
                <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">Fin *</label>
                <input type="time" name="schedule[end_time][${index}]" required
                    class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
            </div>
             <p data-error="schedule_time" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
        </div>
          <p data-error="schedule_general" class="text-red-500 text-sm mt-2 hidden" aria-live="assertive"></p>
    `;

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
                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">
                            Veces por semana *
                        </label>
                        <input type="number" name="prices[weekly_sessions][]" required
                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                            <p data-error="weekly_sessions" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
                            </div>

                    <div class="relative">
                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">
                            Precio *
                        </label>
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
        fileList: [],
        previewImages(event) {
            // Limpiar errores anteriores
            const errorEl = document.querySelector('[data-error="photos"]');
            if (errorEl) {
                errorEl.innerText = '';
                errorEl.classList.add('hidden');
            }

            this.photos = [];
            this.fileList = [];

            const files = Array.from(event.target.files);
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSizeInBytes = 2 * 1024 * 1024; // 2MB

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (!allowedTypes.includes(file.type)) {
                    if (errorEl) {
                        errorEl.innerText = `El archivo "${file.name}" no es válido. Usá JPG o PNG.`;
                        errorEl.classList.remove('hidden');
                    }
                    return;
                }

                if (file.size > maxSizeInBytes) {
                    const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                    if (errorEl) {
                        errorEl.innerText = `El archivo "${file.name}" pesa ${sizeInMB}MB y supera el máximo permitido (2MB).`;
                        errorEl.classList.remove('hidden');
                    }
                    return;
                }

                let reader = new FileReader();
                reader.onload = (e) => {
                    this.photos.push({ url: e.target.result, file });
                };
                reader.readAsDataURL(file);
                this.fileList.push(file);
            }

            this.updateFileInput();
        },
        removeImage(index) {
            this.photos.splice(index, 1);
            this.fileList.splice(index, 1);
            this.updateFileInput();
        },
        updateFileInput() {
            let dataTransfer = new DataTransfer();
            this.fileList.forEach(file => dataTransfer.items.add(file));
            document.getElementById('photos').files = dataTransfer.files;
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