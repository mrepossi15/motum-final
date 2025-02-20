document.addEventListener('DOMContentLoaded', function () {
    const parkDropdownMenu = document.getElementById('parkDropdownMenu');
    const parkDropdown = document.getElementById('parkDropdown');
    const dropdownIcon = document.getElementById('dropdownIcon');
    const addTrainingButton = document.getElementById('add-training-button');
    const calendarContainer = document.getElementById('calendar-container');
    const trainingsList = document.getElementById('trainings-list');
    const weekRange = document.getElementById('week-range');
    const monthTitle = document.getElementById('month-title');

    let selectedParkId = 'all';
    let currentWeekStart = new Date();
    currentWeekStart.setDate(currentWeekStart.getDate() - ((currentWeekStart.getDay() + 6) % 7));

    // Cargar la semana inicial
    loadWeek(currentWeekStart, selectedParkId);

    // Dropdown de parques
    parkDropdown?.addEventListener('click', function () {
        parkDropdownMenu.classList.toggle('hidden');
        dropdownIcon.classList.toggle('rotate-180');
    });

    parkDropdownMenu?.addEventListener('click', function (event) {
        if (event.target.tagName === 'A') {
            const selectedValue = event.target.dataset.value;
            const dropdownText = parkDropdown.querySelector('#dropdownText');
            dropdownText.textContent = event.target.textContent;

            selectedParkId = selectedValue;
            parkDropdownMenu.classList.add('hidden');
            loadWeek(currentWeekStart, selectedParkId);
        }
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (!parkDropdown.contains(e.target) && !parkDropdownMenu.contains(e.target)) {
            parkDropdownMenu.classList.add('hidden');
            dropdownIcon.classList.remove('rotate-180');
        }
    });

    // Botón para agregar entrenamiento
    addTrainingButton?.addEventListener('click', function () {
        const url = `/entrenamientos/crear?park_id=${selectedParkId}`;
        window.location.href = url;
    });

    // Navegación semanal
    document.getElementById('prev-week')?.addEventListener('click', function () {
        currentWeekStart.setDate(currentWeekStart.getDate() - 7);
        loadWeek(currentWeekStart, selectedParkId);
    });

    document.getElementById('next-week')?.addEventListener('click', function () {
        currentWeekStart.setDate(currentWeekStart.getDate() + 7);
        loadWeek(currentWeekStart, selectedParkId);
    });

    // Cargar la semana y resaltar el día actual
    function loadWeek(startDate, parkId) {
        const today = new Date();
        const todayFormatted = formatDate(today);

        calendarContainer.innerHTML = '';
        const endOfWeek = new Date(startDate);
        endOfWeek.setDate(startDate.getDate() + 6);

        // Mostrar rango semanal y mes
        weekRange.textContent = `${formatDate(startDate)} - ${formatDate(endOfWeek)}`;
        monthTitle.textContent = `${getMonthName(startDate.getMonth())} ${startDate.getFullYear()}`;

        const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        for (let i = 0; i < 7; i++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(currentDate.getDate() + i);
            const currentDateFormatted = formatDate(currentDate);

            const dayColumn = document.createElement('div');
            dayColumn.className = 'p-3 text-center border rounded-lg day-column';

            // **Resaltar el día actual**
            if (currentDateFormatted === todayFormatted) {
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold');
            }

            dayColumn.innerHTML = `
                <div class="font-bold">${dayNames[i]}</div>
                <div class="text-sm">${currentDateFormatted}</div>
            `;

            // Clic para mostrar entrenamientos
            dayColumn.addEventListener('click', () => {
                document.querySelectorAll('.day-column').forEach(day => {
                    day.classList.remove('bg-orange-500', 'text-white', 'font-bold');
                });
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold');
                loadTrainings(currentDateFormatted, parkId);
            });

            calendarContainer.appendChild(dayColumn);
        }
    }

    // Cargar entrenamientos del día seleccionado
  // Cargar entrenamientos del día seleccionado
function loadTrainings(date) {
    trainingsList.innerHTML = `<p class="text-center text-gray-500">Cargando entrenamientos para el ${date}...</p>`;
    
    fetch(`/api/trainings/week?week_start_date=${date}`)
        .then(response => {
            if (!response.ok) throw new Error('Respuesta no válida de la API');
            return response.json();
        })
        .then(data => {
            trainingsList.innerHTML = '';

            if (!data || data.length === 0) {
                trainingsList.innerHTML = '<p class="text-center text-gray-500">No hay entrenamientos para este día.</p>';
                return;
            }

            // Filtrar entrenamientos por la fecha seleccionada
            const filteredTrainings = data.filter(training => training.date === date);

            if (filteredTrainings.length === 0) {
                trainingsList.innerHTML = '<p class="text-center text-gray-500">No hay entrenamientos programados para esta fecha.</p>';
                return;
            }

            filteredTrainings.forEach(training => {
                const trainingDiv = document.createElement('div');
                trainingDiv.className = 'p-4 mb-3 border rounded-lg bg-white shadow-sm';

                trainingDiv.innerHTML = `
                    <h3 class="text-lg font-semibold">Entrenamiento ID: ${training.training_id}</h3>
                    <p class="text-sm text-gray-700"><strong>Día:</strong> ${training.day}</p>
                    <p class="text-sm text-gray-600"><strong>Hora:</strong> ${training.start_time} - ${training.end_time}</p>
                    <p class="text-sm ${training.status === 'active' ? 'text-green-500' : 'text-red-500'}">
                        Estado: ${training.status}
                    </p>
                    <button class="mt-2 px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 view-training"
                            data-id="${training.training_id}">
                        Ver Detalle
                    </button>
                `;

                // 👉 Agregar evento para redirigir a la vista trainings.show
                trainingDiv.querySelector('.view-training').addEventListener('click', function () {
                    const trainingId = this.getAttribute('data-id');
                    window.location.href = `/trainings/${trainingId}`;
                });

                trainingsList.appendChild(trainingDiv);
            });
        })
        .catch(error => {
            console.error('Error al cargar entrenamientos:', error);
            trainingsList.innerHTML = '<p class="text-center text-red-500">Error al cargar entrenamientos.</p>';
        });
}
    // Formatear fecha DD/MM/YYYY
    function formatDate(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Obtener nombre del mes
    function getMonthName(monthIndex) {
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return monthNames[monthIndex];
    }
});