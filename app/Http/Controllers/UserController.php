<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

class UserController extends Controller
{
     //////VISTA LOGIN  
     public function home()
     {
         return view('home');
     }
     public function register()
     {
         return view('auth.register');
     }
    public function loginForm()
    {
        return view('auth.login');
    }
    ////// LOGIN 
    public function login(Request $request)
    {
        // Validación con mensajes personalizados
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El mail es obligatorio.',
            'email.email' => 'El mail no tiene un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);
    
        // Verificar si el email existe en la base de datos
        $userExists = \App\Models\User::where('email', $request->email)->exists();
    
        if ($userExists) {
            // Intentar autenticar con las credenciales proporcionadas
            if (Auth::attempt($request->only('email', 'password'))) {
                $role = Auth::user()->role;
    
                if ($role === 'entrenador') {
                    return redirect()->route('trainer.calendar');
                } elseif ($role === 'alumno') {
                    return redirect()->route('students.map');
                }
            }
    
            // Si el email existe pero la contraseña es incorrecta
            return back()->withErrors([
                'password' => 'La contraseña es incorrecta.'
            ])->withInput($request->only('email'));
        }
    
        // Si el email no existe
        return back()->withErrors([
            'email' => 'Mail no registrado.'
        ])->withInput();
    }
    ////// LOGOUT
    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('success', 'Sesión cerrada exitosamente.');
    } 
    public function storeActivities(Request $request)
    {
        $user = auth()->user();
        $user->activities()->sync($request->activities); // Vincula actividades

        return redirect()->route('students.profile', ['id' => auth()->id()])->with('success', 'Tus actividades han sido guardadas.');
        
    }

    public function showActivities()
    {
        $activities = Activity::all(); // Obtener todas las actividades disponibles
        $user = auth()->user();

        return view('auth.activitiesSelect', compact('activities', 'user'));
    }
    public function checkUserExists(Request $request)
    {
        $nameExists = \App\Models\User::where('name', $request->name)->exists();
        $emailExists = \App\Models\User::where('email', $request->email)->exists();
        $phoneExists = \App\Models\User::where('phone', $request->phone)->exists();
        
        return response()->json([
            'name' => $nameExists,
            'email' => $emailExists,
            'phone' => $phoneExists,
        ]);
    }
    
}


