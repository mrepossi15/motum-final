@extends('layouts.main')

@section('title', "Perfil de {$trainer->name}")

@section('content')
<div class="flex justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h2 class="text-2xl font-semibold mb-4"> Perfil de {{ $trainer->name }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-24 md:pb-6">
            
            <!-- 🖼️ Información del Entrenador -->
            <div class="bg-white shadow-lg rounded-lg p-4 md:sticky md:top-4 md:self-start w-full border-t">
                <div class="border-b pb-4">
                    <div class="flex items-center gap-6">
                        <!-- 📸 Imagen de Perfil -->
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-orange-300 shadow-md">
                            @if($trainer->profile_pic)
                                <img src="{{ asset('storage/' . $trainer->profile_pic) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/default-profile.png') }}" alt="Foto de perfil por defecto" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">{{ $trainer->name }}</h2>
                            <p class="text-gray-500">{{ ucfirst($trainer->role) }}</p>
                            <p class="text-gray-700">
                                {{ $trainer->birth ? \Carbon\Carbon::parse($trainer->birth)->age . ' años' : 'Fecha de nacimiento no especificada' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- 🏋️‍♂️ Especialidades -->
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($trainer->activities as $activity)
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-md text-sm">
                            {{ $activity->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- 📋 Información y Entrenamientos -->
            <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-4 px-6 relative">
                
                <!-- 📌 Biografía -->
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <x-lucide-user class="w-5 h-5 text-orange-500 mr-1" /> Biografía
                </h3>
                <p class="text-gray-600  pb-4">{{ $trainer->biography ?? 'Sin información' }}</p>

                <hr class="my-4">

               <!-- 📌 Experiencia -->
                <div class="mt-4 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-briefcase class="w-5 h-5 text-orange-500 mr-1" /> Experiencia
                    </h3>

                    <div class="bg-white p-4 shadow-md rounded-md">
                        @if ($experiences->isEmpty())
                            <p class="text-gray-500 text-center">No tienes experiencias registradas.</p>
                        @else
                            <ul class="divide-y divide-gray-200">
                                @foreach ($experiences->take(2) as $experience)
                                    <li class="py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $experience->role }}</h3>
                                            <p class="text-gray-600"><strong>Empresa/Gimnasio:</strong> {{ $experience->company ?? 'Freelance' }}</p>
                                            <p class="text-gray-600"><strong>Periodo:</strong> {{ $experience->year_start }} - 
                                                @if($experience->currently_working)
                                                    <span class="text-green-500 font-semibold">Actualmente</span>
                                                @else
                                                    {{ $experience->year_end }}
                                                @endif
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- 🔳 Botón para abrir el modal -->
                <div class="flex justify-end mt-2">
                    <a href="#experienceModal" class="text-orange-500 font-semibold underline hover:underline">
                        Ver más experiencias
                    </a>
                </div>
                <!-- 🔲 Modal de experiencias -->
                <div id="experienceModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg relative">
                        <!-- ❌ Botón para cerrar -->
                        <a href="#" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-lg font-bold">✖</a>

                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <x-lucide-briefcase class="w-5 h-5 text-orange-500 mr-1" /> Todas las Experiencias
                        </h3>

                        <ul class="divide-y divide-gray-200 max-h-[60vh] overflow-y-auto">
                            @foreach ($experiences as $experience)
                                <li class="py-4">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $experience->role }}</h4>
                                    <p class="text-gray-600"><strong>Empresa/Gimnasio:</strong> {{ $experience->company ?? 'Freelance' }}</p>
                                    <p class="text-gray-600"><strong>Periodo:</strong> {{ $experience->year_start }} - 
                                        @if($experience->currently_working)
                                            <span class="text-green-500 font-semibold">Actualmente</span>
                                        @else
                                            {{ $experience->year_end }}
                                        @endif
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <!-- 🔹 Estilos para el modal -->
                <style>
                    #experienceModal:target {
                        display: flex;
                    }
                </style>

                <hr class="my-4">
                <!-- 📌 Entrenamientos -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-dumbbell class="w-5 h-5 text-orange-500 mr-2" /> Entrenamientos
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-6 gap-6">
                        @foreach($trainings->take(3) as $training) {{-- Muestra solo 3 entrenamientos --}}
                            @if($training)
                                <a href="{{ route('trainings.selected', $training->id) }}" class="block">
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105 cursor-pointer">
                                        
                                        <!-- 📸 Imagen del entrenamiento -->
                                        @if ($training->photos->isNotEmpty())
                                            <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                                class="w-full h-48 object-cover" 
                                                alt="Foto de entrenamiento">
                                        @endif

                                        <!-- 📝 Detalles del entrenamiento -->
                                        <div class="p-4">
                                            <h5 class="text-xl font-semibold text-gray-800">{{ $training->title }}</h5>

                                            <p class="text-gray-600 text-sm">
                                                <strong>Ubicación:</strong> {{ $training->park->name ?? 'No disponible' }} <br>
                                                <strong>Actividad:</strong> {{ $training->activity->name ?? 'No disponible' }} <br>
                                                <strong>Nivel:</strong> {{ ucfirst($training->level) ?? 'No especificado' }}
                                            </p>

                                            <!-- 📅 Días con clases -->
                                            <div class="mt-3">
                                                <strong class="text-gray-700">Días con Clases:</strong>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach ($training->schedules as $schedule)
                                                        <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                            {{ ucfirst($schedule->day) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>

                    <!-- 📌 Botón "Ver más entrenamientos" si hay más de 3 -->
                    @if($trainings->count() > 3)
                        <div class="mt-4 flex justify-end">
                            <button onclick="openModal()" 
                                class="text-orange-500 font-semibold hover:underline transition">
                                Ver más entrenamientos →
                            </button>
                        </div>
                    @endif
                </div>

                <!-- 🏋️‍♂️ MODAL -->
                <div id="trainingsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
                    <div class="bg-white max-w-4xl w-full p-6 rounded-lg shadow-lg relative">
                        
                        <!-- ❌ Botón de cierre -->
                        <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
                            <x-lucide-x class="w-6 h-6" />
                        </button>

                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Todos los Entrenamientos</h2>

                        <div class="max-h-[500px] overflow-y-auto">
                            @foreach($trainings as $training)
                                <a href="{{ route('trainings.selected', $training->id) }}" class="block mb-4">
                                    <div class="bg-white shadow-md rounded-lg overflow-hidden flex items-center p-4 space-x-4 transition-transform transform hover:scale-105 cursor-pointer">
                                        
                                        <!-- 📸 Imagen del entrenamiento (Columna Izquierda) -->
                                        @if ($training->photos->isNotEmpty())
                                            <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                                class="w-32 h-32 object-cover rounded-sm" 
                                                alt="Foto de entrenamiento">
                                        @endif

                                        <!-- 📝 Detalles del entrenamiento (Columna Derecha) -->
                                        <div class="flex-1">
                                            <h5 class="text-lg font-semibold text-gray-800">{{ $training->title }}</h5>

                                            <p class="text-gray-600 text-sm">
                                                <strong>Ubicación:</strong> {{ $training->park->name ?? 'No disponible' }} <br>
                                                <strong>Actividad:</strong> {{ $training->activity->name ?? 'No disponible' }} <br>
                                                <strong>Nivel:</strong> {{ ucfirst($training->level) ?? 'No especificado' }}
                                            </p>

                                            <!-- 📅 Días con clases -->
                                            <div class="mt-2">
                                                <strong class="text-gray-700">Días con Clases:</strong>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach ($training->schedules as $schedule)
                                                        <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                            {{ ucfirst($schedule->day) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>


                <hr class="my-4">
                <!-- 📌 Reseñas -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-lucide-star class="w-5 h-5 text-orange-500 mr-2" /> Reseñas
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4  gap-y-2">
                    @foreach ($trainer->reviews->take(2) as $index => $review)
                        <div class="bg-gray-50 py-4 px-6 rounded-md shadow-md flex items-start space-x-6">           
                            <div>
                                <!-- ⭐ Calificación -->
                                <div class="grid grid-cols-12 gap-4 items-center">
                                        <!-- 🖼️ Foto del usuario -->
                                        <div class="col-span-2 flex justify-center">
                                            <img src="{{ $review->user->profile_pic ? Storage::url($review->user->profile_pic) : asset('images/default-avatar.png') }}" 
                                            alt="Foto de {{ $review->user->name }}" 
                                            class="w-14 h-14 rounded-full border border-gray-300 object-cover">
                                        </div>

                                        <!-- 👤 Nombre + ⭐ Calificación -->
                                        <div class="col-span-10">
                                            <p class="font-semibold text-gray-900">{{ $review->user->name }}</p>
                                            <div class="flex items-center space-x-1 mt-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <x-lucide-star class="w-4 h-4 {{ $i <= $review->rating ? 'text-orange-500 fill-current' : 'text-gray-300' }}" />
                                                @endfor
                                                <p class="text-sm text-gray-500"><strong>{{ \Carbon\Carbon::parse($review->created_at)->locale('es')->diffForHumans() }}</strong></p>
                                            </div>
                                        </div>
                                </div>

                                <!-- 🏗️ Fila 2: Comentario + Botón de eliminar -->
                                <div class="mt-3">
                                    <p class="text-gray-700 font-light">{{ $review->comment }}</p>

                                    <!-- ❌ Botón de eliminar (solo si es su comentario o admin) -->
                                    @if(auth()->id() === $review->user_id || auth()->user()->role === 'admin')
                                        <button type="button" 
                                                 onclick="openDeleteModal('{{ route('reviews.destroy', $review->id) }}')" 
                                                 class="flex items-center text-red-500 hover:text-red-700 mt-2 ">
                                            <x-lucide-x class="w-5 h-5 text-red-500" />
                                            <span>Eliminar</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button id="open-reviews-modal" class="text-orange-500 font-semibold underline hover:underline">
                            Ver más opiniones
                </button>
                <!-- Botón para abrir el modal -->
                @if ($training->reviews->count() > 2)
                    <!-- Botón para abrir el modal -->
                    <div class="flex justify-end mt-2">
                        <button id="open-reviews-modal" class="text-orange-500 font-semibold underline hover:underline">
                            Ver más opiniones
                        </button>
                    </div>
                @endif
                </div>
                <hr class="my-4">
                <!-- Modal de reseñas -->
                <div id="reviews-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
                    <div id="reviews-content" class="bg-white p-6 rounded-lg w-full max-w-md md:max-w-4xl shadow-lg relative transform transition-transform duration-300 ease-in-out h-[90vh] overflow-hidden">
                        <!-- ❌ Botón para cerrar -->
                        <button id="close-reviews-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                            &times;
                        </button>

                        <h3 class="text-2xl font-semibold mb-4">Todas las opiniones</h3>

                        <!-- 📜 Contenedor de todas las reseñas -->
                        <div class="overflow-y-auto h-[80vh]  space-y-6">
                            @foreach ($trainer->reviews->sortByDesc('created_at') as $review)
                                <div class="rounded-sm bg-white  border-b">

                                    <!-- 🏗️ Fila 1: Foto + Nombre + Calificación -->
                                    <div class="flex items-center space-x-3">
                                        <!-- 🖼️ Foto del usuario -->
                                        <img src="{{ $review->user->profile_pic ? Storage::url($review->user->profile_pic) : asset('images/default-avatar.png') }}" 
                                            alt="Foto de {{ $review->user->name }}" 
                                            class="w-12 h-12 rounded-full border border-gray-300 object-cover shadow-sm aspect-square">

                                        <!-- 👤 Nombre + ⭐ Calificación -->
                                        <div>
                                            <p class="font-semibold text-gray-900 leading-tight">{{ $review->user->name }}</p>
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-1 mt-1">
                                                <div class="flex space-x-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <x-lucide-star class="w-4 h-4 {{ $i <= $review->rating ? 'text-orange-500 fill-current' : 'text-gray-300' }}" />
                                                    @endfor
                                                </div>
                                                <p class="text-sm text-gray-500"><strong>Hace {{ \Carbon\Carbon::parse($review->created_at)->locale('es')->diffForHumans() }}</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                

                                    <!-- 🏗️ Fila 2: Comentario + Botón de eliminar -->
                                    <div class="mt-3">
                                        <p class="text-gray-700 font-light">{{ $review->comment }}</p>

                                        @if(auth()->id() === $review->user_id || auth()->user()->role === 'admin')
                                            <button type="button" 
                                                onclick="openDeleteModal('{{ route('reviews.destroy', $review->id) }}')" 
                                                class="text-red-500 hover:text-red-700 mt-2">
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Formulario para agregar reseña sobre el entrenador -->
                @auth
                <div class="mb-20">
                    @if($hasPurchasedFromTrainer) 
                    <form x-data="{ loading: false, rating: 0 }" 
                        @submit="loading = true" 
                        action="{{ route('reviews.store') }}" 
                        method="POST" 
                        class="bg-gray-50 p-6 rounded-lg shadow-md border border-gray-200">

                        @csrf
                        <input type="hidden" name="trainer_id" value="{{ $trainer->id }}">

                        <!-- ⭐ Calificación con Estrellas -->
                        <label class="block font-semibold text-gray-800 mb-2">Calificación:</label>
                        <div class="flex space-x-1 mb-4">
                            @foreach (range(1, 5) as $i)
                                <button type="button" @click="rating = rating === {{ $i }} ? 0 : {{ $i }}" class="focus:outline-none">
                                    <x-lucide-star 
                                        class="w-5 h-5 transition-transform duration-200 transform scale-100 hover:scale-110"
                                        x-bind:class="rating >= {{ $i }} ? 'text-orange-500 fill-orange-500' : 'text-gray-300 fill-none'"
                                    />
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="rating" x-model="rating">

                        <!-- 📝 Comentario -->
                        <label for="comment" class="block font-semibold text-gray-800">Comentario:</label>
                        <textarea name="comment" id="comment" 
                            class="border border-gray-300 p-3 rounded-md w-full mt-1 focus:ring-2 focus:ring-orange-500 transition resize-none" 
                            rows="3" required></textarea>

                        <!-- 🔄 Spinner y Botón -->
                        <div class="flex justify-end mt-4">
                            <button type="submit" 
                                class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full sm:w-auto md:w-1/3 lg:w-1/4 hover:bg-orange-600 transition flex items-center justify-center">
                                <span x-show="!loading">Enviar Reseña</span>
                                <svg x-show="loading" class="animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                @else
                    <p class="text-gray-500">Debes haber comprado un entrenamiento con este entrenador para dejar una reseña.</p>
                @endif
                @endauth

            </div>
        </div>
    </div>
</div>
<script>

    function openModal() {
        document.getElementById('trainingsModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('trainingsModal').classList.add('hidden');
    }

    ///modal reviews
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("reviews-modal");
        const openModalButton = document.getElementById("open-reviews-modal"); // Asegúrate de tener un botón con este ID
        const closeModalButton = document.getElementById("close-reviews-modal");

        // Función para abrir el modal y bloquear el scroll
        function openModal() {
            modal.classList.remove("hidden");
            document.body.classList.add("overflow-hidden"); // Bloquea el scroll de fondo
        }

        // Función para cerrar el modal y desbloquear el scroll
        function closeModal() {
            modal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden"); // Habilita el scroll de fondo
        }

        // Evento para abrir el modal
        if (openModalButton) {
            openModalButton.addEventListener("click", openModal);
        }

        // Evento para cerrar el modal
        if (closeModalButton) {
            closeModalButton.addEventListener("click", closeModal);
        }

        // Cerrar con la tecla ESC
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                closeModal();
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        let modal = document.getElementById("sessions-modal");
        let modalContent = document.getElementById("sessions-content");
        let openModalBtn = document.getElementById("openModal");
        let closeModalBtn = document.getElementById("close-sessions-btn");

        // 🟢 ABRIR MODAL
        openModalBtn.addEventListener("click", function () {
            modal.classList.remove("hidden"); // Hace visible el modal
            setTimeout(() => {
                modalContent.classList.remove("translate-y-full");
            }, 10); // Pequeño delay para suavizar la animación
        });

        // ❌ CERRAR MODAL
        closeModalBtn.addEventListener("click", function () {
            modalContent.classList.add("translate-y-full");
            setTimeout(() => {
                modal.classList.add("hidden");
            }, 300); // Espera la animación antes de ocultarlo
        });

        // ⬆️ Cerrar tocando fuera del modal
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                modalContent.classList.add("translate-y-full");
                setTimeout(() => {
                    modal.classList.add("hidden");
                }, 300);
            }
        });
    });
    //CONFIRMRAR BORRAR
    function openDeleteModal(action) {
        document.getElementById("delete-modal").classList.remove("hidden");
        document.getElementById("delete-form").setAttribute("action", action);
        document.body.classList.add("overflow-hidden"); // Bloquea el scroll
    }

    function closeDeleteModal() {
        document.getElementById("delete-modal").classList.add("hidden");
        document.body.classList.remove("overflow-hidden"); // Restaura el scroll
    }

    // Cerrar modal con la tecla ESC
    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            closeDeleteModal();
        }
    });

</script>

@endsection