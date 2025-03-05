<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Payment\MercadoPagoPayment;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $user = auth()->user();

        if (!$user->medical_fit) {
            return redirect()->route('student.profile')->with('error', 'Debes subir tu apto médico antes de comprar un entrenamiento.');
        }

        $cartItems = Cart::with('training.trainer')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'El carrito está vacío.'], 422);
        }

        $mercadoPago = new MercadoPagoPayment();
        $items = [];

        foreach ($cartItems as $item) {
            $price = (float) $item->training->prices->where('weekly_sessions', $item->weekly_sessions)->first()->price;
            $items[] = [
                'title' => $item->training->title,
                'quantity' => 1,
                'unit_price' => $price,
                'currency_id' => 'ARS',
            ];
        }

        $mercadoPago->setItems($items);
        $mercadoPago->setBackUrls(
            success: url('/payment/success'),
            pending: url('/payment/pending'),
            failure: url('/payment/failure')
        );

        $totalAmount = array_sum(array_column($items, 'unit_price'));

        // 🔹 Simulación de pago al entrenador en la base de datos
        $payment = Payment::create([
            'user_id' => $user->id,
            'training_id' => $cartItems->first()->training->id,
            'total_amount' => $totalAmount,
            'company_fee' => $totalAmount * 0.05, // 5% de comisión
            'trainer_amount' => $totalAmount * 0.95, // 95% simulado
            'status' => 'pending',
            'payment_id' => null,
            'external_reference' => (string) uniqid(),
        ]);

        Log::info("📌 External Reference generado: {$payment->external_reference}");

        try {
            $preference = $mercadoPago->createPreference();

            return redirect($preference->init_point);
        } catch (\Exception $e) {
            Log::error('❌ Error al procesar el pago:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Hubo un problema al procesar el pago.'], 500);
        }
    }

    public function success(Request $request)
    {
        return redirect('/mis-entrenamientos')->with('success', 'El pago se realizó con éxito.');
    }

    public function failure()
    {
        return redirect('/cart/view')->with('error', 'Hubo un problema con el pago.');
    }

    public function pending()
    {
        return redirect('/cart/view')->with('warning', 'El pago está pendiente de confirmación.');
    }
    public function dashboard()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();
    
        // Obtener los pagos del usuario con la información del entrenamiento
        $payments = Payment::where('user_id', $user->id)
                    ->with('training') // Carga los datos del entrenamiento relacionado
                    ->orderBy('created_at', 'desc')
                    ->get();
    
        // Retornar la vista con los datos
        return view('payments.dashboard', compact('payments'));
    }

    
}