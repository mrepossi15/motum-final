<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'weekly_sessions' => 'required|integer|min:1',
        ]);
    
        Cart::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'training_id' => $request->training_id,
            ],
            [
                'weekly_sessions' => $request->weekly_sessions,
            ]
        );
    
        return redirect()->back()->with('cart_success', 'El entrenamiento ha sido agregado al carrito.');
    }

    public function viewCart()
    {
        // Obtener los ítems del carrito del usuario autenticado
        $cartItems = Cart::with(['training.trainer', 'training.prices'])->where('user_id', auth()->id())->get();
    
        // Calcular el total del carrito
        $cartTotal = $cartItems->sum(function ($item) {
            $price = optional($item->training->prices->where('weekly_sessions', $item->weekly_sessions)->first())->price ?? 0;
            return $price * ($item->quantity ?? 1);
        });
    
        return view('cart.view', compact('cartItems', 'cartTotal'));
    }
    
    public function remove(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:carts,id',
        ]);

        Cart::where('id', $request->cart_item_id)
            ->where('user_id', auth()->id())
            ->delete();

        return redirect()->back()->with('success', 'Ítem eliminado del carrito.');
    }

    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return redirect()->back()->with('success', 'Carrito vaciado correctamente.');
    }

}