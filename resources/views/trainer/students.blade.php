@extends('layouts.main')

@section('title', 'Alumnos del Entrenamiento')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 relative">
    
    <h1 class=" hidden">
        Alumnos de "{{ $training->title }}"
    </h1>
    <h2 class="text-2xl font-semibold mb-4">Tus alumnos
        <span class="ml-2 bg-gray-200 text-gray-700 text-sm font-semibold px-3 py-1 rounded-sm">
            {{ $training->activeStudents->count() }} activos
        </span>
    </h2>
    @if($students->isEmpty())
        <p class="text-gray-500 text-center">No hay alumnos en este entrenamiento.</p>
    @else
        <ul class="bg-white shadow-lg rounded-lg divide-y divide-gray-200">
            @foreach($students as $student)
                <a href="{{ route('trainer.studentDetail', $student->id) }}" class="block">
                    <li class="p-4 hover:bg-gray-50 flex justify-between items-center rounded-md transition cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-14 h-14 rounded-full overflow-hidden  shadow-md">
                                @if($student->profile_pic)
                                    <img src="{{ asset('storage/' . $student->profile_pic) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/default-profile.png') }}" alt="Foto de perfil por defecto" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 hover:text-orange-500 transition">
                                    {{ $student->name }}
                                </p>
                                <p class="text-gray-500 text-sm">{{ $student->email }}</p>
                            </div>
                        </div>
                        <x-lucide-chevron-right class="w-5 h-5 text-gray-400" />
                    </li>
                </a>
            @endforeach
        </ul>
        <div class="mt-6 text-center">
        <a href="{{ route('trainings.detail', $training->id) }}" class="text-orange-500 hover:underline font-semibold">
            Volver a entrenamiento
        </a>
    </div>

        {{-- 🔹 Agregar Paginación Correctamente --}}
        <div class="mt-6">
            {{ $students->links() }}
        </div>
    @endif
</div>

@endsection

<a href="{{ route('trainings.detail', $training->id) }}" class="block">