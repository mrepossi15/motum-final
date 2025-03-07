@extends('layouts.main')

@section('title', 'Tu Información')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 relative">
    <h2 class="text-2xl font-semibold mb-4">Tu información</h2>
    
    <!-- Botón de edición flotante en la esquina superior derecha -->
    <div class="absolute top-4 right-4">
            <!-- 📱 Versión para Móvil (botón negro con icono naranja) -->
            <a href="{{ route('students.edit') }}"
                class="sm:hidden flex items-center space-x-2  text-white px-2 py-2 rounded-lg transition hover:bg-gray-800">
                <x-lucide-pencil class="w-5 h-5 text-orange-500" />
                <span class="sr-only">Editar</span>
            </a>

            <!-- 🖥️ Versión para Tablet y Computadora (link con subrayado en hover) -->
            <a href="{{ route('students.edit') }}"
                class="hidden sm:flex text-orange-500 px-4 py-2 items-center space-x-2 hover:underline transition">
                <x-lucide-pencil class="w-4 h-4" />
                <span>Editar</span>
            </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <ul class="divide-y divide-gray-200">
            <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                <div>
                    <p class="text-gray-900 font-semibold">Nombre completo</p>
                    <p class="text-gray-500 text-sm">{{ $user->name }}</p>
                </div>
            </li>
            <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                <div>
                    <p class="text-gray-900 font-semibold">Correo electrónico</p>
                    <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                </div>
            </li>
            <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                <div>
                    <p class="text-gray-900 font-semibold">Número de teléfono</p>
                    <p class="text-gray-500 text-sm">{{ $user->phone ?? 'No especificado' }}</p>
                </div>
            </li>
            <li class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                <div>
                    <p class="text-gray-900 font-semibold">Fecha de nacimiento</p>
                    <p class="text-gray-500 text-sm">{{ $user->birth ? \Carbon\Carbon::parse($user->birth)->format('d-m-Y') : 'Fecha de nacimiento no especificada' }}</p>
                </div>
            </li>
            <li class="py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                <div>
                    <p class="text-gray-900 font-semibold">Apto médico</p>
                    <p class="text-gray-500 text-sm flex py-2 items-center gap-1">
                        @if ($user->medical_fit)
                            <x-lucide-check-circle class="w-5 h-5 text-green-500" />
                            <span>Cargado</span>
                        @else
                            <x-lucide-x-circle class="w-5 h-5 text-red-500" />
                            <span>No cargado</span>
                        @endif
                    </p>
                </div>
            </li>
        </ul>
    </div>
    <div class="mt-6 text-center">
        <a href="{{ route('students.profile', ['id' => auth()->id()]) }}" class="text-orange-500 hover:underline font-semibold">
            Volver a mi perfil
        </a>
    </div>
</div>
@endsection
