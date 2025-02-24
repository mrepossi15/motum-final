@extends('layouts.main')

@section('title', 'Detalle del Entrenamiento')

@section('content')
<div class="flex justify-center min-h-screen bg-white text-black">
    <div class="w-full px-4 sm:px-6 md:px-6 lg:px-8">
        <!-- Contenido principal -->
        <div class="relative mx-auto w-11/12 sm:w-10/12 md:w-10/12 lg:w-2/3">
            <!-- Dropdown flotante arriba a la derecha respetando márgenes -->
            <div class="absolute top-0 right-4 sm:right-6 lg:right-8 mt-4 z-10">
                <div class="relative">
                    <button class="bg-white text-black px-3 py-1 rounded-md shadow" onclick="toggleDropdown()">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md hidden z-20">
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
                    <a href="{{ route('trainings.gallery', ['training' => $training->id]) }}" class="block">
                        @if($training->photos->count() == 2)
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($training->photos as $photo)
                                    <div class="overflow-hidden cursor-pointer">
                                        <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto de entrenamiento" class="w-full h-[300px] object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @elseif($training->photos->count() == 3)
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-3 overflow-hidden cursor-pointer">
                                    <img src="{{ asset('storage/training_photos/' . basename($training->photos[0]->photo_path)) }}" alt="Foto principal" class="w-full h-[300px] object-cover">
                                </div>
                                <div class="grid grid-rows-2 gap-4">
                                    @foreach($training->photos->slice(1, 2) as $photo)
                                        <div class="overflow-hidden cursor-pointer">
                                            <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto adicional" class="w-full h-[140px] object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($training->photos->count() >= 4)
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-3 overflow-hidden cursor-pointer">
                                    <img src="{{ asset('storage/training_photos/' . basename($training->photos[0]->photo_path)) }}" alt="Foto principal" class="w-full h-[300px] object-cover">
                                </div>
                                <div class="grid grid-rows-2 gap-4">
                                    <div class="overflow-hidden">
                                        <img src="{{ asset('storage/training_photos/' . basename($training->photos[1]->photo_path)) }}" alt="Foto adicional 1" class="w-full h-[140px] object-cover">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach($training->photos->slice(2, 2) as $photo)
                                            <div class="overflow-hidden">
                                                <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto adicional" class="w-full h-[140px] object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="overflow-hidden cursor-pointer">
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
                                        <img src="{{ asset('storage/training_photos/' . basename($photo->photo_path)) }}" alt="Foto de entrenamiento" class="w-full h-[300px] object-cover">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Fila 2: Detalles -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <div class="md:col-span-3 space-y-4">
                    <h1 class="text-3xl font-bold text-orange-600">{{ $training->title }}</h1>
                    <div class="flex items-center mt-2">
                        @php
                            $averageRating = round($training->averageRating(), 1);
                            $fullStars = floor($averageRating);
                            $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                        @endphp

                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $fullStars)
                                <svg class="w-6 h-6 text-orange-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M10 1l2.39 6.86h7.11l-5.7 4.13 2.39 6.87-5.7-4.14-5.7 4.14 2.39-6.87-5.7-4.13h7.11z"/>
                                </svg>
                            @elseif ($hasHalfStar && $i == $fullStars + 1)
                                <svg class="w-6 h-6 text-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <defs>
                                        <linearGradient id="halfStar">
                                            <stop offset="50%" stop-color="currentColor"/>
                                            <stop offset="50%" stop-color="lightgray"/>
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#halfStar)" d="M10 1l2.39 6.86h7.11l-5.7 4.13 2.39 6.87-5.7-4.14-5.7 4.14 2.39-6.87-5.7-4.13h7.11z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-300 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M10 1l2.39 6.86h7.11l-5.7 4.13 2.39 6.87-5.7-4.14-5.7 4.14 2.39-6.87-5.7-4.13h7.11z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <p class="text-gray-600 mt-1">🏞 {{ $training->park->name }}</p>

                    <!-- Información Clave -->
                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-4 gap-4 border-b border-t pb-4 pt-4">
                        <div class="text-left">
                            <p class="text-sm text-gray-500">📅 Fecha</p>
                            <p class="text-gray-700">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d \d\e F Y') }}</p>
                        </div>

                        <div class="text-left">
                            <p class="text-sm text-gray-500">🕒 Horario</p>
                            <p class="text-gray-700">{{ $selectedTime }} - {{ $filteredSchedules->first()->end_time ?? 'No disponible' }}</p>
                        </div>

                        <div class="text-left">
                            <p class="text-sm text-gray-500">⚽ Actividad</p>
                            <p class="text-gray-700">{{ $training->activity->name }}</p>
                        </div>

                        <div class="text-left">
                            <p class="text-sm text-gray-500">🎟 Cupos</p>
                            <p class="text-gray-700">{{ $filteredReservations->has($selectedTime) ? $filteredReservations[$selectedTime]->count() : 0 }} / {{ $training->available_spots ?? 'No especificados' }}</p>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <h2 class="text-lg font-semibold mb-2">Descripción</h2>
                        <p class="text-gray-700">{{ $training->description ?? 'No hay descripción disponible.' }}</p>
                    </div>
                </div>

                <!-- Botón Ver Lista -->
              <!-- Botón para Desktop y Tablet -->
<div class="hidden md:flex justify-center items-center">
    <div class="bg-white p-4 rounded-lg shadow-lg border w-full max-w-[400px] mx-auto">
        <div class="flex justify-center">
            @if($filteredReservations->has($selectedTime) && $filteredReservations[$selectedTime]->isNotEmpty())
                @if($isClassAccessible)
                    <a href="{{ $reservationDetailUrl }}" class="inline-flex items-center justify-center bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 w-full text-base truncate whitespace-nowrap">
                        Tomar Lista
                    </a>
                @else
                    <button class="bg-yellow-500 text-black px-4 py-2 rounded-md w-full text-base truncate whitespace-nowrap" disabled>
                        Disponible el día de la clase
                    </button>
                @endif
            @else
                <button class="bg-gray-400 text-white px-4 py-2 rounded-md w-full text-base truncate whitespace-nowrap" disabled>
                    No disponible
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Botón Fijo en Móviles (sm o menos) -->
<div class="fixed inset-x-0 bottom-0 z-50 bg-white p-2 shadow-lg md:hidden">
    <div class="flex justify-center">
        <div class="w-full max-w-[400px]">
            @if($filteredReservations->has($selectedTime) && $filteredReservations[$selectedTime]->isNotEmpty())
                @if($isClassAccessible)
                    <a href="{{ $reservationDetailUrl }}" class="inline-flex items-center justify-center bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 w-full text-sm truncate whitespace-nowrap">
                        Tomar Lista
                    </a>
                @else
                    <button class="bg-yellow-500 text-black px-4 py-2 rounded-md w-full text-sm truncate whitespace-nowrap" disabled>
                        Disponible el día de la clase
                    </button>
                @endif
            @else
                <button class="bg-gray-400 text-white px-4 py-2 rounded-md w-full text-sm truncate whitespace-nowrap" disabled>
                    No disponible
                </button>
            @endif
        </div>
    </div>
</div>
                
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