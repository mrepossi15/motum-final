@extends('layouts.main')

@section('title', $park->name)

@section('content')
@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
            ✖
        </button>
    </div>
@endif

<div class="container mx-auto mt-5 p-4 shadow-sm border rounded bg-gray-100">
    <!-- Cabecera del parque -->
    <div class="bg-orange-500 text-white py-3 px-4 rounded-t">
        <h1 class="mb-0 text-center text-lg font-bold">{{ $park->name }}</h1>
    </div>

    <!-- Carrusel de Fotos del Parque -->
    @if ($park->photo_urls)
    @php $photos = json_decode($park->photo_urls, true); @endphp
    @if (!empty($photos))
        <div x-data="{ 
                activeSlide: 0, 
                slides: {{ json_encode($photos) }},
                next() { 
                    this.activeSlide = (this.activeSlide + 1) % this.slides.length 
                },
                prev() { 
                    this.activeSlide = this.activeSlide === 0 ? this.slides.length - 1 : this.activeSlide - 1 
                }
            }" class="relative w-full my-3">

            <!-- Contenedor de Imágenes -->
            <div class="relative w-full overflow-hidden rounded-lg h-96">
                <template x-for="(photo, index) in slides" :key="index">
                    <div x-show="activeSlide === index" 
                         x-transition:enter="opacity-0"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="opacity-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0 w-full h-full flex justify-center items-center">
                        <img :src="'{{ url('') }}' + photo" 
                        alt="Foto de {{ $park->name }}" 
                        class="w-full h-96 object-cover rounded-lg">
                                        </div>
                </template>
            </div>

            <!-- Botón Anterior -->
            <button @click="prev()" 
                    class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-gray-700 bg-opacity-50 text-white p-3 rounded-full hover:bg-gray-900">
                ◀
            </button>

            <!-- Botón Siguiente -->
            <button @click="next()" 
                    class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-gray-700 bg-opacity-50 text-white p-3 rounded-full hover:bg-gray-900">
                ▶
            </button>

            <!-- Indicadores -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <template x-for="(photo, index) in slides" :key="index">
                    <button @click="activeSlide = index" 
                        :class="activeSlide === index ? 'bg-white' : 'bg-gray-400'"
                        class="w-3 h-3 rounded-full transition-all"></button>
                </template>
            </div>

        </div>
    @endif
@endif

    <!-- Información del Parque -->
    <div class="p-4">
        <p class="mb-2"><strong>📍 Ubicación:</strong> {{ $park->location }}</p>
        <p class="mb-2"><strong>🌍 Coordenadas:</strong> {{ $park->latitude }}, {{ $park->longitude }}</p>

        <!-- Actividades Disponibles -->
        <h3 class="mt-4 text-lg font-semibold">🏋️ Actividades Disponibles</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
            @foreach($park->trainings->pluck('activity')->unique() as $activity)
                @if ($activity) 
                    <a href="{{ route('trainings.catalog', ['park' => $park->id, 'activity' => $activity->id]) }}" 
                       class="text-decoration-none">
                        <div class="activity-card p-3 text-center shadow-sm border rounded-lg bg-white">
                            <h5 class="text-orange-500 font-bold">{{ $activity->name }}</h5>
                            <p class="text-gray-500">Explora entrenamientos relacionados</p>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-between items-center mt-4">
        <a href="{{ route('students.map') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
            ← Volver al mapa
        </a>

        <!-- Botón de Favoritos -->
        <button id="favorite-btn" 
            class="px-4 py-2 rounded transition {{ $isFavorite ? 'bg-red-500 text-white' : 'border border-red-500 text-red-500' }}" 
            data-id="{{ $park->id }}" 
            data-type="park"
            data-favorite="{{ $isFavorite ? 'true' : 'false' }}">
            ❤️ {{ $isFavorite ? 'Guardado' : 'Guardar' }}
        </button>
    </div>
</div>

<!-- Script de Favoritos -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        let button = document.querySelector("#favorite-btn");

        if (!button) {
            console.warn("❌ No se encontró el botón de favoritos en el DOM.");
            return;
        }

        console.log("✅ Botón encontrado, adjuntando evento click.");

        let isFavorite = button.dataset.favorite === "true";
        button.classList.toggle("bg-red-500", isFavorite);
        button.classList.toggle("border", !isFavorite);
        button.classList.toggle("border-red-500", !isFavorite);
        button.classList.toggle("text-white", isFavorite);
        button.classList.toggle("text-red-500", !isFavorite);
        button.innerHTML = isFavorite ? "❤️ Guardado" : "❤️ Guardar";

        button.addEventListener("click", async function (event) {
            event.preventDefault();
            if (button.dataset.processing === "true") return;
            button.dataset.processing = "true";

            let favoritableId = button.dataset.id;
            let favoritableType = button.dataset.type;
            let isCurrentlyFavorite = button.classList.contains("bg-red-500");

            button.classList.toggle("bg-red-500", !isCurrentlyFavorite);
            button.classList.toggle("border", isCurrentlyFavorite);
            button.classList.toggle("border-red-500", isCurrentlyFavorite);
            button.classList.toggle("text-white", !isCurrentlyFavorite);
            button.classList.toggle("text-red-500", isCurrentlyFavorite);
            button.innerHTML = !isCurrentlyFavorite ? "❤️ Guardado" : "❤️ Guardar";

            try {
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
                if (!csrfToken) {
                    throw new Error("No se encontró el token CSRF en el HTML.");
                }

                let response = await fetch("/favorites/toggle", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ favoritable_id: favoritableId, favoritable_type: favoritableType }),
                });

                if (!response.ok) {
                    throw new Error("Error en la respuesta del servidor");
                }

                let data = await response.json();
                console.log("✅ Respuesta del servidor:", data);

                if (data.status === "added") {
                    button.classList.add("bg-red-500", "text-white");
                    button.classList.remove("border", "border-red-500", "text-red-500");
                    button.innerHTML = "❤️ Guardado";
                    button.dataset.favorite = "true";
                } else if (data.status === "removed") {
                    button.classList.remove("bg-red-500", "text-white");
                    button.classList.add("border", "border-red-500", "text-red-500");
                    button.innerHTML = "❤️ Guardar";
                    button.dataset.favorite = "false";
                }
            } catch (error) {
                console.error("❌ Error en la solicitud:", error);
                alert("Hubo un error al procesar la solicitud.");
            } finally {
                setTimeout(() => {
                    button.dataset.processing = "false";
                }, 1000);
            }
        });
    }, 500);
});
</script>

@endsection