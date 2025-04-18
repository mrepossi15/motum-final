<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\TrainerReviewMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Training;


class ReviewController extends Controller
{
    public function store(Request $request)
    {
    // Verifica que el usuario tenga el rol de alumno
    if (Auth::user()->role !== 'alumno') {
        abort(403, 'Solo los alumnos pueden dejar reseñas.');
    }

    $request->validate([
        'comment' => 'required|string|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'trainer_id' => 'nullable|exists:users,id',
        'training_id' => 'nullable|exists:trainings,id',
    ]);
    
    $review = Review::create([
        'user_id' => Auth::id(),
        'trainer_id' => $request->trainer_id,
        'training_id' => $request->training_id,
        'comment' => $request->comment,
        'rating' => $request->rating,
    ]);

   // 📩 Enviar email al entrenador
    $trainer = null;

    if ($request->trainer_id) {
        // Si la reseña es directamente para un entrenador
        $trainer = User::find($request->trainer_id);
    } elseif ($request->training_id) {
        // Si la reseña es para un entrenamiento, obtener el entrenador del entrenamiento
        $training = Training::with('trainer')->find($request->training_id);
        if ($training) {
            $trainer = $training->trainer;
        }
    }

    // Si se encontró un entrenador, enviar el email
    if ($trainer && $trainer->email) {
        Mail::to($trainer->email)->send(new TrainerReviewMail($review, $trainer));
    }


    return redirect()->back()->with('review_success', 'Tu reseña se ha enviado correctamente.');
}
public function destroy($id)
{
    $review = Review::findOrFail($id);
    
    // Solo el alumno que creó la reseña o un administrador pueden eliminarla
    if (Auth::id() !== $review->user_id && Auth::user()->role !== 'admin') {
        abort(403, 'No tienes permiso para eliminar esta reseña.');
    }

    $review->delete();

    return back()->with('success', 'Reseña eliminada correctamente.');
}
public function show($trainer_id)
{
    $trainer = User::where('id', $trainer_id)->where('role', 'entrenador')->firstOrFail();
    $reviews = Review::where('trainer_id', $trainer_id)
        ->with('user') // Cargar la relación del usuario que dejó la reseña
        ->latest()
        ->get();
        

    return view('reviews.trainer', compact('trainer', 'reviews'));
}
}

