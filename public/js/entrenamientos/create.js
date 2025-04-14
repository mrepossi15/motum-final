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
        </div>
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
                    </div>

                    <div class="relative">
                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">
                            Precio *
                        </label>
                        <input type="number" name="prices[price][]" required
                            class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
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
            this.photos = [];
            this.fileList = Array.from(event.target.files);
            this.fileList.forEach((file, index) => {
                let reader = new FileReader();
                reader.onload = e => this.photos.push({ url: e.target.result, file: file });
                reader.readAsDataURL(file);
            });
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