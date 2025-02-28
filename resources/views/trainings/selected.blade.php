@extends('layouts.main')

@section('title', 'Detalle del Entrenamiento')

@section('content')

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" aria-label="Close">✖</button>
    </div>
@endif

<main class="container mx-auto mt-4">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-orange-500 text-white px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold">{{ $training->title }}</h2>
            <button id="favorite-btn" 
                class="px-4 py-2 rounded transition {{ $isFavorite ? 'bg-red-500 text-white' : 'border border-red-500 text-red-500' }}" 
                data-id="{{ $training->id }}" 
                data-type="training"
                data-favorite="{{ $isFavorite ? 'true' : 'false' }}">
                ❤️ {{ $isFavorite ? 'Guardado' : 'Guardar' }}
            </button>
        </div>

        <div class="p-6">
            <p><strong>🏞 Parque:</strong> {{ $training->park->name }}</p>
            <p><strong>📍 Ubicación:</strong> {{ $training->park->location }}</p>
            <p><strong>🏋️ Actividad:</strong> {{ $training->activity->name }}</p>
            <p><strong>🎚 Nivel:</strong> {{ ucfirst($training->level) }}</p>
            <p><strong>📖 Descripción:</strong> {{ $training->description ?? 'No especificada' }}</p>

            <p>
                <strong>👨‍🏫 Entrenador:</strong>
                <a href="{{ route('students.trainerProfile', ['id' => $training->trainer->id]) }}" class="text-orange-500 underline">
                    {{ $training->trainer->name }}
                </a>
            </p>

            <h5 class="font-semibold mt-4">📅 Horarios:</h5>
            <ul class="list-disc list-inside text-gray-700">
                @forelse ($training->schedules as $schedule)
                    <li>
                        {{ ucfirst($schedule->day) }}:
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                    </li>
                @empty
                    <li class="text-gray-500">No hay horarios disponibles.</li>
                @endforelse
            </ul>

            <h5 class="font-semibold mt-4">💰 Precios:</h5>
            <ul class="list-disc list-inside text-gray-700">
                @forelse ($training->prices as $price)
                    <li>{{ $price->weekly_sessions }} veces por semana: ${{ number_format($price->price, 2) }}</li>
                @empty
                    <li class="text-gray-500">No hay precios definidos.</li>
                @endforelse
            </ul>

            <h5 class="font-semibold mt-4">📸 Fotos de Entrenamientos:</h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse ($training->photos as $photo)
                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Entrenamiento" class="w-full h-32 object-cover rounded shadow-md">
                @empty
                    <p class="text-gray-500">No hay fotos disponibles.</p>
                @endforelse
            </div>

            <hr class="my-4">

            <h5 class="font-semibold">📝 Reseñas</h5>
            @if($training->reviews->isEmpty())
                <p class="text-gray-500">No hay reseñas para este entrenamiento.</p>
            @else
                @foreach($training->reviews as $review)
                    <div class="border p-3 rounded shadow-sm mt-2">
                        <p><strong>⭐ Calificación:</strong> {{ $review->rating }} / 5</p>
                        <p><strong>Comentario:</strong> {{ $review->comment }}</p>
                        <p><small><strong>Autor:</strong> {{ $review->user->name }}</small></p>

                        @if(Auth::id() === $review->user_id || Auth::user()->role === 'admin')
                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('¿Seguro que quieres eliminar esta reseña?')">
                                    ❌ Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            @endif

            <hr class="my-4">

            @auth
                @if($hasPurchased)
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="training_id" value="{{ $training->id }}">
                        <label for="rating" class="block font-semibold">Calificación:</label>
                        <select name="rating" id="rating" class="border p-2 rounded w-full mt-1" required>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>

                        <label for="comment" class="block font-semibold mt-2">Comentario:</label>
                        <textarea name="comment" id="comment" class="border p-2 rounded w-full mt-1" rows="3" required></textarea>

                        <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                            Enviar Reseña
                        </button>
                    </form>
                @else
                    <p class="text-gray-500">Debes haber comprado este entrenamiento para dejar una reseña.</p>
                @endif
            @endauth
        </div>
        <!-- Formulario para agregar clase al carrito -->
<form action="{{ route('cart.add') }}" method="POST" class="bg-white p-4 rounded shadow-md">
    @csrf
    <input type="hidden" name="training_id" value="{{ $training->id }}">

    <div class="mb-4">
        <label for="weekly_sessions" class="block text-sm font-semibold text-gray-700">
            Cantidad de veces por semana:
        </label>
        <select name="weekly_sessions" id="weekly_sessions" 
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            @foreach ($training->prices as $price)
                <option value="{{ $price->weekly_sessions }}">
                    {{ $price->weekly_sessions }} veces por semana - ${{ $price->price }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" 
            class="w-full bg-orange-500 text-white px-4 py-2 rounded shadow-md hover:bg-orange-600 transition">
        Comprar y reservar clase
    </button>
</form>

<!-- Modal de confirmación de carrito con Alpine.js -->
@if(session('cart_success'))
    <div x-data="{ open: true }">
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-96 p-6">
                <div class="flex justify-between items-center border-b pb-2">
                    <h5 class="text-lg font-semibold text-orange-500">¡Agregado al carrito!</h5>
                    <button @click="open = false" class="text-gray-500 hover:text-gray-700">✖</button>
                </div>

                <div class="mt-4 text-gray-700">
                    ✅ {{ session('cart_success') }}
                </div>

                <div class="mt-4 text-right">
                    <button @click="open = false" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

        <div class="px-6 py-4 bg-gray-100 text-right">
            <a href="{{ route('parks.show', $training->park->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                ← Volver a clases
            </a>
        </div>
    </div>
</main>



<!-- Script para Favoritos -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let button = document.querySelector("#favorite-btn");

    if (!button) return;

    button.addEventListener("click", async function (event) {
        event.preventDefault();

        let isFavorite = button.dataset.favorite === "true";
        button.classList.toggle("bg-red-500", !isFavorite);
        button.classList.toggle("border", isFavorite);
        button.classList.toggle("border-red-500", isFavorite);
        button.classList.toggle("text-white", !isFavorite);
        button.classList.toggle("text-red-500", isFavorite);
        button.innerHTML = !isFavorite ? "❤️ Guardado" : "❤️ Guardar";

        try {
            let response = await fetch("/favorites/toggle", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ favoritable_id: button.dataset.id, favoritable_type: button.dataset.type }),
            });

            let data = await response.json();
            button.dataset.favorite = data.status === "added" ? "true" : "false";

        } catch (error) {
            alert("Hubo un error al procesar la solicitud.");
        }
    });
});
</script>

@endsection