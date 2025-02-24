@extends('layouts.main')

@section('title', 'Detalle del Entrenamiento')

@section('content')
<div class="container mx-auto p-6">
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-orange-600">{{ $training->title }}</h1>
        <div class="relative">
            <button class="bg-white text-black px-3 py-1 rounded-md" onclick="toggleDropdown()">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md hidden">
                <li>
                    <a href="{{ route('trainings.edit', ['id' => $training->id, 'day' => $selectedDay ?? '']) }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                        Editar
                    </a>
                </li>
                <li>
                    <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="openModal('deleteTrainingModal')">
                        Eliminar
                    </button>
                </li>
            </ul>
        </div>
    </div>
<!-- Galería de Imágenes -->
<div class="mb-6">
    <div class="hidden md:block">
        <!-- Enlace general para la galería -->
        <a href="{{ route('trainings.gallery', ['training' => $training->id]) }}" class="block">
            @if($training->photos->count() == 2)
                <div class="grid grid-cols-2 gap-4">
                    @foreach($training->photos as $photo)
                        <div class="overflow-hidden rounded-lg cursor-pointer">
                            <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto de entrenamiento" class="w-full h-[300px] object-cover">
                        </div>
                    @endforeach
                </div>
            @elseif($training->photos->count() == 3)
                <div class="grid grid-cols-4 gap-4">
                    <div class="col-span-3 overflow-hidden rounded-lg cursor-pointer">
                        <img src="{{ asset('storage/training_photos/' . basename($training->photos[0]->photo_path)) }}" alt="Foto principal" class="w-full h-[300px] object-cover">
                    </div>
                    <div class="grid grid-rows-2 gap-4">
                        @foreach($training->photos->slice(1, 2) as $photo)
                            <div class="overflow-hidden rounded-lg cursor-pointer">
                                <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto adicional" class="w-full h-[140px] object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($training->photos->count() >= 4)
                <div class="grid grid-cols-4 gap-4">
                    <div class="col-span-3 overflow-hidden rounded-lg cursor-pointer">
                        <img src="{{ asset('storage/training_photos/' . basename($training->photos[0]->photo_path)) }}" alt="Foto principal" class="w-full h-[300px] object-cover">
                    </div>
                    <div class="grid grid-rows-2 gap-4">
                        <div class="overflow-hidden rounded-lg">
                            <img src="{{ asset('storage/training_photos/' . basename($training->photos[1]->photo_path)) }}" alt="Foto adicional 1" class="w-full h-[140px] object-cover">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($training->photos->slice(2, 2) as $photo)
                                <div class="overflow-hidden rounded-lg">
                                    <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto adicional" class="w-full h-[140px] object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="overflow-hidden rounded-lg cursor-pointer">
                    <img src="{{ asset('storage/training_photos/' . ($training->photos->first() ? basename($training->photos->first()->photo_path) : 'images/default-training.jpg')) }}" alt="Foto de entrenamiento" class="w-full h-[300px] object-cover">
                </div>
            @endif
        </a>
    </div>

    <!-- Carrusel en dispositivos móviles -->
    <div class="block md:hidden" x-data="{ activeSlide: 0 }">
    <a href="{{ route('trainings.gallery', ['training' => $training->id]) }}" class="block">
            <div class="relative w-full overflow-hidden">
                <div class="flex transition-transform duration-500" x-ref="carousel" :style="'transform: translateX(-' + activeSlide * 100 + '%)'">
                    @foreach($training->photos as $photo)
                        <div class="w-full flex-shrink-0">
                            <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto de entrenamiento" class="w-full h-[300px] object-cover rounded-lg">
                        </div>
                    @endforeach
                </div>
            </div>
        </a>
    </div>
</div>

    <!-- Sección de Detalles y Acción -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-3 bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-4">Detalles del Entrenamiento</h2>
            <ul class="space-y-2">
                <li><strong>Parque:</strong> {{ $training->park->name }}</li>
                <li><strong>Ubicación:</strong> {{ $training->park->location }}</li>
                <li><strong>Actividad:</strong> {{ $training->activity->name }}</li>
                <li><strong>Nivel:</strong> {{ $training->level }}</li>
                <li><strong>Entrenador:</strong> {{ $training->trainer->name }}</li>
                <li><strong>Descripción:</strong> {{ $training->description ?? 'No especificada' }}</li>
                <li><strong>Cupos Disponibles:</strong> {{ $filteredReservations->has($selectedTime) ? $filteredReservations[$selectedTime]->count() : 0 }} / {{ $training->available_spots ?? 'No especificados' }}</li>
                <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d \d\e F Y') }}</li>
                <li><strong>Horario:</strong> {{ $selectedTime }} - {{ $filteredSchedules->first()->end_time ?? 'No disponible' }}</li>
            </ul>
        </div>

       <!-- Columna de Acción (25%) -->
<div class="bg-white rounded-lg shadow p-6 lg:static fixed inset-x-0 bottom-0 z-10 md:static">
    <h2 class="text-xl font-semibold mb-4">Acción Principal</h2>
    <div>
        @if($filteredReservations->has($selectedTime) && $filteredReservations[$selectedTime]->isNotEmpty())
            @if($isClassAccessible)
                <a href="{{ $reservationDetailUrl }}" class="block text-center bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                    Tomar Lista
                </a>
            @else
                <button class="block text-center bg-yellow-500 text-black px-4 py-2 rounded-md" disabled>
                    Podrás tomar lista el día de la clase
                </button>
            @endif
        @else
            <button class="block text-center bg-gray-400 text-white px-4 py-2 rounded-md" disabled>
                No hay reservas para tomar lista
            </button>
        @endif
    </div>
</div>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('hidden');
}

function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
}
</script>
@endsection