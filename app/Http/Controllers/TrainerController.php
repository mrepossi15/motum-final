<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\HandlesImages;
use App\Models\User;
use App\Models\Training;
use App\Models\TrainingStatus;
use App\Models\Payment;
use App\Models\TrainingSchedule;
use Illuminate\Support\Facades\Http;
use App\Models\Park;
use App\Models\UserExperience;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\ParkReview;




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
            'phone' => 'required|string|max:255|unique:users', // Validar Collector ID
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
            'rating' => 'nullable|numeric|min:0|max:5',
           
        ]);
        // dd('Validación realizada correctamente');
        $userData = $request->only([
            'name', 'email',  'mercado_pago_email','password', 'certification', 'biography', 'especialty', 'birth','phone'
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
                    'rating' => $request->rating, // ✅ Guardar la calificación
                ]
            );
            $reviews = json_decode($request->reviews, true);
        if (is_array($reviews)) {
            foreach ($reviews as $review) {
                ParkReview::create([
                    'park_id' => $park->id,
                    'author' => $review['author'],
                    'rating' => $review['rating'],
                    'text' => $review['text'],
                    'time' => $review['time'],
                ]);
            }
        }
        
        
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

    public function getTrainingsByPark(Request $request)
    {
        // Validate the input
        $request->validate([
            'park_id' => 'required|exists:parks,id',
            'selected_day' => 'required|string',
            'selected_date' => 'required|date',
        ]);
    
        // Get the park and day from the request
        $parkId = $request->query('park_id');
        $selectedDay = $request->query('selected_day');
        $selectedDate = $request->query('selected_date'); // selected date passed from the frontend
    
        // We will use strtotime to calculate the correct date for the selected day based on selected_date
        $trainings = TrainingSchedule::with(['training', 'exceptions', 'statuses'])
            ->whereHas('training', function ($query) use ($parkId) {
                $query->where('trainer_id', auth()->id())
                      ->where('park_id', $parkId);
            })
            ->where('day', $selectedDay)
            ->get()
            ->map(function ($schedule) use ($selectedDate, $selectedDay) {
                // Get the index of the day in the week (e.g., Lunes -> 0, Martes -> 1, etc.)
                $daysOfWeek = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
                $dayIndex = array_search($schedule->day, $daysOfWeek);
    
                // If the day isn't found, skip it
                if ($dayIndex === false) return null;
    
                // Calculate the date for this day of the selected week
                $trainingDate = date('Y-m-d', strtotime("$selectedDate +$dayIndex days"));
    
                // Log the calculated training date for debugging
                Log::info("Fecha de entrenamiento calculada: " . $trainingDate);
    
                // Check if the class is suspended for the calculated date
                $isSuspended = TrainingStatus::where('training_schedule_id', $schedule->id)
                    ->where('date', $trainingDate)
                    ->where('status', 'suspended')
                    ->exists();
    
                // If suspended, skip this class
                if ($isSuspended) {
                    return null;
                }
    
                // Look for any exceptions for the calculated date
                $exception = $schedule->exceptions->firstWhere('date', $trainingDate);
    
                if ($exception) {
                    Log::info("Excepción encontrada para la clase en la fecha $trainingDate: "
                        . "Inicio: " . $exception->start_time . ", Fin: " . $exception->end_time);
                    return [
                        'id'          => $schedule->id,
                        'training_id' => $schedule->training_id,
                        'date'        => $trainingDate,
                        'day'         => $schedule->day,
                        'title'       => $schedule->training->title,
                        'start_time'  => $exception->start_time,
                        'end_time'    => $exception->end_time,
                        'status'      => $exception->status, // 'modified' or 'cancelled'
                        'is_exception'=> true,
                    ];
                }
    
                // If no exception, return the original schedule
                Log::info("Horario original para la clase en la fecha $trainingDate: "
                    . "Inicio: " . $schedule->start_time . ", Fin: " . $schedule->end_time);
                return [
                    'id'          => $schedule->id,
                    'training_id' => $schedule->training_id,
                    'date'        => $trainingDate,
                    'day'         => $schedule->day,
                    'title'       => $schedule->training->title,
                    'start_time'  => $schedule->start_time,
                    'end_time'    => $schedule->end_time,
                    'status'      => 'active', // Normal schedule
                    'is_exception'=> false,
                ];
            })
            ->filter() // Filter out null values (suspended classes)
            ->values(); // Reindex the results
    
        // Log the number of filtered trainings for debugging
        Log::info("Entrenamientos obtenidos: " . $trainings->count() . " clases.");
    
        return response()->json($trainings);
    }
    
    public function showTrainerProfile()
    {
        $trainer = auth()->user();  // Obtener al entrenador autenticado
    
        // Recuperar parques asociados al entrenador
        $parks = $trainer->parks()->get();
    
        // Obtener experiencias asociadas al entrenador desde el modelo UserExperience
        $experiences = $trainer->experiences()->get();  // Usar el método correcto para obtener la colección
    
        // Recuperar otros datos como entrenamientos y fotos
        $trainings = $trainer->trainings()->with(['park', 'activity', 'schedules'])->get();
        $trainingPhotos = $trainer->trainingPhotos;
    
        // Pasar todos los datos a la vista
        return view('trainer.profile', compact('trainer', 'parks', 'trainings', 'trainingPhotos', 'experiences'));
    }
   
    public function editTrainerProfile()
    {
        $trainer = auth()->user(); // Obtener al entrenador autenticado
        return view('trainer.edit', compact('trainer')); // Retornar la vista del formulario
    }
   
    public function updateTrainer(Request $request)
{
    try {
        Log::info('🚀 Iniciando actualización del perfil del entrenador', ['user_id' => auth()->id()]);

        // Verificar qué datos llegan en la solicitud
        Log::info('📩 Datos recibidos en la solicitud:', $request->all());

        // Validación de datos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'mercado_pago_email' => 'nullable|email|unique:users,mercado_pago_email,' . auth()->id(),
            'phone' => 'nullable|string|max:255|unique:users,phone,' . auth()->id(),
            'certification' => 'nullable|string|max:255',
            'biography' => 'nullable|string|max:500',
            'especialty' => 'nullable|string|max:255',
            'birth' => 'nullable|date',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'certification_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        Log::info('✅ Validación completada con éxito.');

        $user = auth()->user();

        // Datos a actualizar
        $data = [
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'mercado_pago_email' => $request->mercado_pago_email ?? $user->mercado_pago_email,
            'phone' => $request->phone ?? $user->phone,
            'certification' => $request->certification ?? $user->certification,
            'biography' => $request->biography ?? $user->biography,
            'especialty' => $request->especialty ?? $user->especialty,
            'birth' => $request->birth ?? $user->birth,
        ];
        // Manejo de imágenes
        $imageFields = [
            'profile_pic' => 'profile_pics',
            'medical_fit' => 'medical_fits',
            'certification_pic' => 'certification_pics',
        ];

        foreach ($imageFields as $field => $folder) {
            if ($request->hasFile($field)) {
                Log::info("📸 Subiendo nueva imagen para {$field} del usuario ID: {$user->id}");

                // Eliminar imagen anterior si existe
                if ($user->$field && Storage::disk('public')->exists($user->$field)) {
                    Log::info("🗑 Eliminando imagen antigua de {$field}: {$user->$field}");
                    Storage::disk('public')->delete($user->$field);
                }

                // Guardar nueva imagen redimensionada
                $data[$field] = $this->resizeAndSaveImage($request->file($field), $folder, 600, 400);
                Log::info("✅ {$field} actualizada en: {$data[$field]}");
            }
        }

        // Actualizar el usuario en una sola consulta
        $user->update($data);
        Log::info('✅ Datos del entrenador actualizados.');

        // Actualizar experiencias laborales si existen
        if ($request->has('experiences') && is_array($request->experiences)) {
            Log::info("💼 Eliminando experiencias antiguas del usuario ID: {$user->id}");
            $user->experiences()->delete();

            foreach ($request->experiences as $experience) {
                if (!isset($experience['role']) || !isset($experience['year_start']) || !isset($experience['year_end'])) {
                    continue; // Evita errores si faltan datos obligatorios
                }

                Log::info("➕ Agregando experiencia laboral: " . json_encode($experience));

                $user->experiences()->create([
                    'role' => $experience['role'],
                    'company' => $experience['company'] ?? null,
                    'year_start' => $experience['year_start'],
                    'year_end' => $experience['year_end'],
                    'details' => $experience['details'] ?? null,
                ]);
            }
        }

        Log::info("✅ Perfil del entrenador actualizado correctamente, redirigiendo...");
        return redirect()->route('trainer.profile')->with('success', 'Perfil actualizado exitosamente.');
        
    } catch (\Exception $e) {
        Log::error('🚨 Error durante la actualización del perfil', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return redirect()->back()->with('error', 'Error al actualizar el perfil.');
    }
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

        return view('trainer.show-trainings', compact('trainings'));
    }
    public function indexExperience()
    {
        $experiences = UserExperience::where('user_id', auth()->id())->orderBy('year_start', 'desc')->get();
        return view('trainer.experience', compact('experiences'));
    }
    
    public function storeExperience(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'year_start' => 'required|integer|min:1900|max:' . now()->year,
            'year_end' => 'nullable|integer|min:1900|max:' . now()->year,
            'currently_working' => 'boolean',
        ]);
    
        $validated['user_id'] = auth()->id();
        $validated['year_end'] = $request->currently_working ? null : $request->year_end;
    
        UserExperience::create($validated);
    
        return redirect()->route('trainer.experience')->with('success', 'Experiencia guardada correctamente.');
    }
    
    public function editExperience($id)
    {
        $experience = UserExperience::where('user_id', auth()->id())->findOrFail($id);
        return response()->json($experience);
    }
    
    public function updateExperience(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'year_start' => 'required|integer|min:1900|max:' . now()->year,
            'year_end' => 'nullable|integer|min:1900|max:' . now()->year,
            'currently_working' => 'boolean',
        ]);
    
        $experience = UserExperience::where('user_id', auth()->id())->findOrFail($id);
        $validated['year_end'] = $request->currently_working ? null : $request->year_end;
    
        $experience->update($validated);
    
        return redirect()->route('trainer.experience')->with('success', 'Experiencia actualizada exitosamente.');
    }
    
    public function destroyExperience($id)
    {
        $experience = UserExperience::where('user_id', auth()->id())->findOrFail($id);
        $experience->delete();
    
        return redirect()->route('trainer.experience')->with('success', 'Experiencia eliminada correctamente.');
    }
    public function myPark()
    {
        $parks = Park::all(); // Obtener todos los parques asociados
        return view('trainer.parks', compact('parks'));
    }
    public function trainerPayments()
    {
        $trainerId = Auth::id(); // Obtener el ID del entrenador autenticado
        
        $payments = Payment::whereHas('training', function ($query) use ($trainerId) {
            $query->where('trainer_id', $trainerId);
        })->with(['user', 'training'])->orderBy('created_at', 'desc')->get();
        
        return view('trainer.payments', compact('payments'));
    }
    public function detail()
    {
        $user = Auth::user();
        return view('trainer.info', compact('user'));
    }
    public function students($training_id)
    {
        $training = Training::findOrFail($training_id);
        $students = $training->students()->paginate(10); // 🔹 Aquí aplicamos la paginación correctamente
    
        return view('trainer.students', compact('training', 'students'));
    }

    public function studentDetail($student_id)
    {
        $student = User::with(['payments.training'])->findOrFail($student_id);
    
        // Filtrar solo los entrenamientos que tienen relación con este entrenador
        $trainings = $student->payments->map(function ($payment) {
            return $payment->training;
        })->unique(); // Elimina entrenamientos duplicados
    
        return view('trainer.studentDetail', compact('student', 'trainings'));
    }
    
}
