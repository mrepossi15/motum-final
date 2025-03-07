@extends('layouts.main')

@section('title', '')

@section('content')
<div class="flex justify-center min-h-screen bg-gray-100">

    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">
        <h2 class="text-2xl font-semibold mb-4">Mi perfil</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-24 md:pb-6"> 
            <div class="bg-white shadow-lg rounded-lg p-4 md:sticky md:top-4 md:self-start w-full md:relative border-t md:border-none">
                <div class="border-b align-center pb-4">
                    <div class="flex items-center gap-6">
                        <!-- Imagen de Perfil -->
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-orange-300 shadow-md">
                            @if($trainer->profile_pic)
                            <img src="{{ Storage::url($trainer->profile_pic) }}" alt="Foto de perfil" class="w-full h-full object-cover">
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
                <div class="flex flex-wrap gap-2 mt-3">
                    <p class="text-gray-500">{{ $trainer->certification }}</p>
                        @foreach($trainer->activities as $activity)
                        <p class="text-gray-500">Especialidades</p>
                            <span class="bg-orange-500 text-white px-3 py-1 rounded-md text-sm">
                                {{ $activity->name }}
                            </span>
                        @endforeach
                    </div>
            </div>

            <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-4 relative">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-semibold">Sobre {{ $trainer->name }}</h2>
                    
                    <a href="{{ route('trainer.edit') }}" class= "text-orange-500 px-4 py-2 flex items-center space-x-2 hover:underline transition">
                        <x-lucide-pencil class="w-4 h-4" />
                        <span>Editar</span>
                    </a>
                
                </div>
                <p class="text-gray-600 border-b pb-10">{{ $trainer->biography ?? 'No especificada' }}</p>
                <ul class="divide-y divide-gray-200">
                    <li class="flex items-center justify-between py-4 cursor-pointer" onclick="window.location='{{ route('trainer.info') }}';">
                        <div class="flex items-center space-x-4">
                            <x-lucide-user class="text-orange-500 w-6 h-6" />
                            <div>
                                <p class="font-semibold">Tu información</p>
                                <p class="text-sm text-gray-500">Nombre elegido y datos para identificarte.</p>
                            </div>
                        </div>
                        <x-lucide-chevron-right class="text-gray-400 w-5 h-5" />
                    </li>
                    <li class="flex items-center justify-between py-4 cursor-pointer" onclick="window.location='{{ route('trainer.experience') }}';">
                        <div class="flex items-center space-x-4">
                            <x-lucide-briefcase class="text-orange-500 w-6 h-6" />
                            <div>
                                <p class="font-semibold">Tu experiencia</p>
                                <p class="text-sm text-gray-500">Reseñas de tus alumnos sobre vos.</p>
                            </div>
                        </div>
                        <x-lucide-chevron-right class="text-gray-400 w-5 h-5" />
                    </li>
                    <li class="flex items-center justify-between py-4 cursor-pointer" onclick="window.location='{{ route('reviews.trainer', ['trainer' => $trainer->id]) }}';">
                        <div class="flex items-center space-x-4">
                            <x-lucide-star class="text-orange-500 w-6 h-6" />
                            <div>
                                <p class="font-semibold">Tus reseñas</p>
                                <p class="text-sm text-gray-500">Reseñas de tus alumnos sobre vos.</p>
                            </div>
                        </div>
                        <x-lucide-chevron-right class="text-gray-400 w-5 h-5" />
                    </li>
                    <li class="flex items-center justify-between py-4 cursor-pointer" onclick="window.location='{{ route('trainer.parks') }}';">
                        <div class="flex items-center space-x-4">
                            <x-lucide-trees class="text-orange-500 w-6 h-6" />
                            <div>
                                <p class="font-semibold">Tus parques</p>
                                <p class="text-sm text-gray-500">Parques donde entrenas.</p>
                            </div>
                        </div>
                        <x-lucide-chevron-right class="text-gray-400 w-5 h-5" />
                    </li>
                    <li class="flex items-center justify-between py-4 cursor-pointer" onclick="window.location='{{ route('trainer.show-trainings') }}';">
                        <div class="flex items-center space-x-4">
                            <x-lucide-dumbbell class="text-orange-500 w-6 h-6" />
                            <div>
                                <p class="font-semibold">Tus entrenamientos</p>
                                <p class="text-sm text-gray-500">Historial y próximas clases reservadas.</p>
                            </div>
                        </div>
                        <x-lucide-chevron-right class="text-gray-400 w-5 h-5" />
                    </li>
                    <li class="flex items-center justify-between py-4 cursor-pointer" onclick="window.location='{{ route('trainer.payments') }}';">
                        <div class="flex items-center space-x-4">
                            <x-lucide-dollar-sign class="text-orange-500 w-6 h-6" />
                            <div>
                                <p class="font-semibold">Tus pagos</p>
                                <p class="text-sm text-gray-500">Consulta tu historial de pagos y facturación.</p>
                            </div>
                        </div>
                        <x-lucide-chevron-right class="text-gray-400 w-5 h-5" />
                    </li>
                    <li class="flex items-center justify-between py-4 cursor-pointer">
                        <form action="{{ route('logout.process') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center justify-between w-full text-left bg-transparent border-0 cursor-pointer focus:outline-none">
                                <div class="flex items-center space-x-4">
                                    <x-lucide-log-out class="text-orange-500 w-6 h-6" />
                                    <div>
                                        <p class="font-semibold">Cerrar sesión</p>
                                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                <x-lucide-chevron-right class="text-gray-400 w-5 h-5" />
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<form id="clear-cart-form" method="POST" action="#" class="hidden">
</form>
@endsection

