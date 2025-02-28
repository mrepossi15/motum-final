<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
     //////VISTA LOGIN  
    public function loginForm()
    {
        return view('auth.login');
    }
    ////// LOGIN 
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar si el email existe en la base de datos
        $userExists = \App\Models\User::where('email', $request->email)->exists();

        if ($userExists) {
            // Intentar autenticar con las credenciales proporcionadas
            if (Auth::attempt($request->only('email', 'password'))) {
                // Redirigir según el rol del usuario autenticado
                $role = Auth::user()->role;

                if ($role === 'entrenador') {
                    return redirect()->route('trainer.calendar');
                } elseif ($role === 'alumno') {
                    return redirect()->route('students.map');
                }
            }

            // Si el email existe pero la contraseña es incorrecta
            return back()->withErrors(['password' => 'La contraseña es incorrecta'])
                        ->withInput($request->only('email')); // Retener el email
        }

        // Si el email no existe, devolver error genérico
        return back()->withErrors(['email' => 'Mail no registrado']);
    }
    ////// LOGOUT
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente.');
    } 
    
}


