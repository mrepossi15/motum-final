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
        cache: {}
    };

    state.currentWeekStart.setDate(state.currentWeekStart.getDate() - ((state.currentWeekStart.getDay() + 6) % 7));
    loadWeek();

    parkDropdown?.addEventListener('click', () => toggleDropdown());

    parkDropdownMenu?.addEventListener('click', (event) => {
        if (event.target.tagName === 'A') {
            const selectedValue = event.target.dataset.value;
            const href = event.target.getAttribute('href');

            if (href && href.includes('/entrenador/agregar-parque')) return;
            
            event.preventDefault();
            parkDropdown.querySelector('#dropdownText').textContent = event.target.textContent;
            state.selectedParkId = selectedValue;
            toggleDropdown(true);
            loadWeek();
        }
    });

    document.addEventListener('click', (e) => {
        if (!parkDropdown.contains(e.target) && !parkDropdownMenu.contains(e.target)) {
            toggleDropdown(true);
        }
    });

    addTrainingButton?.addEventListener('click', () => {
        window.location.href = `/entrenamientos/crear?park_id=${state.selectedParkId}`;
    });

    document.getElementById('prev-week')?.addEventListener('click', () => changeWeek(-7));
    document.getElementById('next-week')?.addEventListener('click', () => changeWeek(7));

    function changeWeek(days) {
        state.currentWeekStart.setDate(state.currentWeekStart.getDate() + days);
        loadWeek();
    }

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

    async function loadTrainings() {
        const cacheKey = `${state.selectedDay}_${state.selectedParkId}_${formatDateToArg(state.currentWeekStart)}`;
        trainingsList.innerHTML = `<p class="text-center text-gray-500">Cargando entrenamientos...</p>`;

        if (state.cache[cacheKey]) {
            renderTrainings(state.cache[cacheKey]);
            return;
        }

        let url = state.selectedParkId !== 'all'
            ? `/api/trainings/park?park_id=${state.selectedParkId}&selected_day=${state.selectedDay}&selected_date=${formatDateToArg(state.currentWeekStart)}`
            : `/api/trainings/week?week_start_date=${formatDateToArg(state.currentWeekStart)}&day=${state.selectedDay}`;

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`Error en la API: ${response.status}`);

            const data = await response.json();
            if (!Array.isArray(data)) throw new Error("Respuesta no válida.");

            const filteredData = data.filter(training => training.day === state.selectedDay);
            if (!filteredData.length) {
                trainingsList.innerHTML = `<p class="text-center text-gray-500">No hay entrenamientos para ${state.selectedDay}.</p>`;
                return;
            }

            state.cache[cacheKey] = filteredData;
            renderTrainings(filteredData);
        } catch (error) {
            console.error('Error al cargar entrenamientos:', error);
            trainingsList.innerHTML = `<p class="text-center text-red-500">Error: ${error.message}</p>`;
        }
    }

    function renderTrainings(data) {
        trainingsList.innerHTML = '';
        if (!data || data.length === 0) {
            trainingsList.innerHTML = '<p class="text-center text-gray-500">No hay entrenamientos disponibles.</p>';
            return;
        }

        const sortedTrainings = data.sort((a, b) => (a.start_time || '23:59:59').localeCompare(b.start_time || '23:59:59'));

        const trainingHtml = sortedTrainings.map(training => {
            const price = training.price !== undefined ? `$${training.price}` : 'No definido';
            const sessions = training.sessions !== undefined ? `${training.sessions} sesiones` : 'No definido';
            const statusClass = training.status === 'cancelled' ? 'text-red-500' : 'text-green-500';
            const statusText = training.is_exception ? 'Modificado' : 'Activo';

            return `
                <div class="p-4 mb-3 border rounded-lg bg-white shadow-sm">
                    <h3 class="text-lg font-semibold">${training.title}</h3>
                    <p><strong>Día:</strong> ${training.day} (${training.date})</p>
                    <p><strong>Hora:</strong> ${training.start_time} - ${training.end_time}</p>
                    <p><strong>Precio:</strong> ${price} por ${sessions}</p>
                    <p class="${statusClass}">Estado: ${statusText}</p>
                    <button class="mt-2 px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 view-training"
                            data-id="${training.training_id}" data-date="${training.date}">
                        Ver Detalle
                    </button>
                </div>
            `;
        }).join('');

        trainingsList.innerHTML = trainingHtml;

        document.querySelectorAll('.view-training').forEach(button => {
            button.addEventListener('click', () => {
                window.location.href = `/entrenamientos/${button.dataset.id}?date=${button.dataset.date}`;
            });
        });
    }

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
        return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
    }

    function getMonthName(monthIndex) {
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return monthNames[monthIndex];
    }
});