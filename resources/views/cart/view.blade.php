@extends('layouts.main')

@section('title', 'Carrito de Compras')

@section('content')
<div class="container mx-auto mt-8 p-6 bg-white shadow-lg rounded-lg">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">🛒 Carrito de Compras</h1>

    <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 rounded-lg shadow-sm">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="py-2 px-4 text-left">🏋️ Entrenamiento</th>
                    <th class="py-2 px-4 text-left">👨‍🏫 Entrenador</th>
                    <th class="py-2 px-4 text-left">📆 Sesiones Semanales</th>
                    <th class="py-2 px-4 text-left">💰 Precio</th>
                    <th class="py-2 px-4 text-left">🗑️ Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cartItems as $item)
                    <tr class="border-t">
                        <td class="py-3 px-4">{{ $item->training->title }}</td>
                        <td class="py-3 px-4">{{ $item->training->trainer->name }}</td>
                        <td class="py-3 px-4 text-center">{{ $item->weekly_sessions }}</td>
                        <td class="py-3 px-4 text-green-600 font-semibold">
                            ${{ $item->training->prices->where('weekly_sessions', $item->weekly_sessions)->first()->price }}
                        </td>
                        <td class="py-3 px-4">
                            <form method="POST" action="{{ url('/cart/remove') }}">
                                @csrf
                                <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
                                    ❌ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Tu carrito está vacío.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Opciones de acciones del carrito -->
    @if ($cartItems->isNotEmpty())
        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Vaciar Carrito -->
            <form method="POST" action="{{ url('/cart/clear') }}">
                @csrf
                <button class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">
                    🗑️ Vaciar Carrito
                </button>
            </form>

            <!-- Proceder al Pago -->
            <form method="POST" action="{{ url('/payment/create') }}">
                @csrf
                <button class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition">
                    💳 Proceder al Pago
                </button>
            </form>
        </div>
    @endif

    <!-- Botón Atrás -->
    <div class="mt-6">
        <a href="{{ url()->previous() }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
            ⬅️ Atrás
        </a>
    </div>
</div>
@endsection