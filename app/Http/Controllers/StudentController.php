<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Park;
use App\Models\Payment;
use App\Models\Activity;
use App\Models\Training;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Traits\HandlesImages;
use App\Models\TrainingSchedule;
use App\Models\TrainingReservation;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;


class StudentController extends Controller
{
    use HandlesImages;
 
    public function registerStudent()
    {
        $activities = Activity::all(); // Obtener todas las actividades disponibles
        return view('auth.register-student', compact('activities'));
    }

    public function storeStudent(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string|max:255|unique:users', // Validar Collector ID
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'profile_pic_description' => 'nullable|string|max:255',
            'birth' => 'date', // Fecha de nacimiento
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
            'medical_fit_description' => 'nullable|string|max:255',
            'activities' => 'nullable|array', // Validar actividades
            'activities.*' => 'exists:activities,id', // Verificar que las actividades existen
            
            
        ]);
    
        $input = $request->all();
    
        // Manejar la subida de la imagen de perfil
        if ($request->hasFile('profile_pic')) {
            $userData['profile_pic'] = $this->resizeAndSaveImage($request->file('profile_pic'), 'profile_pics', 300, 300);
            $userData['profile_pic_description'] = 'Foto de portada del alumno ' . $request->name;
        }
        if ($request->hasFile('medical_fit')) {
            $userData['medical_fit'] = $this->resizeAndSaveImage($request->file('medical_fit'), 'medical_fits', 300, 300);
            $userData['medical_fit_description'] = 'Apto medico del alumno ' . $request->name;
        }
    
        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role' => 'alumno', // Rol automático para alumnos
            'profile_pic' => $input['profile_pic'] ?? null,
            'profile_pic_description' => $input['profile_pic_description'] ?? null,
            'birth' => $request->birth ?? null,
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
            'medical_fit_description' => $input['medical_fit_description'] ?? null,
        ]);
        Mail::to($user->email)->send(new WelcomeMail($user));
        // Vincular actividades seleccionadas
        if ($request->has('activities')) {
            $user->activities()->sync($request->activities);
        }
        // Iniciar sesión automáticamente
        Auth::login($user);
    
        return redirect('/mapa')->with('success', 'Alumno registrado exitosamente.');
    }
    public function studentProfile($id)
    {
        $user = User::findOrFail($id); // Buscar el usuario por ID
        return view('students.profile', compact('user'));
    }
    

    public function editStudentProfile()
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        return view('students.edit', compact('user'));
    }

    public function updateStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'biography' => 'nullable|string|max:1000',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'phone' => 'nullable|string|max:255|unique:users', // Validar Collector ID
            'birth' => 'date',
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);
    
        $user = Auth::user();
        $user->fill($request->only(['name', 'email', 'birth', 'biography', 'phone']));
    
        // 👉 Procesar la imagen de perfil usando el trait
        if ($request->hasFile('profile_pic')) {
            $this->deleteImageIfExists($user->profile_pic);
            $user->profile_pic = $this->resizeAndSaveImage($request->file('profile_pic'), 'profile_pics', 300, 300);
            $user->profile_pic_description = 'Foto de perfil de ' . $user->name;
        }
    
        // 👉 Procesar la imagen del apto médico usando el trait
        if ($request->hasFile('medical_fit')) {
            $this->deleteImageIfExists($user->medical_fit);
            $user->medical_fit = $this->resizeAndSaveImage($request->file('medical_fit'), 'medical_fits', 600, 400);
            $user->medical_fit_description = 'Apto médico de ' . $user->name . ' actualizado';
        }
    
        $user->save();
    
        return redirect()->route('students.profile', ['id' => $user->id])->with('success', 'Perfil actualizado correctamente.');
    }

    public function myTrainings() {
        $userId = Auth::id();
    
        // Obtener los entrenamientos que el alumno ha comprado
        $trainings = Payment::where('user_id', $userId)
            ->with('training')
            ->get()
            ->pluck('training')
            ->unique();
    
        // Obtener las reservas activas del usuario
        $reservations = TrainingReservation::where('user_id', $userId)
            ->with('training')
            ->orderBy('date', 'asc')
            ->get();
    
        return view('student.training.my-trainings', compact('trainings', 'reservations'));
    }

    //Mapa principal
    public function map()
    {
        $activities = Activity::all(); 
        return view('students.map', compact('activities'));
    }

    public function showTrainerProfile($trainerId)
    {
        $trainer = User::where('id', $trainerId)->where('role', 'entrenador')->firstOrFail();

        $parks = $trainer->parks()->get();
        $experiences = $trainer->experiences()->get();
        $trainings = $trainer->trainings()->with(['park', 'activity'])->get();
        $reviews = $trainer->reviews()->with('user')->get(); 
        $hasPurchasedFromTrainer = auth()->check() 
        ? auth()->user()->hasPurchasedFromTrainer($trainer->id) 
        : false;

        return view('students.trainerProfile', compact('trainer', 'parks', 'trainings', 'experiences', 'reviews', 'hasPurchasedFromTrainer'));
    }
    public function detail()
    {
        $user = Auth::user();
        return view('students.info', compact('user'));
    }
    public function trainerTraining()
    {
        // Obtener el entrenador autenticado
        $trainer = Auth::user();

        // Asegurar que el usuario es un entrenador
        if (!$trainer->isTrainer()) {
            return redirect()->route('home')->with('error', 'Acceso denegado.');
        }

        // Obtener los entrenamientos creados por el entrenador
        $trainings = Training::where('trainer_id', $trainer->id)
            ->with(['park', 'activity', 'photos', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->paginate(9); // Paginación de 9 por página

        return view('students.trainerTrainings', compact('trainer', 'trainings'));
    }

}
