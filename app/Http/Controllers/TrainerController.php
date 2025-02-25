<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\HandlesImages;
use App\Models\User;
use App\Models\Training;
use App\Models\Payment;
use App\Models\TrainingSchedule;
use Illuminate\Support\Facades\Http;
use App\Models\Park;
use App\Models\UserExperience;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;



class TrainerController extends Controller
{
    use HandlesImages;

    //////REGISTRO ENTRENADOR
    public function registerTrainer()
    {
        return view('auth.register-trainer');
    }

    public function storeTrainer(Request $request)
    {
     
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mercado_pago_email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'certification' => 'required|string|max:255',
            'biography' => 'nullable|string|max:500',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'park_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'opening_hours' => 'nullable|string',
            'especialty' => 'nullable|string|max:255',
            'birth' => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'profile_pic_description' => 'nullable|string|max:255',
            'certification_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'certification_pic_description' => 'nullable|string|max:255',
            'experiences' => 'nullable|array',
            'experiences.*.role' => 'nullable|string|max:255',
            'experiences.*.company' => 'nullable|string|max:255',
            'experiences.*.year_start' => 'nullable|integer|min:1900|max:' . now()->year,
            'experiences.*.year_end' => 'nullable|integer|min:1900|max:' . now()->year,
            'experiences.*.currently_working' => 'nullable|boolean',
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
            'medical_fit_description' => 'nullable|string|max:355',
            'photo_reference' => 'nullable|array',
           
        ]);
        // dd('Validación realizada correctamente');
        $userData = $request->only([
            'name', 'email',  'mercado_pago_email','password', 'certification', 'biography', 'especialty', 'birth',
        ]);
        $userData['role'] = 'entrenador';
        $userData['password'] = Hash::make($request->password);
    
        if ($request->hasFile('profile_pic')) {
            $userData['profile_pic'] = $this->resizeAndSaveImage($request->file('profile_pic'), 'profile_pics', 300, 300);
            $userData['profile_pic_description'] = 'Foto de portada del entrenador ' . $request->name;
        }
        if ($request->hasFile('medical_fit')) {
            $userData['medical_fit'] = $this->resizeAndSaveImage($request->file('medical_fit'), 'medical_fits', 300, 300);
            $userData['medical_fit_description'] = 'Apto medico del entrenador ' . $request->name;
        }
        if ($request->hasFile('certification_pic')) {
            $userData['certification_pic'] = $this->resizeAndSaveImage($request->file('certification_pic'), 'certification_pics', 300, 300);
            $userData['certification_pic_description'] = 'Certificación del entrenador ' . $request->name;
        }
       
        // Convertir `photo_references` de JSON a array
        $photoReferences = json_decode($request->photo_references, true);

        if (!is_array($photoReferences)) {
            $photoReferences = [];
        }

        // Descargar y guardar hasta 4 fotos
        $photoUrls = [];
        foreach (array_slice($photoReferences, 0, 4) as $photoReference) {
            try {
                
        
                $imageContents = Http::get($photoReference)->body();
        
                if (empty($imageContents)) {
                    continue;
                }
        
                $imageName = 'parks/' . uniqid() . '.jpg';
                Storage::disk('public')->put($imageName, $imageContents);
        
                $photoUrls[] = Storage::url($imageName);
            } catch (\Exception $e) {
                
            }
        }

        
            // Guardar parque con fotos en la base de datos
            $park = Park::firstOrCreate(
                ['name' => $request->park_name],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'location' => $request->location,
                    'opening_hours' => $request->opening_hours,
                    'photo_urls' => json_encode($photoUrls), // Guardar como JSON
                ]
            );
        
        
            $user = User::create($userData);
            
            Mail::to($user->email)->send(new WelcomeMail($user));

            Auth::login($user);
        
            $user->parks()->attach($park->id);
            if ($request->has('experiences')) {
                foreach ($request->experiences as $experience) {
                    $user->experiences()->create([
                        'role' => $experience['role'],
                        'company' => $experience['company'] ?? null,
                        'year_start' => $experience['year_start'],
                        'year_end' => $experience['currently_working'] ? null : $experience['year_end'],
                        'currently_working' => $experience['currently_working'] ?? false,
                    ]);
                }
            }
    
        return redirect('/entrenador/calendario')->with('success', 'Entrenador registrado exitosamente.');
    }

    public function calendar(Request $request)
    {
        $user = auth()->user();

        // Verifica si el usuario tiene parques asociados
        $parks = $user->parks;

        if ($parks->isEmpty()) {
            return redirect()->route('trainer.calendar')->with('error', 'No tienes parques asociados.');
        }

        // Lógica del calendario
        $startOfWeek = Carbon::now()->startOfWeek();
        $groupedTrainings = []; // Carga aquí los entrenamientos agrupados si es necesario

        return view('trainer.calendar', compact('user', 'startOfWeek', 'groupedTrainings', 'parks'));
    }
    // Filtrar el calendario por parque
    public function getTrainingsByPark(Request $request)
    {
        $request->validate([
            'park_id' => 'nullable|exists:parks,id', // Hacer park_id opcional
        ]);

        // Base de la consulta: entrenamientos del entrenador autenticado
        $query = Training::with(['schedules', 'activity', 'prices'])
            ->where('trainer_id', Auth::id());

        // Filtrar por parque si park_id está presente
        if ($request->park_id) {
            $query->where('park_id', $request->park_id);
        }

        // Ejecutar la consulta y ordenar por fecha de creación
        $trainings = $query->orderBy('created_at', 'desc')->get();

        return response()->json($trainings);
    }

   

    public function getTrainingsForWeek(Request $request)
{
    $weekStartDate = $request->query('week_start_date');
    if (!$weekStartDate || !strtotime($weekStartDate)) {
        return response()->json(['error' => 'Fecha de inicio de semana inválida.'], 400);
    }

    $daysOfWeek = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];

    $trainings = TrainingSchedule::with(['training', 'exceptions'])
        ->whereHas('training', function ($query) {
            $query->where('trainer_id', auth()->id());
        })
        ->get()
        ->map(function ($schedule) use ($weekStartDate, $daysOfWeek) {
            $dayIndex = array_search($schedule->day, $daysOfWeek);
            if ($dayIndex === false) return null;

            $trainingDate = date('Y-m-d', strtotime("$weekStartDate +$dayIndex days"));

            // Buscar excepción para la fecha específica
            $exception = $schedule->exceptions->firstWhere('date', $trainingDate);

            // Priorizar excepción si existe
            $startTime = $exception ? $exception->start_time : $schedule->start_time;
            $endTime   = $exception ? $exception->end_time   : $schedule->end_time;
            $status    = $exception ? $exception->status     : 'active';

            // Omitir entrenamientos cancelados
            if ($status === 'cancelled') {
                return null;
            }

            return [
                'id'          => $schedule->id,
                'training_id' => $schedule->training_id,
                'date'        => $trainingDate,
                'day'         => $schedule->day,
                'title'       => $schedule->training->title,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'status'      => $status,
                'is_exception'=> (bool)$exception,
            ];
        })
        ->filter()
        ->sortBy('start_time')  // 🔑 Ordenar por hora de inicio
        ->values();

    return response()->json($trainings);
}
    






    public function showTrainerProfile()
    {
        // Usuario autenticado
        $trainer = auth()->user();
    
        // Parques asociados al entrenador
        $parks = $trainer->parks()->get();
        
    
        // Entrenamientos asociados al entrenador
        $trainings = $trainer->trainings()->with(['park', 'activity', 'schedules'])->get();
    
        // Fotos asociadas a los entrenamientos del entrenador
        $trainingPhotos = $trainer->trainingPhotos;
    
        return view('trainer.profile', compact('trainer', 'parks', 'trainings', 'trainingPhotos'));
    }

    public function showTrainerTrainings()
    {
        $trainings = Training::where('trainer_id', Auth::id())
            ->with(['schedules', 'photos', 'park', 'activity'])
            ->get()
            ->map(function ($training) {
                // Contar cuántos usuarios distintos compraron este entrenamiento
                $training->student_count = Payment::where('training_id', $training->id)
                    ->distinct('user_id') // Evitar contar múltiples compras del mismo usuario
                    ->count('user_id');
                return $training;
            });

        return view('trainer.index', compact('trainings'));
    }
    
  
    
}
