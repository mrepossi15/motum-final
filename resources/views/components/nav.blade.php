<div>
    <!-- Navbar -->
    <nav class="bg-black border-b border-gray-700">
        <div class="container mx-auto flex items-center justify-between px-4 py-2">
            <!-- Logo y Botón Toggler -->
            <a href="{{ route('login') }}" class="text-orange-500 font-semibold italic text-2xl">
                motum
            </a>
            <!-- Toggler for Mobile View -->
            <button 
                class="md:hidden text-orange-500 focus:outline-none" 
                type="button" 
                aria-label="Toggle navigation" 
                onclick="document.getElementById('mainNav').classList.toggle('hidden')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <!-- Navbar Links -->
            <div id="mainNav" class="hidden lg:flex items-center space-x-6">
                <ul class="flex flex-col lg:flex-row lg:space-x-6 items-center">
                    @guest
                        <!-- Enlaces para invitados -->
                        <li>
                            <a href="{{ route('login') }}" class="text-orange-500 hover:underline">
                                Iniciar sesión
                            </a>
                        </li>
                    @else
                        <!-- Enlaces para usuarios autenticados -->
                        @if (auth()->user()->role === 'entrenador')
                            <li>
                                <a href="{{ route('trainer.calendar') }}" class="text-orange-500 hover:underline {{ request()->routeIs('trainer.calendar') ? 'font-bold' : '' }}">
                                    Calendario
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('trainer.profile') }}" class="text-orange-500 hover:underline {{ request()->routeIs('trainer.profile') ? 'font-bold' : '' }}">
                                    Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('trainer.show-trainings') }}" class="text-orange-500 hover:underline {{ request()->routeIs('trainer.show-trainings') ? 'font-bold' : '' }}">
                                    Mis Entrenamientos
                                </a>
                            </li>
                        @elseif (auth()->user()->role === 'alumno')
                            <li>
                                <a href="{{ route('students.map') }}" class="text-orange-500 hover:underline {{ request()->routeIs('students.map') ? 'font-bold' : '' }}">
                                    Mi carrito
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('students.profile', ['id' => auth()->id()]) }}"
                                class="text-orange-500 hover:underline {{ request()->routeIs('students.profile') ? 'font-bold' : '' }}">
                                    Mi Perfil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('cart.view') }}" class="text-orange-500 hover:underline {{ request()->routeIs('cart.view') ? 'font-bold' : '' }}">
                                    Mapa
                                </a>
                             </li>

                            
                        @endif

                        <!-- Cerrar sesión -->
                        <li>
                            <form action="{{ route('logout.process') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-orange-500 hover:underline bg-transparent border-0 cursor-pointer">
                                    {{ auth()->user()->email }} (Cerrar sesión)
                                </button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</div>

<!-- Script para el toggler en mobile -->
<script>
    document.getElementById('navToggler').addEventListener('click', function() {
        document.getElementById('mainNav').classList.toggle('hidden');
    });
</script>
