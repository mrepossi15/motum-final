<div class="relative mx-auto w-full">
    @if (!empty($photos))
        <div 
            x-data="{ 
                activeSlide: 0, 
                slides: {{ json_encode($photos) }},
                showModal: false,
                next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
                prev() { this.activeSlide = this.activeSlide === 0 ? this.slides.length - 1 : this.activeSlide - 1 }
            }" 
            class="relative w-full mt-0"
        >

            {{-- 🖥️ Vista escritorio --}}
            <div class="hidden lg:grid grid-cols-4 gap-2">
                @switch(count($photos))
                    @case(1)
                        <div class="col-span-4">
                            <img src="{{ $photos[0] }}" alt="Foto principal de {{ $title }}"
                                class="w-full h-[400px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 0">
                        </div>
                        @break

                    @case(2)
                        <div class="col-span-3">
                            <img src="{{ $photos[0] }}" alt="Foto principal"
                                class="w-full h-[400px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 0">
                        </div>
                        <div class="col-span-1">
                            <img src="{{ $photos[1] }}" alt="Foto secundaria"
                                class="w-full h-[400px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 1">
                        </div>
                        @break

                    @case(3)
                        <div class="col-span-3">
                            <img src="{{ $photos[0] }}" alt="Foto principal"
                                class="w-full h-[400px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 0">
                        </div>
                        <div class="col-span-1 grid grid-rows-2 gap-2">
                            <img src="{{ $photos[1] }}" class="w-full h-[195px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 1">
                            <img src="{{ $photos[2] }}" class="w-full h-[195px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 2">
                        </div>
                        @break

                    @default
                        <div class="col-span-3">
                            <img src="{{ $photos[0] }}" alt="Foto principal"
                                class="w-full h-[400px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 0">
                        </div>
                        <div class="col-span-1 grid grid-rows-2 gap-2">
                            <img src="{{ $photos[1] }}" class="w-full h-[195px] object-cover cursor-pointer"
                                @click="showModal = true; activeSlide = 1">
                            <div class="grid grid-cols-2 gap-2">
                                <img src="{{ $photos[2] }}" class="w-full h-[195px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 2">
                                <img src="{{ $photos[3] }}" class="w-full h-[195px] object-cover cursor-pointer"
                                    @click="showModal = true; activeSlide = 3">
                            </div>
                        </div>
                @endswitch
            </div>

            {{-- 📱 Carrusel mobile --}}
            <div class="lg:hidden relative w-full" x-data="{
                activeSlide: 0, 
                slides: {{ json_encode($photos) }},
                touchStartX: 0,
                touchEndX: 0,
                startSwipe(event) { this.touchStartX = event.touches[0].clientX; },
                endSwipe(event) { 
                    this.touchEndX = event.changedTouches[0].clientX;
                    let diff = this.touchStartX - this.touchEndX;
                    if (Math.abs(diff) > 50) {
                        if (diff > 0) { this.activeSlide = (this.activeSlide + 1) % this.slides.length; } 
                        else { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length; }
                    }
                }
                }">
                <img :src="slides[activeSlide]" alt="Foto de {{ $title }}"
                    class="w-full h-[300px] object-cover"
                    @touchstart="startSwipe($event)"
                    @touchend="endSwipe($event)"
                    @click="showModal = true">
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    <template x-for="(photo, index) in slides" :key="index">
                        <button @click="activeSlide = index" 
                            :class="activeSlide === index ? 'bg-orange-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full transition-all"></button>
                    </template>
                </div>
            </div>

            {{-- 📸 Modal --}}
            <template x-if="showModal">
                <div class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center" @click="showModal = false">
                    <div class="relative w-full max-w-4xl mx-auto shadow-lg" @click.stop>
                        <button class="absolute top-4 right-4 p-2 rounded-full z-50 focus:outline-none" type="button" @click="showModal = false">
                            <x-lucide-x class="w-6 h-6 text-white" />
                        </button>

                        <div class="relative">
                            <img :src="slides[activeSlide]" alt="Imagen ampliada" class="w-full max-h-[80vh] object-contain">
                            <button class="hidden lg:flex absolute top-1/2 left-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md"
                                @click.stop="prev()">
                                <x-lucide-chevron-left class="w-6 h-6 text-orange-500" />
                            </button>
                            <button class="hidden lg:flex absolute top-1/2 right-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md"
                                @click.stop="next()">
                                <x-lucide-chevron-right class="w-6 h-6 text-orange-500" />
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            
        </div>
        @endif
        {{-- 🧩 Botón flotante (ej: favorito) --}}
        @if ($hasFloatingButton ?? false)
            <div class="absolute top-4 right-4 sm:right-6 lg:right-8 z-10">
                <button 
                    id="floating-favorite-btn"
                    class="p-2 rounded-full bg-white shadow-md"
                    data-id="{{ $favoriteId }}" 
                    data-type="{{ $favoriteType }}"
                    data-favorite="{{ $isFavorite ? 'true' : 'false' }}"
                >
                    <x-lucide-heart 
                        id="floating-favorite-icon"
                        :class="$isFavorite ? 'w-6 h-6 text-orange-500 fill-current' : 'w-6 h-6 text-orange-500 stroke-current'" 
                    />
                </button>
            </div>
        @endif

        {{-- 🧩 Botón de opciones (menú desplegable, etc) --}}
        @if ($hasActions ?? false)
            <div class="absolute top-0 right-4 sm:right-6 lg:right-8 mt-4 z-10">
                <div class="relative">
                    {{ $actions }}
                </div>
            </div>
        @endif
    
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let desktopButton = document.querySelector("#favorite-btn");
        let floatingButton = document.querySelector("#floating-favorite-btn");

        if (!desktopButton && !floatingButton) return;

        function toggleFavorite(button, icon) {
            let isCurrentlyFavorite = button.dataset.favorite === "true";

            // Cambia el estado del botón visualmente
            button.classList.toggle("bg-black", !isCurrentlyFavorite);
            button.classList.toggle("text-orange-500", !isCurrentlyFavorite);
            button.classList.toggle("border-black", isCurrentlyFavorite);
            button.classList.toggle("text-black", isCurrentlyFavorite);
            icon.classList.toggle("fill-current", !isCurrentlyFavorite);
            icon.classList.toggle("stroke-current", isCurrentlyFavorite);
            button.dataset.favorite = isCurrentlyFavorite ? "false" : "true";
        }

        async function handleFavoriteClick(event, button, icon) {
            event.preventDefault();
            if (button.dataset.processing === "true") return;
            button.dataset.processing = "true";

            let favoritableId = button.dataset.id;
            let favoritableType = button.dataset.type;

            toggleFavorite(button, icon);

            try {
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
                if (!csrfToken) throw new Error("No se encontró el token CSRF en el HTML.");

                let response = await fetch("/favorites/toggle", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ favoritable_id: favoritableId, favoritable_type: favoritableType }),
                });

                if (!response.ok) throw new Error("Error en la respuesta del servidor");

                let data = await response.json();
                console.log("✅ Respuesta del servidor:", data);
            } catch (error) {
                console.error("❌ Error en la solicitud:", error);
                alert("Hubo un error al procesar la solicitud.");
                toggleFavorite(button, icon); // Deshacer cambios si falla
            } finally {
                button.dataset.processing = "false";
            }
        }

        // Agregar eventos a ambos botones (si existen)
        if (desktopButton) {
            let desktopIcon = desktopButton.querySelector("#favorite-icon");
            desktopButton.addEventListener("click", (event) => handleFavoriteClick(event, desktopButton, desktopIcon));
        }

        if (floatingButton) {
            let floatingIcon = floatingButton.querySelector("#floating-favorite-icon");
            floatingButton.addEventListener("click", (event) => handleFavoriteClick(event, floatingButton, floatingIcon));
        }
    });

 
</script>
