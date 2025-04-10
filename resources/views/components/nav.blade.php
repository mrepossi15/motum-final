<div>
    <!-- Navbar (Fijo en la parte superior) -->
    <nav class="fixed top-0 left-0 w-full bg-black z-50 p-2">
        <div class="container mx-auto flex items-center justify-between px-4 py-2">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="text-orange-500 font-semibold italic text-2xl">
                motum
            </a>
            <!-- Toggler para vista móvil -->
            @if (!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('register.student'))
            <button 
                id="navToggler"
                class="md:hidden text-orange-500 focus:outline-none" 
                type="button" 
                aria-label="Toggle navigation">
                <x-lucide-menu class="w-6 h-6 text-orange-500 group-hover:text-orange-500 transition" />
            </button>
            @endif

            <!-- Navbar Links (Visible en pantallas grandes) -->
            <div id="mainNav" class="hidden md:flex items-center space-x-6">
                @guest
                    @if (!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('register.student'))
                        <a href="{{ route('login') }}" class="text-orange-500 hover:bg-gray-700 py-2 px-4 rounded-md {{ request()->routeIs('login') ? 'font-bold' : '' }}">
                            Inicia sesión
                        </a>
                        <a href="{{ route('register') }}" class="bg-orange-500 py-2 px-4 rounded-md text-white">
                            Regístrate
                        </a>
                    @endif
                @else
                    @if (auth()->user()->role === 'entrenador')
                        <a href="{{ route('trainer.calendar') }}" class="text-orange-500 hover:underline">Calendario</a>
                        <a href="{{ route('trainer.profile') }}" class="text-orange-500 hover:underline">Mi Perfil</a>
                        <a href="{{ route('trainer.show-trainings') }}" class="text-orange-500 hover:underline">Mis Entrenamientos</a>
                    @elseif (auth()->user()->role === 'alumno')
                        <a href="{{ route('students.map') }}" class="text-orange-500 hover:underline">Mapa</a>
                        <a href="{{ route('students.profile', ['id' => auth()->id()]) }}" class="text-orange-500 hover:underline">Mi Perfil</a>
                        <a href="{{ route('cart.view') }}" class="text-orange-500 hover:underline">Mi carrito</a>
                        <a href="{{ route('reservations.show') }}" class="text-orange-500 hover:underline">Mis entrenamientos</a>
                        <a href="{{ route('favorites.view') }}" class="text-orange-500 hover:underline">Mis favoritos</a>
                    @endif
                    <form action="{{ route('logout.process') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-orange-500 hover:underline bg-transparent border-0 cursor-pointer">
                            {{ auth()->user()->email }} (Cerrar sesión)
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>
</div>

<!-- Dropdown Menu (Solo visible en mobile) -->
<div 
    id="menuDropdown" 
    class="fixed top-12 left-0 w-full  bg-black text-white z-40 transition-transform transform -translate-y-full md:hidden"
>
    <div class="pt-4"> 
        @guest
            <a href="{{ route('login') }}" class="block px-4 py-3 border-t border-b border-orange-500 text-orange-500 hover:bg-gray-800">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="block px-4 py-3 border-b border-orange-500 text-orange-500 hover:bg-gray-800">Regístrate</a>
        @else
            @if (auth()->user()->role === 'entrenador')
                <a href="{{ route('trainer.calendar') }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Calendario</a>
                <a href="{{ route('trainer.profile') }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Mi Perfil</a>
                <a href="{{ route('trainer.show-trainings') }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Mis Entrenamientos</a>
            @elseif (auth()->user()->role === 'alumno')
                <a href="{{ route('students.map') }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Mapa</a>
                <a href="{{ route('students.profile', ['id' => auth()->id()]) }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Mi Perfil</a>
                <a href="{{ route('cart.view') }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Mi carrito</a>
                <a href="{{ route('reservations.show') }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Mis entrenamientos</a>
                <a href="{{ route('favorites.view') }}" class="block px-4 py-3 hover:bg-gray-800 border-b border-orange-500">Mis favoritos</a>
            @endif
            <form action="{{ route('logout.process') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-white hover:bg-gray-800">Cerrar sesión</button>
            </form>
        @endguest
    </div>
</div>

<!-- Espacio para evitar que el contenido quede oculto por la navbar fija -->
<div class="pt-12"></div>

<!-- Script para el toggler en mobile -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navToggler = document.getElementById('navToggler');
        const menuDropdown = document.getElementById('menuDropdown');
        let isOpen = false;

        navToggler.addEventListener('click', function(event) {
            event.stopPropagation();
            isOpen = !isOpen;
            if (isOpen) {
                menuDropdown.style.transform = 'translateY(0)';
            } else {
                menuDropdown.style.transform = 'translateY(-100%)';
            }
        });

        document.addEventListener('click', function(event) {
            if (!menuDropdown.contains(event.target) && !navToggler.contains(event.target)) {
                isOpen = false;
                menuDropdown.style.transform = 'translateY(-100%)';
            }
        });
    });
</script>