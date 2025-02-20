document.addEventListener('DOMContentLoaded', function () {
    const scheduleContainer = document.getElementById('schedule-container');
    const addScheduleButton = document.getElementById('add-schedule');
    const pricesContainer = document.getElementById('prices');
    const addPriceButton = document.getElementById('add-price-button');

    document.getElementById('schedule-container').addEventListener('change', function () {
        const maxDays = getTotalSelectedDays();
        document.querySelectorAll('input[name="prices[weekly_sessions][]"]').forEach(input => {
            input.max = maxDays;
        });
    });
    
    // Añadir bloque de horario dinámico
    addScheduleButton.addEventListener('click', () => {
        const index = scheduleContainer.children.length;
    
        const scheduleBlock = document.createElement('div');
        scheduleBlock.classList.add('border', 'rounded', 'p-3', 'mb-3', 'schedule-item');
    
        scheduleBlock.innerHTML = `
            <div class="space-y-4">
                <!-- Checkbox Group -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Días:</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        ${['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'].map(day => `
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="schedule[days][${index}][]" value="${day}" class="h-4 w-4 text-orange-500 focus:ring-orange-500">
                                <span class="ml-2">${day}</span>
                            </label>
                        `).join('')}
                    </div>
                </div>
    
                <!-- Hora de Inicio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="schedule[start_time][${index}]">Hora de Inicio *</label>
                    <input type="time" name="schedule[start_time][${index}]" class="w-full px-3 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                </div>
    
                <!-- Hora de Fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="schedule[end_time][${index}]">Hora de Fin *</label>
                    <input type="time" name="schedule[end_time][${index}]" class="w-full px-3 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                </div>
    
                <!-- Botón Eliminar -->
                <div class="text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-schedule text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded">
                        Eliminar
                    </button>
                </div>
            </div>
        `;
    
        // Eliminar bloque al hacer clic en "Eliminar"
        scheduleBlock.querySelector('.remove-schedule').addEventListener('click', () => {
            scheduleBlock.remove();
        });
    
        scheduleContainer.appendChild(scheduleBlock);
    });

    // Añadir bloque de precios dinámico
    addPriceButton.addEventListener('click', () => {
        const index = pricesContainer.children.length;
        const maxDays = getTotalSelectedDays();
    
        const priceBlock = document.createElement('div');
        priceBlock.classList.add('border', 'rounded', 'p-3', 'mb-3');
    
        priceBlock.innerHTML = `
            <div class="space-y-4">
                <!-- Veces por Semana -->
                <div>
                    <label for="prices[weekly_sessions][${index}]" class="block text-sm font-medium text-gray-700">Veces por Semana *</label>
                    <input type="number" name="prices[weekly_sessions][]" min="1" max="${maxDays}" placeholder="Ej: 2" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                </div>
    
                <!-- Precio -->
                <div>
                    <label for="prices[price][${index}]" class="block text-sm font-medium text-gray-700">Precio *</label>
                    <input type="number" name="prices[price][]" step="0.01" placeholder="Ej: 500.00" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                </div>
    
                <!-- Botón Eliminar -->
                <div class="text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-price text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded">
                        Eliminar
                    </button>
                </div>
            </div>
        `;
    
        pricesContainer.appendChild(priceBlock);
    
        // Eliminar bloque al hacer clic en "Eliminar"
        priceBlock.querySelector('.remove-price').addEventListener('click', () => {
            priceBlock.remove();
        });
    });

    // Validar antes de enviar el formulario
    document.querySelector('form').addEventListener('submit', (event) => {
        const weeklySessionsInputs = document.querySelectorAll('input[name^="prices[weekly_sessions]"]');
        const weeklySessionsValues = Array.from(weeklySessionsInputs).map(input => input.value);
        const uniqueValues = new Set(weeklySessionsValues);

        if (weeklySessionsValues.length !== uniqueValues.size) {
            event.preventDefault();
            alert("No puedes agregar más de un precio con la misma cantidad de sesiones por semana.");
        }
    });

    // Obtener total de días seleccionados
    function getTotalSelectedDays() {
        return document.querySelectorAll('input[name^="schedule[days]"]:checked').length;
    }
});