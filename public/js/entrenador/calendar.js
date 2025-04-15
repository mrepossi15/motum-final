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

    async function loadWeek() {
        calendarContainer.innerHTML = '';
        const endOfWeek = new Date(state.currentWeekStart);
        endOfWeek.setDate(state.currentWeekStart.getDate() + 6);
    
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
    
        // Obtener entrenamientos de toda la semana para marcar días con clases
        let weekTrainings = [];
        try {
            const url = `/api/trainings/week?week_start_date=${formatDateToArg(state.currentWeekStart)}`;
            const response = await fetch(url);
            if (response.ok) {
                weekTrainings = await response.json();
            }
        } catch (err) {
            console.error("Error obteniendo entrenamientos semanales:", err);
        }
    
        const daysWithClasses = new Set(weekTrainings.map(t => t.day));
    
        const today = new Date();
        const todayFormatted = formatDateToArg(today);
    
        for (let i = 0; i < 7; i++) {
            const currentDate = new Date(state.currentWeekStart);
            currentDate.setDate(currentDate.getDate() + i);
    
            const [fullName, mediumName, shortName] = dayNames[i];
            const currentDateFormatted = formatDateToArg(currentDate);
    
            const dayColumn = document.createElement('div');
            dayColumn.className = 'p-3 text-center border border-gray-800 rounded-lg day-column shadow-sm cursor-pointer transition';
    
            // 🌟 Si hay clases ese día, marcarlo con fondo naranja claro
            const isToday = currentDateFormatted === todayFormatted;
            const isSelected = fullName === state.selectedDay;
    
            if (isSelected) {
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold', 'border-0');
            } else if (isToday) {
                dayColumn.classList.add('bg-gray-200');
            } else if (daysWithClasses.has(fullName)) {
                dayColumn.classList.add('bg-orange-100');
            }
    
            dayColumn.innerHTML = `
                <div class="hidden lg:inline font-bold">${fullName}</div>
                <div class="hidden md:inline lg:hidden font-bold">${mediumName}</div>
                <div class="inline md:hidden font-bold">${shortName}</div>
                <div class="text-sm">
                    <span class="hidden lg:inline">${formatDateToArg(currentDate).split("-").reverse().join("/")}</span>
                    <span class="hidden md:inline lg:hidden">${formatDateToArg(currentDate).slice(8, 10)}/${formatDateToArg(currentDate).slice(5, 7)}/${formatDateToArg(currentDate).slice(2, 4)}</span>
                    <span class="inline md:hidden">${currentDate.getDate()}</span>
                </div>
            `;
    
            dayColumn.addEventListener('click', () => {
                document.querySelectorAll('.day-column').forEach((dayEl, index) => {
                    const dayName = dayNames[index][0]; // fullName del día
            
                    dayEl.classList.remove('bg-orange-500', 'text-white', 'font-bold', 'border-0');
                    dayEl.classList.add('border-gray-800');
            
                    // ✅ Restaurar bg-orange-100 si ese día tiene clases
                    if (daysWithClasses.has(dayName)) {
                        dayEl.classList.add('bg-orange-100');
                    } else {
                        dayEl.classList.remove('bg-orange-100');
                    }
                });
            
                // ✅ Aplicar estilos al día seleccionado
                dayColumn.classList.add('bg-orange-500', 'text-white', 'font-bold', 'border-0');
                dayColumn.classList.remove('bg-orange-100');
            
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
                trainingsList.innerHTML = `<div class="bg-gray-200 text-center py-20 rounded-lg">
                                            <div class="flex flex-col items-center justify-center space-y-4">
                                                <!-- Icono tipo check dentro de un recuadro -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="10" y1="14" x2="14" y2="18"/><line x1="14" y1="14" x2="10" y2="18"/></svg>
                                                <!-- Mensaje -->
                                                <p class="text-gray-600 text-sm">No hay entrenamientos para este ${state.selectedDay}</p>
                                            </div>
                                        </div>`;
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
    <div class="flex items-justify gap-4 p-4 border border-gray-200 cursor-pointer hover:scale-104 rounded-xl shadow-sm bg-white mb-4 hover:shadow-lg hover:bg-orange-50 transition">
        
        <!-- 🖼️ Imagen -->
        <div class="w-[45%] md:w-[20%] flex-shrink-0">
            <img src="${training.photo_url}" 
                alt="Foto de entrenamiento" 
                class="w-full aspect-square object-cover rounded-md" />
        </div>

        <!-- 📄 Contenido -->
        <div class="w-[60%] md:w-[80%] flex flex-col justify-between h-full">
            
            <!-- 🧩 Contenido superior -->
            <div class="space-y-2">
                <!-- 🏷️ Título -->
                <h5 class="md:text-2xl text-xl font-semibold">${training.title}</h5>

                <!-- 🕒 Día y Hora -->
                <div class="flex flex-col md:flex-row md:items-center text-gray-700 text-md gap-2">
                <!-- 🕒 Icono + Horario -->
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    <span>${training.start_time.slice(0, 5)} - ${training.end_time.slice(0, 5)}</span>
                </div>

                <!-- ⚠️ Excepción (debajo en mobile, al lado en desktop) -->
                ${training.is_exception ? `
                    <span class="bg-orange-300 text-white px-2 py-1 text-xs rounded-lg text-center inline-block">
                        Horario Modificado
                    </span>
                ` : ''}
            </div>

                <!-- 📍 Ubicación -->
                <div class="flex items-center text-gray-700 text-md gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7z"/>
                        <circle cx="12" cy="9" r="2.5"/>
                    </svg>
                    <span>${training.park_name ?? 'No especificado'}</span>
                </div>

                <!-- 👥 Cupos -->
                <div class="flex items-center text-gray-700 text-lg gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>${training.reservations_count} / ${training.available_spots ?? 'No especificados'}</span>
                </div>
            </div>

            <!-- 🔍 Footer: Botón -->
            <div class="pt-2">
                <button class="hidden md:inline-block px-4 py-2 bg-orange-500 text-white text-sm rounded hover:bg-orange-600 transition">
                    Ver Detalle
                </button>
            </div>
        </div>
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