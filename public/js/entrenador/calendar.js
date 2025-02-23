document.addEventListener('DOMContentLoaded', function () {
    const parkDropdownMenu = document.getElementById('parkDropdownMenu');
    const parkDropdown = document.getElementById('parkDropdown');
    const dropdownIcon = document.getElementById('dropdownIcon');
    const addTrainingButton = document.getElementById('add-training-button');
    const calendarContainer = document.getElementById('calendar-container');
    const trainingsList = document.getElementById('trainings-list');
    const weekRange = document.getElementById('week-range');
    const monthTitle = document.getElementById('month-title');

    const state = {
        selectedParkId: 'all',
        currentWeekStart: new Date(),
        selectedDay: getDayName(new Date()),
        cache: {} // Cache para almacenar resultados de la API
    };

    state.currentWeekStart.setDate(state.currentWeekStart.getDate() - ((state.currentWeekStart.getDay() + 6) % 7));

    // Cargar la semana inicial
    loadWeek();

    // Dropdown de parques
    parkDropdown?.addEventListener('click', () => toggleDropdown());

    parkDropdownMenu?.addEventListener('click', (event) => {
        if (event.target.tagName === 'A') {
            event.preventDefault();  // Evita el #
            const selectedValue = event.target.dataset.value;
            parkDropdown.querySelector('#dropdownText').textContent = event.target.textContent;
    
            state.selectedParkId = selectedValue;
            toggleDropdown(true);
            loadWeek();
        }
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!parkDropdown.contains(e.target) && !parkDropdownMenu.contains(e.target)) {
            toggleDropdown(true);
        }
    });

    // Botón para agregar entrenamiento
    addTrainingButton?.addEventListener('click', () => {
        window.location.href = `/entrenamientos/crear?park_id=${state.selectedParkId}`;
    });

    // Navegación semanal
    document.getElementById('prev-week')?.addEventListener('click', () => changeWeek(-7));
    document.getElementById('next-week')?.addEventListener('click', () => changeWeek(7));

    // Cambiar semana
    function changeWeek(days) {
        state.currentWeekStart.setDate(state.currentWeekStart.getDate() + days);
        loadWeek();
    }

    // Cargar la semana y entrenamientos
    function loadWeek() {
        calendarContainer.innerHTML = '';
        const endOfWeek = new Date(state.currentWeekStart);
        endOfWeek.setDate(state.currentWeekStart.getDate() + 6);

        weekRange.textContent = `${formatDateToArg(state.currentWeekStart)} - ${formatDateToArg(endOfWeek)}`;
        monthTitle.textContent = `${getMonthName(state.currentWeekStart.getMonth())} ${state.currentWeekStart.getFullYear()}`;

        const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        for (let i = 0; i < 7; i++) {
            const currentDate = new Date(state.currentWeekStart);
            currentDate.setDate(currentDate.getDate() + i);
            const dayName = dayNames[i];

            const dayColumn = document.createElement('div');
            dayColumn.className = 'p-3 text-center border rounded-lg day-column';

            if (dayName === state.selectedDay) {
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold');
            }

            dayColumn.innerHTML = `
                <div class="font-bold">${dayName}</div>
                <div class="text-sm">${formatDateToArg(currentDate)}</div>
            `;

            dayColumn.addEventListener('click', () => {
                document.querySelectorAll('.day-column').forEach(day => day.classList.remove('bg-orange-500', 'text-white', 'font-bold'));
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold');
                state.selectedDay = dayName;
                loadTrainings();
            });

            calendarContainer.appendChild(dayColumn);
        }

        loadTrainings();
    }

    // Cargar entrenamientos del día y parque seleccionados
    function loadTrainings() {
        const cacheKey = `${state.selectedDay}_${state.selectedParkId}`;
        trainingsList.innerHTML = `<p class="text-center text-gray-500">Cargando entrenamientos...</p>`;

        if (state.cache[cacheKey]) {
            renderTrainings(state.cache[cacheKey]);
            return;
        }

        let url = `/api/trainings/park?day=${state.selectedDay}`;
        if (state.selectedParkId !== 'all') {
            url += `&park_id=${state.selectedParkId}`;
        }

        fetch(url)
            .then(response => response.ok ? response.json() : Promise.reject('Error en la API'))
            .then(data => {
                state.cache[cacheKey] = data;
                renderTrainings(data);
            })
            .catch(error => {
                console.error('Error al cargar entrenamientos:', error);
                trainingsList.innerHTML = '<p class="text-center text-red-500">Error al cargar entrenamientos.</p>';
            });
    }

    // Renderizar entrenamientos
    function renderTrainings(data) {
        trainingsList.innerHTML = '';
        let hasTrainings = false;

        data.forEach(training => {
            training.schedules.forEach(schedule => {
                if (schedule.day === state.selectedDay) {
                    hasTrainings = true;
                    const trainingDiv = document.createElement('div');
                    trainingDiv.className = 'p-4 mb-3 border rounded-lg bg-white shadow-sm';

                    trainingDiv.innerHTML = `
                        <h3 class="text-lg font-semibold">${training.title}</h3>
                        <p><strong>Actividad:</strong> ${training.activity.name}</p>
                        <p><strong>Parque:</strong> ${training.park_id}</p>
                        <p><strong>Hora:</strong> ${schedule.start_time} - ${schedule.end_time}</p>
                        <p class="${training.available_spots > 0 ? 'text-green-500' : 'text-red-500'}">
                            Cupos disponibles: ${training.available_spots}
                        </p>
                        <button class="mt-2 px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 view-training"
                                data-id="${training.id}">
                            Ver Detalle
                        </button>
                    `;

                    trainingDiv.querySelector('.view-training').addEventListener('click', () => {
                        const trainingId = training.id;
                        const selectedDate = formatDateToArg(new Date(state.currentWeekStart)); // Usar la fecha seleccionada
                    
                        // Redireccionar con el ID del entrenamiento y la fecha
                        window.location.href = `/entrenamientos/${trainingId}?date=${selectedDate}`;
                    });

                    trainingsList.appendChild(trainingDiv);
                }
            });
        });

        if (!hasTrainings) {
            trainingsList.innerHTML = '<p class="text-center text-gray-500">Hoy no tienes clases.</p>';
        }
    }

    // Helpers
    function toggleDropdown(close = false) {
        if (close) {
            parkDropdownMenu.classList.add('hidden');
            dropdownIcon.classList.remove('rotate-180');
        } else {
            parkDropdownMenu.classList.toggle('hidden');
            dropdownIcon.classList.toggle('rotate-180');
        }
    }

    function getDayName(date) {
        const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        return days[date.getDay()];
    }

    function formatDateToArg(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;  // Formato correcto para MySQL
    }

    function getMonthName(monthIndex) {
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return monthNames[monthIndex];
    }
});
