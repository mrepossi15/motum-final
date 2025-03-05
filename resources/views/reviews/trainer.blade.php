@extends('layouts.main')

@section('title', 'Reseñas de ' . $trainer->name)

@section('content')

<div class="max-w-4xl mx-auto p-4 mt-6">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Reseñas de {{ $trainer->name }}</h2>

    @if($reviews->isEmpty())
        <p class="text-gray-500 text-center">Este entrenador aún no tiene reseñas.</p>
    @else
        <div class="bg-white rounded-lg shadow-md p-4">
            <ul class="divide-y divide-gray-200">
                @foreach ($reviews as $review)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-white text-lg">
                                {{ substr($review->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $review->user->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="text-gray-700">{{ $review->comment }}</p>
                            <p class="text-yellow-500 text-lg">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 text-center">
        <a href="{{ route('trainer.profile', ['id' => $trainer->id]) }}" class="text-orange-500 hover:underline font-semibold">
            Volver al perfil de {{ $trainer->name }}
        </a>
    </div>
</div>
@endsection