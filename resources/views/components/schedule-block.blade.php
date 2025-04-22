<div class="schedule-block p-4 border border-gray-300 rounded-md shadow-sm bg-white space-y-4">
    <div class="flex justify-between items-center mb-2">
       
        <button type="button" class="text-red-500 hover:underline remove-schedule">Eliminar</button>
    </div>

    <x-form.checkbox-group 
        name="schedule[days][{{ $index }}]" 
        label="Días"
        :options="['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']"
        :selected="[]"
        hideLabel="true"
    />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="relative">
            <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">Inicio *</label>
            <input type="time" name="schedule[start_time][{{ $index }}]" required
                class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div class="relative">
            <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-gray-700 text-sm">Fin *</label>
            <input type="time" name="schedule[end_time][{{ $index }}]" required
                class="w-full bg-white text-black border border-gray-300 hover:border-orange-500 rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
        </div>
    </div>

    <p data-error="schedule_days" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
    <p data-error="schedule_time" class="text-red-500 text-sm mt-1 hidden" aria-live="assertive"></p>
    <p data-error="schedule_general" class="text-red-500 text-sm mt-2 hidden" aria-live="assertive"></p>
</div>