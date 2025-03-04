@extends('layouts.main')

@section('title', 'Carrito de Compras')

@section('content')
<div class="flex justify-center min-h-screen text-black bg-gray-100">
    <div class="w-full max-w-7xl mx-auto p-4 lg:px-10">

        <!-- 🛒 Encabezado -->
        <h1 class="hidden text-2xl font-bold text-gray-800 mb-4">🛒 Carrito de Compras</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 pb-24 md:pb-6">
            <!-- 📦 Lista de Productos en el Carrito -->
            <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-4">
                @forelse ($cartItems as $item)
                    <div class="border-b py-4 flex flex-col sm:flex-row sm:items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- 📋 Detalles del Entrenamiento -->
                            <div>
                                <p class="text-gray-900 font-semibold">{{ $item->training->title }}</p>
                                <p class="text-gray-500 text-sm">Entrenador: {{ $item->training->trainer->name }}</p>
                                <p class="text-gray-500 text-sm">Sesiones: <strong>{{ $item->weekly_sessions }} {{ $item->weekly_sessions == 1 ? 'vez' : 'veces' }} por semana </strong></p>

                                <div class="flex space-x-4 text-blue-600 mt-2">
                                    <!-- 🗑 Eliminar -->
                                    <a href="#" class="text-red-500 hover:underline"
                                       onclick="event.preventDefault(); document.getElementById('remove-cart-item-{{ $item->id }}').submit();">
                                        Eliminar
                                    </a>
                                    <!-- ✏️ Modificar -->
                                    <a href="{{ route('trainings.selected', $item->training->id) }}" class="hover:underline text-orange-500">
                                        Modificar
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 💰 Precio -->
                        <div class="text-right mt-4 sm:mt-0">
                            <p class="text-orange-600 font-bold text-lg">
                                ${{ number_format($item->training->prices->where('weekly_sessions', $item->weekly_sessions)->first()->price, 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- 🗑 Formulario oculto para eliminar cada producto -->
                    <form id="remove-cart-item-{{ $item->id }}" method="POST" action="{{ url('/cart/remove') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                    </form>
                @empty
                    <p class="text-gray-500 text-center py-6">Tu carrito está vacío.</p>
                @endforelse

                <!-- 📦 Sección de Envío (Solo si hay productos en el carrito) -->
                @if ($cartItems->isNotEmpty())
                    <div class="py-4 border-t">
                        <a href="#" class="text-gray-700 font-medium underline hover:text-red-600 transition"
                           onclick="event.preventDefault(); document.getElementById('clear-cart-form').submit();">
                            Vaciar carrito
                        </a>
                    </div>
                @endif
            </div>

            <!-- 🛍️ Resumen de Compra -->
            <div class="bg-white shadow-lg rounded-lg p-4 
                        md:sticky md:top-4 md:self-start h-auto w-full 
                        fixed bottom-0 left-0 md:relative z-50 md:z-auto border-t md:border-none">
                <h2 class="text-lg font-semibold mb-3">Resumen de compra</h2>

                <div class="border-b pb-3">
                    <div class="flex justify-between text-gray-700">
                        <span>Producto</span>
                        <span>${{ number_format($cartTotal ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>Descuentos</span>
                        <span>-$0</span>
                    </div>
                </div>

                <div class="flex justify-between font-bold text-lg mt-3">
                    <span>Total</span>
                    <span>${{ number_format($cartTotal ?? 0, 2) }}</span>
                </div>

                <!-- Solo mostrar el botón de pago si hay productos -->
                @if ($cartItems->isNotEmpty())
                    <form method="POST" action="{{ url('/payment/create') }}">
                        @csrf
                        <button class="bg-orange-500 text-white font-semibold w-full py-3 rounded-md mt-4 hover:bg-orange-600 transition">
                            Proceder al pago
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 🗑 Formulario oculto para vaciar el carrito -->
@if ($cartItems->isNotEmpty())
    <form id="clear-cart-form" method="POST" action="{{ url('/cart/clear') }}" class="hidden">
        @csrf
    </form>
@endif
@endsection