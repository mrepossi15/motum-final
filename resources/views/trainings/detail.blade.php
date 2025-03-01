@extends('layouts.main')

@section('title', $training->title)

@section('content')
<div class="flex justify-center min-h-screen bg-white text-black">
    <div class="w-full px-4 sm:px-6 md:px-6 lg:px-8">
        <!-- Contenido principal -->
        <div class="relative mx-auto w-11/12 sm:w-10/12 md:w-10/12 lg:w-2/3">
            
            <!-- Dropdown flotante -->
            <div class="absolute top-0 right-4 sm:right-6 lg:right-8 mt-4 z-10">
                <div class="relative">
                    <button class="bg-white text-black px-3 py-1 rounded-md shadow" onclick="toggleDropdown()">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md hidden z-20">
                    <li>
    <a href="{{ route('trainings.editAll', ['id' => $training->id]) }}" 
       class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
        Editar
    </a>
</li>
                        <li>
                            <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"
                                    onclick="toggleModal()">Eliminar</button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Galería de Imágenes -->
            @php $photoCount = $training->photos->count(); @endphp

            <div class="mb-6">
                <div class="hidden md:block">
                    <a href="{{ route('trainings.gallery', ['training' => $training->id]) }}" class="block">
                        @php $photoCount = $training->photos->count(); @endphp

                        @if($photoCount > 0)
                            <div class="grid gap-4 
                                        @if($photoCount == 1) grid-cols-1 
                                        @elseif($photoCount == 2) grid-cols-2 
                                        @else grid-cols-4 @endif">

                                {{-- 🖼️ Si hay solo una foto, mostrarla en pantalla completa --}}
                                @if($photoCount == 1)
                                    <div class="overflow-hidden cursor-pointer">
                                        <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}"
                                            alt="Foto entrenamiento"
                                            class="w-full h-[300px] object-cover ">
                                    </div>

                                {{-- 🖼️ Si hay 2 fotos, cada una ocupa la mitad --}}
                                @elseif($photoCount == 2)
                                    @foreach($training->photos as $photo)
                                        <div class="overflow-hidden cursor-pointer">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                                alt="Foto entrenamiento"
                                                class="w-full h-[300px] object-cover ">
                                        </div>
                                    @endforeach

                                {{-- 🖼️ Si hay 3 o más fotos, mostrar en layout de 4 columnas --}}
                                @elseif($photoCount >= 3)
                                    <div class="col-span-3 overflow-hidden">
                                        <img src="{{ asset('storage/' . $training->photos[0]->photo_path) }}"
                                            alt="Foto principal"
                                            class="w-full h-[300px] object-cover ">
                                    </div>

                                    <div class="grid grid-rows-2 gap-4">
                                        @foreach($training->photos->slice(1, 2) as $photo)
                                            <div class="overflow-hidden cursor-pointer">
                                                <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                                    alt="Foto adicional"
                                                    class="w-full h-[140px] object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        @else
                            {{-- 🖼️ Imagen por defecto si no hay fotos --}}
                            <div class="overflow-hidden cursor-pointer">
                                <img src="{{ asset('images/default-training.jpg') }}" 
                                    alt="Foto de entrenamiento" 
                                    class="w-full h-[300px] object-cover ">
                            </div>
                        @endif
                    </a>
                </div>
            </div>

            <!-- 📋 Detalles del Entrenamiento -->
            <h1 class="text-3xl font-bold text-orange-600">{{ $training->title }}</h1>
            <p class="text-gray-600">🏞 {{ $training->park->name }} - ⚽ {{ $training->activity->name }} - Nivel: {{ $training->level }}</p>


            <!-- 📅 Horarios -->
            <div class="mt-4">
                <strong class="text-gray-700">Horarios:</strong>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach ($training->schedules as $schedule)
                        <span class="bg-gray-200 text-gray-700 text-xs px-3 py-1 rounded">
                            {{ $schedule->day }} ({{ $schedule->start_time }} - {{ $schedule->end_time }})
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- 📜 Descripción -->
            <div class="mt-6">
                <strong class="text-gray-700">Descripción:</strong>
                <p class="text-gray-600 mt-2">{{ $training->description }}</p>
            </div>

            <!-- 💰 Precios -->
            @if($training->prices->isNotEmpty())
                        <div class="border-b pb-4 pt-4">
                            <h2 class="text-lg font-semibold mb-2">💰 Precios</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($training->prices as $price)
                                    <div class="border p-4 rounded-lg shadow-sm bg-white">
                                        <p class="text-gray-700"><strong>{{ $price->weekly_sessions }} x semana</strong></p>
                                        <p class="text-gray-500 text-sm">Precio: <span class="text-orange-600 font-semibold">${{ number_format($price->price, 2) }}</span></p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
             <!-- 📜 Participanets -->
             <div class="mt-6">
                <strong class="text-gray-700">Participantes:</strong>
                <ul class="divide-y divide-gray-200 bg-white shadow-md rounded-lg">
                    @foreach($training->students as $student)
                        <li class="p-4 hover:bg-gray-100 flex justify-between items-center">
                            <a href="{{ route('students.profile', $student->id) }}" class="font-semibold text-blue-600 hover:underline">
                                {{ $student->name }}
                            </a>
                            <span class="text-gray-500 text-sm">({{ $student->email }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- ⭐ Reseñas -->
            <div class="mt-6">
                <strong class="text-gray-700">Reseñas:</strong>
                @if ($training->reviews->isNotEmpty())
                    @foreach ($training->reviews as $review)
                        <div class="bg-gray-100 p-3 rounded-lg mt-2">
                            <p class="text-gray-800"><strong>{{ $review->user->name }}</strong> 
                                <span class="text-yellow-500">⭐ {{ $review->rating }}/5</span>
                            </p>
                            <p class="text-gray-600 mt-1">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 mt-2">No hay reseñas aún.</p>
                @endif
            </div>


            <!-- 🔙 Botón para volver -->
            <div class="mt-6">
                <a href="{{ route('trainer.show-trainings') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Volver a Mis Entrenamientos
                </a>
            </div>

        </div>
    </div>
</div>
<!-- 🔥 Modal para Confirmar Eliminación -->
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden" id="deleteModal">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h5 class="text-lg font-semibold text-red-600">Confirmar Eliminación</h5>
        <p class="text-gray-700 mt-2">¿Estás seguro de que deseas eliminar este entrenamiento? Esta acción no se puede deshacer.</p>
        
        <!-- Botones del Modal -->
        <div class="flex justify-end space-x-4 mt-4">
            <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                    onclick="toggleModal()">Cancelar</button>

            <form action="{{ route('trainings.destroyAll', $training->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</div>
<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('hidden');
    }

    
    function toggleModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.toggle('hidden');
    }
</script>


@endsection