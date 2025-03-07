@extends('layouts.main')

@section('title', 'Editar Entrenamiento')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 relative">
<a href="{{ route('trainings.detail', ['id' => $training->id]) }}" 
   class="text-orange-500 font-medium">
    &lt; Volver a entrenamiento
</a>
    <h2 class="text-2xl font-semibold mb-4 mt-6">Editar entrenamiento</h2>
       
    <div class="bg-white  pt-6 rounded-lg shadow-md p-4">
        <form action="{{ route('trainings.updateAll', $training->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-6 pt-2">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-lg font-semibold text-gray-700">Datos básicos del entrenamiento</h5>
                </div>
                <div class="w-full">
                    <x-form.input name="title" label="Título" :value="old('title', $training->title)" required />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                    <x-form.select name="park_id" label="Parque *" :options="$parks->pluck('name', 'id')" :selected="old('park_id', $training->park_id)" />
                    <x-form.select name="activity_id" label="Tipo de Actividad *" :options="$activities->pluck('name', 'id')" :selected="old('activity_id', $training->activity_id)" />   
                </div>
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-lg font-semibold text-gray-700">Información adicional</h5>
                </div>
                <div class="w-full">
                    <x-form.textarea name="description" label="Descripción" :value="old('description', $training->description)" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 md:mt-4  ">
                    <div class="md:col-span-3 mb-6">
                        <x-form.radio-group name="level" label="Nivel *" :options="['Principiante' => 'Principiante', 'Intermedio' => 'Intermedio', 'Avanzado' => 'Avanzado']" :checked="old('level', $training->level)" />
                    </div>
                    <div>
                        <x-form.input name="available_spots" type="number" label="Cupos Disponibles *" :value="old('available_spots', $training->available_spots)" required />
                    </div>
                </div>
        
                <div class="">
                    <div class="flex justify-between items-center ">
                        <h5 class="text-lg font-semibold text-gray-700">Días y Horarios</h5>
                    </div>
                    <div class="">
                        @forelse ($filteredSchedules as $index => $schedule)
                            <div class=" pb-4  border-b mb-4">
                                <label class="hidden">Días</label>
                                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4 p-2">
                                    @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $day)
                                        <label class="flex items-center gap-2 rounded-md hover:bg-gray-100 cursor-pointer transition px-2 py-1">
                                            <input type="checkbox" name="schedule[days][{{ $index }}][]" value="{{ $day }}"
                                                class=" bg-gray-50 h-5 w-5 text-orange-500 focus:ring-orange-500"
                                                {{ in_array($day, old("schedule.days.$index", is_array($schedule->day) ? $schedule->day : [$schedule->day])) ? 'checked' : '' }}>
                                            <span class="text-black">{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="relative">
                                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Hora de Inicio</label>
                                        <input type="time" name="schedule[start_time][{{ $index }}]" value="{{ old("schedule.start_time.$index", $schedule->start_time) }}"
                                            class=" bg-gray-50 w-full text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <div class="relative">
                                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Hora de Fin</label>
                                        <input type="time" name="schedule[end_time][{{ $index }}]" value="{{ old("schedule.end_time.$index", $schedule->end_time) }}"
                                            class="w-full bg-gray-50 text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No hay horarios disponibles para editar.</p>
                        @endforelse
                    </div>
                </div>
                <div class="">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="text-lg font-semibold text-gray-700">Precios por Sesiones Semanales</h5>
                    </div>
                    <div>
                        @foreach ($training->prices as $index => $price)
                            <div class="mb-2">
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="relative">
                                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Sesiones por Semana</label>
                                        <input type="number" name="prices[weekly_sessions][{{ $index }}]" value="{{ $price->weekly_sessions }}"
                                            class="w-full bg-gray-50 text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <div class="relative">
                                        <label class="absolute top-0 left-3 -mt-2 bg-white px-1 text-black text-sm">Precio</label>
                                        <input type="number" name="prices[price][{{ $index }}]" value="{{ $price->price }}" step="0.01"
                                            class="w-full bg-gray-50 text-black border hover:border-orange-500 border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Imágenes -->
                <div  x-data="photoPreview()" x-init="loadExistingPhotos({{ json_encode($training->photos->map(fn($photo) => ['id' => $photo->id, 'url' => asset('storage/' . $photo->photo_path)])) }})">
                    <label class="block font-medium text-gray-700">Fotos del Entrenamiento *</label>
                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple @change="previewImages(event)"
                        class="w-full bg-gray-50 text-black border border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                    
                    <!-- Input oculto para almacenar IDs de fotos eliminadas -->
                    <template x-for="photo in deletedPhotos">
                        <input type="hidden" name="deleted_photos[]" :value="photo">
                    </template>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                        <template x-for="(photo, index) in photos" :key="photo.id || index">
                            <div class="relative w-full h-24 overflow-hidden rounded-sm shadow-sm">
                                <img :src="photo.url" class="w-full h-full object-cover">
                                <button type="button" @click="removeImage(index)" class="absolute top-1 right-1 w-6 h-6 flex items-center justify-center">
                                    <x-lucide-square-x class="h-4 w-4 text-red-500" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="submit" class="bg-orange-500 text-white text-md px-6 py-3 rounded-md  hover:bg-orange-600 transition">Guardar Cambios</button>
                </div>
        </form>
    </div>
</div>
<script>
function photoPreview() {
    return {
        photos: [],
        deletedPhotos: [],

        loadExistingPhotos(existingPhotos) {
            this.photos = existingPhotos;
        },

        previewImages(event) {
            const files = event.target.files;
            for (let file of files) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photos.push({ id: null, url: e.target.result, file });
                };
                reader.readAsDataURL(file);
            }
        },

        removeImage(index) {
            let photo = this.photos[index];
            if (photo.id) {
                this.deletedPhotos.push(photo.id); // Agregar el ID a la lista de eliminados
            }
            this.photos.splice(index, 1);
        }
    };
}
</script>
@endsection

