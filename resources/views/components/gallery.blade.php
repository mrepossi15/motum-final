@props(['photos' => [], 'title' => 'Galería de Imágenes'])

<div class="relative mx-auto lg:px-[25%] w-full">
    <h2 class="text-center text-xl font-bold mb-4">{{ $title }}</h2>

    @if (!empty($photos))
        <div x-data="{ 
            activeSlide: 0, 
            slides: @json($photos),
            showModal: false,
            next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
            prev() { this.activeSlide = this.activeSlide === 0 ? this.slides.length - 1 : this.activeSlide - 1 }
        }" class="relative w-full my-3">

            <!-- 🖥️ Modo Escritorio -->
            <div class="hidden lg:grid grid-cols-10 gap-2">
                <div class="col-span-7">
                    <img :src="slides[0]" 
                         alt="Imagen principal" 
                         class="w-full h-[350px] object-cover cursor-pointer"
                         @click="showModal = true; activeSlide = 0">
                </div>

                <div class="col-span-3 grid grid-rows-2 gap-2">
                    <template x-for="(photo, index) in slides.slice(1, 4)" :key="index">
                        <img :src="photo"
                             alt="Imagen secundaria"
                             class="w-full h-[170px] object-cover cursor-pointer"
                             @click="showModal = true; activeSlide = index + 1">
                    </template>
                </div>
            </div>

            <!-- 📱 Modo Móvil -->
            <div class="lg:hidden relative w-full">
                <img :src="slides[activeSlide]" 
                     alt="Imagen en el carrusel" 
                     class="w-full h-[300px] object-cover"
                     @touchstart="touchStartX = $event.touches[0].clientX" 
                     @touchend="touchEndX = $event.changedTouches[0].clientX; 
                               let diff = touchStartX - touchEndX;
                               if (Math.abs(diff) > 50) { diff > 0 ? next() : prev(); }">

                <!-- Indicadores -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    <template x-for="(photo, index) in slides" :key="index">
                        <button @click="activeSlide = index"
                            :class="activeSlide === index ? 'bg-orange-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full transition-all"></button>
                    </template>
                </div>
            </div>

            <!-- 📸 Modal de Imágenes -->
            <template x-if="showModal">
                <div class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center" @click="showModal = false">
                    <div class="relative w-full max-w-4xl mx-auto shadow-lg" @click.stop>
                        
                        <!-- ❌ Cerrar -->
                        <button class="absolute top-4 right-4 p-2 rounded-full bg-white shadow-md" 
                                @click="showModal = false">
                            ✖
                        </button>

                        <!-- 📸 Imagen en Modal -->
                        <div class="relative">
                            <img :src="slides[activeSlide]" 
                                 alt="Foto en modal" 
                                 class="w-full max-h-[80vh] object-contain">

                            <!-- ⬅ Anterior -->
                            <button class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                    @click.stop="prev()">
                                ⬅
                            </button>

                            <!-- ➡ Siguiente -->
                            <button class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md" 
                                    @click.stop="next()">
                                ➡
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @else
        <p class="text-center text-gray-500">No hay imágenes disponibles.</p>
    @endif
</div>