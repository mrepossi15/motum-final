document.addEventListener('DOMContentLoaded', function () {
    const parkDropdownMenu = document.getElementById('parkDropdownMenu');
    const parkDropdown = document.getElementById('parkDropdown');
    const dropdownIcon = document.getElementById('dropdownIcon');
    const addTrainingButtonMobile = document.getElementById('add-training-button-mobile');
    const addTrainingButtonDesktop = document.getElementById('add-training-button-desktop');
    const calendarContainer = document.getElementById('calendar-container');
    const trainingsList = document.getElementById('trainings-list');
    const monthTitle = document.getElementById('month-title');

    const state = {
        selectedParkId: 'all',
        currentWeekStart: new Date(),
        selectedDay: getDayName(new Date()),
        cache: {}
    };

    // Set the currentWeekStart to the most recent Monday based on the local timezone
    // This assumes the local timezone is America/Argentina/Buenos_Aires
    state.currentWeekStart.setDate(state.currentWeekStart.getDate() - ((state.currentWeekStart.getDay() + 6) % 7));  // Find the most recent Monday
    state.currentWeekStart.setHours(0, 0, 0, 0);  // Reset the time to midnight

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
            console.log("selectedParkId en state:", state.selectedParkId); // Verifica que se actualice correctamente
            toggleDropdown(true);
            loadWeek();
        }
    });
    document.addEventListener('click', (e) => {
        if (!parkDropdown.contains(e.target) && !parkDropdownMenu.contains(e.target)) {
            toggleDropdown(true);
        }
    });

    addTrainingButtonMobile.addEventListener('click', function () {
        console.log("selectedParkId en el botón:", state.selectedParkId); // Verifica el valor antes de redirigir
        const url = `/trainings/create?park_id=${state.selectedParkId || ''}`;
        window.location.href = url;
    });
    addTrainingButtonDesktop.addEventListener('click', function () {
        console.log("selectedParkId en el botón:", state.selectedParkId); // Verifica el valor antes de redirigir
        const url = `/trainings/create?park_id=${state.selectedParkId || ''}`;
        window.location.href = url;
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
        endOfWeek.setDate(state.currentWeekStart.getDate() + 6); // Calcular fin de la semana
    
       
        monthTitle.textContent = `${getMonthName(state.currentWeekStart.getMonth())} ${state.currentWeekStart.getFullYear()}`;
    
        const dayNames = [
            ["Lunes", "Lun", "L"], 
            ["Martes", "Mar", "M"], 
            ["Miércoles", "Mié", "M"], 
            ["Jueves", "Jue", "J"], 
            ["Viernes", "Vie", "V"], 
            ["Sábado", "Sáb", "S"], 
            ["Domingo", "Dom", "D"]
        ];
    
        // Obtener la fecha actual
        const today = new Date();
        const todayFormatted = formatDateToArg(today); // Formato de la fecha para comparación
    
        for (let i = 0; i < 7; i++) {
            const currentDate = new Date(state.currentWeekStart);
            currentDate.setDate(currentDate.getDate() + i); // Ajustar cada día basado en el inicio de la semana
    
            const [fullName, mediumName, shortName] = dayNames[i]; // Extraer nombres en distintos tamaños
    
            // Formatear la fecha en distintos formatos según el tamaño de pantalla
            const day = currentDate.getDate().toString().padStart(2, '0'); // dd
            const month = (currentDate.getMonth() + 1).toString().padStart(2, '0'); // mm
            const yearFull = currentDate.getFullYear(); // aaaa
            const yearShort = yearFull.toString().slice(-2); // aa
    
            const dateFull = `${day}/${month}/${yearFull}`; // dd/mm/aaaa
            const dateMedium = `${day}/${month}/${yearShort}`; // dd/mm/aa
            const dateShort = `${day}`; // dd
    
            const dayColumn = document.createElement('div');
            dayColumn.className = 'p-3 text-center border border-gray-800 rounded-lg day-column shadow-sm cursor-pointer transition'; 
    
            // Formatear la fecha actual para comparación con `selectedDay`
            const currentDateFormatted = formatDateToArg(currentDate);
    
            if (currentDateFormatted === todayFormatted) {
                // Si es el día actual, aplicamos el fondo y quitamos el borde
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold', 'shadow-sm', 'border-0');
                state.selectedDay = fullName; // Asegurar que el estado refleje la selección
            } else {
                dayColumn.classList.add('border-gray-800'); // Mantener el borde en los demás días
            }
    
            dayColumn.innerHTML = `
                <!-- 🖥 Versión de escritorio -->
                <div class="hidden lg:inline font-bold">${fullName}</div>
    
                <!-- 📱 Versión de tablet -->
                <div class="hidden md:inline lg:hidden font-bold">${mediumName}</div>
    
                <!-- 📱 Versión de móvil -->
                <div class="inline md:hidden font-bold">${shortName}</div>
    
                <!-- Fechas formateadas según pantalla -->
                <div class="text-sm">
                    <span class="hidden lg:inline">${dateFull}</span> <!-- 🖥 dd/mm/aaaa -->
                    <span class="hidden md:inline lg:hidden">${dateMedium}</span> <!-- 📱 dd/mm/aa -->
                    <span class="inline md:hidden">${dateShort}</span> <!-- 📱 dd -->
                </div>
            `;
    
            dayColumn.addEventListener('click', () => {
                // Remover estilos de los otros días
                document.querySelectorAll('.day-column').forEach(day => {
                    day.classList.remove('bg-orange-500', 'text-white', 'font-bold', 'border-0');
                    day.classList.add('border-gray-800'); // Vuelve a poner el borde a los demás
                });
    
                // Aplicar estilos al día seleccionado
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold', 'border-0');
                dayColumn.classList.remove('border-gray-800'); // Quita el borde del seleccionado
    
                state.selectedDay = fullName;
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
    const formatTime = (time) => {
        return new Date(`1970-01-01T${time}Z`).toLocaleTimeString('es-AR', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
    };
    
   
    

    function renderTrainings(data) {
        trainingsList.innerHTML = '';
        if (!data || data.length === 0) {
            trainingsList.innerHTML = '<p class="text-center text-gray-500">No hay entrenamientos disponibles.</p>';
            return;
        }

        const filteredTrainings = data.filter(training => {
            return training.status !== 'suspended'; // Asegúrate de que no esté suspendida
        });

        const sortedTrainings = filteredTrainings.sort((a, b) => (a.start_time || '23:59:59').localeCompare(b.start_time || '23:59:59'));

        const trainingHtml = sortedTrainings.map(training => {
            const sessions = training.sessions !== undefined ? `${training.sessions} sesiones` : 'No definido';
            const statusClass = training.status === 'cancelled' ? 'text-red-500' : 'text-green-500';
            const statusText = training.is_exception ? `<p class="${statusClass}">Estado: Modificado</p>` : '';
    
            const trainingUrl = `/entrenamientos/${training.training_id}?date=${training.date}&time=${training.start_time}`;
    
            return `
                <a href="${trainingUrl}" class="block">
                    <div class="p-4 border border-gray-200 hover:scale-105 cursor-pointer rounded-lg shadow-sm bg-white mb-4 cursor-pointer hover:shadow-md transition">
                        <h5 class="text-xl font-semibold mb-2">${training.title}</h5>
                        <p class="text-gray-700"><strong>Día:</strong> ${training.day}</p>
                      <p class="text-gray-700"><strong>Hora:</strong> ${training.start_time.slice(0, 5)} - ${training.end_time.slice(0, 5)}</p>
                        <button class="mt-2 px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                            Ver Detalle
                        </button>
                    </div>
                </a>
            `;
        }).join('');
    
        trainingsList.innerHTML = trainingHtml;

        document.querySelectorAll('.view-training').forEach(button => {
            button.addEventListener('click', () => {
                const trainingId = button.dataset.id;
                const trainingDate = button.dataset.date;
                const trainingTime = button.dataset.time; // Captura el time correctamente
        
                if (!trainingTime) {
                    console.error("⛔ Error: No se encontró el time para el entrenamiento ID:", trainingId);
                }
        
                window.location.href = `/entrenamientos/${trainingId}?date=${trainingDate}&time=${trainingTime}`;
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
    const formattedDate = formatDateToArg(state.currentWeekStart);
    
    function getMonthName(monthIndex) {
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return monthNames[monthIndex];
    }
});