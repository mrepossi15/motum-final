document.addEventListener('DOMContentLoaded', function () {
    const scheduleContainer = document.getElementById('schedule-container');
    const addScheduleButton = document.getElementById('add-schedule');
    const pricesContainer = document.getElementById('prices');
    const addPriceButton = document.getElementById('add-price-button');

    // Agregar Horario
    addScheduleButton.addEventListener('click', () => {
        const index = scheduleContainer.children.length;

        const scheduleBlock = document.createElement('div');
        scheduleBlock.classList.add('pb-4');
        scheduleBlock.innerHTML = `
        <div class="pb-4 border-t pt-4">
            <!-- Días de la semana (imitando x-form.checkbox-group) -->
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
                ${['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'].map(day => `
                    <label class="flex items-center gap-2 rounded-md hover:bg-gray-100 cursor-pointer transition">
                        <input type="checkbox" name="schedule[days][${index}][0][]" value="${day}" class="h-5 w-5 text-orange-500 focus:ring-orange-500">
                        <span class="text-black text-sm">${day}</span>
                    </label>
                `).join('')}
            </div>
    
            <!-- Horario en una sola fila -->
            <div class="grid grid-cols-2 gap-4 mt-6 border-t pt-4">
                <div class="relative">
                    <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Inicio*</label>
                    <input type="time" name="schedule[start_time][${index}]" required 
                        class="w-full bg-white text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>
    
                <div class="relative">
                    <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Fin*</label>
                    <input type="time" name="schedule[end_time][${index}]" required 
                        class="w-full bg-white text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
    
            <!-- Botón para eliminar -->
            <div class="text-end mt-2">
                <button type="button" class="text-red-500 hover:bg-gray-50 px-3 py-1 rounded remove-schedule">
                    Eliminar
                </button>
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
        const index = pricesContainer.children.length;
        
        const priceBlock = document.createElement('div');
        priceBlock.classList.add('pb-4');

        priceBlock.innerHTML = `
        <div class="border-t">
            <div class="grid grid-cols-2 gap-4">
                <div class="relative">
                    <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Veces/Semana*</label>
                    <input type="number" name="prices[weekly_sessions][]" required class="w-full bg-white text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>

                 <div class="relative">
                    <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Precio*</label>
                    <input type="number" name="prices[price][]" required class="w-full bg-white text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>

            <!-- Botón para eliminar precio -->
            <div class="text-end mt-2">
                <button type="button" class="text-red-500 hover:bg-gray-600 px-3 py-1 rounded remove-price">
                    Eliminar
                </button>
            </div>
        </div>
        `;

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