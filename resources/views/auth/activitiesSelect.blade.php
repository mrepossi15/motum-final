@extends('layouts.main')

@section('title', 'Selecciona tus actividades favoritas')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6">
    <h2 class="text-2xl font-semibold mb-4">Selecciona tus actividades favoritas</h2>
    
    <form method="POST" action="{{ route('user.activities') }}">
        @csrf
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($activities as $activity)
                <label class="cursor-pointer flex items-center space-x-2 bg-gray-100 p-3 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-200 transition">
                    <input type="checkbox" name="activities[]" value="{{ $activity->id }}" {{ in_array($activity->id, $user->activities->pluck('id')->toArray()) ? 'checked' : '' }} />
                    <span class="text-gray-700 font-semibold">{{ $activity->name }}</span>
                </label>
            @endforeach
        </div>
        <div class="mt-6 text-center">
            <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-full shadow-md hover:bg-orange-600 transition">
                Guardar preferencias
            </button>
        </div>
    </form>
</div>
@endsection