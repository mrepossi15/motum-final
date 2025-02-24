@extends('layouts.main')

@section('title', 'Galería de Fotos')

@section('content')
<div class="container mx-auto p-6" x-data="galleryData()">
    <h1 class="text-3xl font-bold text-orange-600 mb-4">Galería de {{ $training->title }}</h1>

    <!-- Formulario para Agregar Fotos -->
    <div class="mb-6">
        <form action="{{ route('trainings.photos.store', $training->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-4">
            @csrf
            <input type="file" name="photos[]" accept="image/*" multiple required class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-500 file:text-white hover:file:bg-orange-600">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Agregar Fotos</button>
        </form>
    </div>

    <!-- Galería de imágenes -->
    @if($training->photos->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($training->photos as $index => $photo)
                <div class="relative">
                    <!-- Imagen -->
                    <div class="overflow-hidden cursor-pointer" @click="openModal({{ $index }})">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto de entrenamiento" class="w-full h-[300px] object-cover">
                    </div>

                    <!-- Botón para eliminar la foto -->
                    <form action="{{ route('trainings.photos.destroy', $photo->id) }}" method="POST" class="absolute top-2 right-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded-full hover:bg-red-600">✖️</button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No hay fotos disponibles para este entrenamiento.</p>
    @endif

    <!-- Modal de la imagen -->
    <template x-if="showModal">
        <div class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center" @click="showModal = false">
            <div class="relative w-full max-w-4xl mx-auto" @click.stop>
                <button class="absolute top-4 right-4 text-white text-3xl focus:outline-none z-50" type="button" @click.stop="showModal = false">
                    &times;
                </button>

                <div class="relative">
                    <img :src="photos[activeIndex]" alt="Foto del entrenamiento" class="w-full max-h-[80vh] object-contain">

                    <button class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white text-black p-2 rounded-full" @click.stop="prevPhoto">
                        &#10094;
                    </button>

                    <button class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white text-black p-2 rounded-full" @click.stop="nextPhoto">
                        &#10095;
                    </button>
                </div>
            </div>
        </div>
    </template>

    <div class="mt-6">
        <button onclick="window.history.back()" class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600">Volver</button>
    </div>
</div>

<!-- Script Alpine.js -->
<script>
function galleryData() {
    return {
        showModal: false,
        activeIndex: 0,
        photos: {!! json_encode($training->photos->map(fn($photo) => asset('storage/' . $photo->photo_path))) !!},

        openModal(index) {
            this.activeIndex = index;
            this.showModal = true;
        },

        nextPhoto() {
            this.activeIndex = (this.activeIndex + 1) % this.photos.length;
        },

        prevPhoto() {
            this.activeIndex = (this.activeIndex - 1 + this.photos.length) % this.photos.length;
        }
    }
}
</script>
@endsection