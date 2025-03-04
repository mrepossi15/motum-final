<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\Activity;
use App\Models\Park;
use App\Models\TrainingPhoto;
use App\Models\TrainingSchedule;
use App\Models\TrainingException;
use App\Models\TrainingPrice;
use App\Models\TrainingStatus;
use App\Models\Payment;
use Carbon\Carbon;
use App\Traits\HandlesImages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TrainingReservation;
use App\Mail\TrainingCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrainingSuspendedMail;


class TrainingController extends Controller
{    
    use HandlesImages;
  
    public function create(Request $request)
    {
        $selectedParkId = $request->query('park_id'); // Obtén el parque seleccionado, si se pasa en la URL
        $parks = Auth::user()->parks; // Obtener todos los parques del entrenador
        $activities = Activity::all(); // Todas las actividades disponibles

        return view('trainings.create', compact('parks', 'selectedParkId', 'activities'));
    }
    public function store(Request $request)
    {
        if (!Auth::user()->medical_fit) {
            return redirect()->back()->with('error', 'Debes subir un apto médico antes de crear un entrenamiento.');
        }
    
        // Validación
        $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'park_id'            => 'required|exists:parks,id',
            'activity_id'        => 'required|exists:activities,id',
            'level'              => 'required|in:Principiante,Intermedio,Avanzado',
            'photos.*'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photos_description.*' => 'nullable|string|max:255',
            'schedule.days'      => 'required|array',
            'schedule.days.*'    => 'required|array|min:1',
            'schedule.start_time.*' => 'required|date_format:H:i',
            'schedule.end_time.*' => 'required|date_format:H:i',
            'prices.weekly_sessions.*' => 'required|integer|min:1',
            'prices.price.*'       => 'required|numeric|min:0',
            'available_spots'    => 'required|integer|min:1',
        ]);
        // Validar si el parque existe y pertenece al usuario
        $park = Park::find($request->park_id);
        if (!$park || !$park->users->contains(Auth::user())) {
            return redirect()->back()->with('error', 'El parque no es válido o no está asociado a tu cuenta.');
        }
            
        // Verificar horarios válidos
        foreach ($request->schedule['start_time'] as $index => $startTime) {
            $endTime = $request->schedule['end_time'][$index];
            if (strtotime($startTime) >= strtotime($endTime)) {
                return redirect()->back()->with('error', "La hora de fin debe ser posterior a la hora de inicio en el horario #" . ($index + 1));
            }
        }
    
        // Crear el entrenamiento
        $training = Training::create([
            'trainer_id'      => Auth::id(),
            'park_id'         => $request->park_id,
            'activity_id'     => $request->activity_id,
            'title'           => $request->title,
            'description'     => $request->description,
            'level'           => $request->level,
            'available_spots' => $request->available_spots,
        ]);
    
        if (!$training) {
            return redirect()->back()->with('error', 'Error al crear el entrenamiento.');
        }
    
        // Manejo de imágenes
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                if ($photo->isValid()) {
                    Log::info("📸 Procesando imagen: {$photo->getClientOriginalName()}");
        
                    $imagePath = $this->resizeAndSaveImage($photo, 'training_photos', 800, 600);
                    Log::info("✅ Imagen guardada en: $imagePath");
        
                    TrainingPhoto::create([
                        'training_id' => $training->id,
                        'photo_path'  => str_replace('storage/', '', $imagePath),
                        'training_photos_description' => $request->input('photos_description')[$index] ?? 'Foto de entrenamiento',
                    ]);
                } else {
                    Log::error("🚫 Imagen no válida: {$photo->getClientOriginalName()}");
                }
            }
        } else {
            Log::warning("⚠️ No se detectaron imágenes en la solicitud.");
        }
    
        // Guardar horarios
        foreach ($request->schedule['days'] as $index => $dayGroups) {
            foreach ($dayGroups as $days) {
                foreach ($days as $day) {
                    TrainingSchedule::create([
                        'training_id' => $training->id,
                        'day'         => $day,
                        'start_time'  => $request->schedule['start_time'][$index] ?? '00:00',
                        'end_time'    => $request->schedule['end_time'][$index] ?? '00:00',
                    ]);
                }
            }
        }
    
        // Guardar precios
        foreach ($request->prices['weekly_sessions'] as $index => $sessions) {
            TrainingPrice::create([
                'training_id'     => $training->id,
                'weekly_sessions' => $sessions,
                'price'           => $request->prices['price'][$index],
            ]);
        }
    
        // Enviar correo de confirmación
        Mail::to(Auth::user()->email)->send(new TrainingCreatedMail(Auth::user(), $training));
    
        return redirect()->route('trainer.calendar')->with('success', 'Entrenamiento creado exitosamente.');
    }
    public function show(Request $request, $id)
    {   
        Log::info("🚀 Iniciando show() para entrenamiento ID: {$id}", [
            'selectedDate' => $request->query('date'),
            'selectedTime' => $request->query('time'),
            'now' => now()->format('Y-m-d H:i:s'),
        ]);
    
        $selectedDate = $request->query('date');
        $selectedTime = $request->query('time');
    
        // 🔄 Cargar entrenamiento con todas sus relaciones
        $training = Training::with([
            'trainer',
            'park',
            'activity',
            'schedules',
            'schedules.exceptions', // Relación con excepciones
            'prices',
            'students',
            'reservations.user',
            'photos'
        ])->findOrFail($id);
    
        // 🔍 Buscar excepciones para la fecha seleccionada
        $filteredSchedules = $training->schedules;
    
        if ($selectedDate) {
            $exceptions = TrainingException::whereIn('training_schedule_id', $training->schedules->pluck('id'))
                ->where('date', $selectedDate)
                ->get()
                ->keyBy('training_schedule_id');
    
            Log::info("📌 Excepciones encontradas para {$selectedDate}:", $exceptions->toArray());
    
            // ✅ Obtener el horario correcto (priorizando la excepción si existe)
            $selectedSchedule = null;
    
            foreach ($training->schedules as $schedule) {
                $exception = $exceptions->get($schedule->id);
    
                // Si hay una excepción, tomar esa
                if ($exception) {
                    $selectedSchedule = (object) [
                        'id' => $schedule->id,
                        'day' => $schedule->day,
                        'start_time' => $exception->start_time,
                        'end_time' => $exception->end_time,
                        'is_exception' => true,
                        'status' => $exception->status
                    ];
                    break; // Tomamos solo la primera excepción encontrada
                }
            }
    
            // Si no hay excepción, tomar el primer horario original
            if (!$selectedSchedule) {
                $selectedSchedule = $training->schedules->first();
            }
    
            // Pasar solo un horario a la vista
            $filteredSchedules = collect([$selectedSchedule]);
        }
    
        Log::info("✅ Horario final después de aplicar excepciones:", $filteredSchedules->toArray());
    
        // 📌 Verificar si hay reservas en esta fecha
        $filteredReservations = $selectedDate
            ? $training->reservations->where('date', $selectedDate)->groupBy('time') 
            : collect([]);
    
        // 📌 Generar URL para detalle de reservas
        $reservationDetailUrl = route('reservations.attendance', [
            'id' => $training->id,
            'date' => $selectedDate,
            'time' => $selectedTime
        ]);
    
        // 📌 **Verificar si la clase es accesible**
        $isClassAccessible = false;
        $accessMessage = "Fecha u hora no especificadas";
    
        if ($selectedDate && $selectedTime) {
            $classStartTime = Carbon::parse("$selectedDate $selectedTime");
            $classEndTime = $classStartTime->copy()->addHours(24);
            $now = now();
    
            if ($now->lessThan($classStartTime)) {
                $accessMessage = "No disponible";
            } elseif ($now->between($classStartTime, $classEndTime)) {
                $isClassAccessible = true;
                $accessMessage = "Lista disponible ahora.";
            } else {
                $accessMessage = "Acceso cerrado";
            }
        }
    
        Log::info('🔍 Verificación de acceso:', [
            'selectedDate' => $selectedDate,
            'selectedTime' => $selectedTime,
            'classStartTime' => $classStartTime ?? null,
            'classEndTime' => $classEndTime ?? null,
            'isClassAccessible' => $isClassAccessible,
            'now' => now()->toDateTimeString(),
            'accessMessage' => $accessMessage,
        ]);
    
        // 📌 Definir vista según el rol
        $role = auth()->user()->role;
        $view = ($role === 'entrenador' || $role === 'admin') ? 'trainings.show' : 'student.show-training';
    
        return view($view, compact(
            'training', 'filteredSchedules', 'selectedTime', 'selectedDate', 'filteredReservations',
            'isClassAccessible', 'accessMessage', 'reservationDetailUrl'
        ));
    }
   
    public function edit(Request $request, $id)
    {
        $training = Training::with(['schedules.exceptions', 'prices'])->findOrFail($id);
        $selectedDate = $request->query('date') ?? now()->toDateString();
    
        // Convertir el nombre del día a español
        $dayName = ucfirst(Carbon::parse($selectedDate)->locale('es')->isoFormat('dddd'));
    
        Log::info('🚀 Iniciando edición', [
            'training_id' => $id,
            'selected_date' => $selectedDate,
            'day_name' => $dayName
        ]);
    
        // Obtener horarios base y excepciones
        $filteredSchedules = $training->schedules->map(function ($schedule) use ($selectedDate, $dayName) {
            $exception = $schedule->exceptions->firstWhere('date', $selectedDate);
    
            Log::info('🔬 Procesando horario:', [
                'schedule_id' => $schedule->id,
                'day' => $schedule->day,
                'base_start' => $schedule->start_time,
                'base_end' => $schedule->end_time,
                'exception' => $exception ? $exception->toArray() : 'Ninguna'
            ]);
    
            return (object) [
                'id'          => $schedule->id,
                'day'         => $schedule->day,
                'start_time'  => $exception ? $exception->start_time : $schedule->start_time,
                'end_time'    => $exception ? $exception->end_time   : $schedule->end_time,
                'is_exception'=> $exception ? true : false,
            ];
        })->filter(function ($schedule) use ($dayName) {
            return strtolower($schedule->day) === strtolower($dayName);
        });
    
        Log::info('👉 Horarios filtrados:', $filteredSchedules->toArray());
    
        // Si no hay horarios base, buscar excepciones directas
        if ($filteredSchedules->isEmpty()) {
            $exceptions = TrainingException::whereHas('schedule.training', function ($query) use ($id) {
                $query->where('id', $id);
            })->where('date', $selectedDate)->get();
    
            Log::info('🔍 Excepciones encontradas para la fecha:', $exceptions->toArray());
    
            foreach ($exceptions as $exception) {
                $filteredSchedules->push((object) [
                    'id'           => $exception->training_schedule_id,
                    'day'          => $dayName,
                    'start_time'   => $exception->start_time,
                    'end_time'     => $exception->end_time,
                    'is_exception' => true,
                ]);
            }
        }
    
        Log::info('✅ Horarios finales para mostrar:', $filteredSchedules->toArray());
    
        // Cargar actividades y parques
        $activities = Activity::all();
        $parks = Park::all();
    
        return view('trainings.edit', compact('training', 'activities', 'parks', 'filteredSchedules', 'selectedDate'));
    }
    
    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);
    
        try {
            Log::info('🚀 Iniciando actualización del entrenamiento', ['training_id' => $id]);
    
            $selectedDate = $request->input('selected_date');
            Log::info('📅 Fecha seleccionada:', ['selected_date' => $selectedDate]);
    
            // ✅ Verificar si los horarios existen en la solicitud
            if (!$request->has('schedule.start_time') || !$request->has('schedule.end_time')) {
                Log::error("🚨 Error: No se enviaron horarios desde el formulario.");
                return redirect()->back()->with('error', 'Debes ingresar los horarios.');
            }
    
            Log::info("✅ Datos recibidos en la solicitud:", [
                'schedule_id' => $request->input('schedule_id'),
                'start_time' => $request->input('schedule.start_time'),
                'end_time' => $request->input('schedule.end_time'),
            ]);
    
            foreach ($request->input('schedule_id') as $scheduleId) {
                // 🔥 **Usar el ID como índice en lugar del $index**
                $startTime = $request->input("schedule.start_time.$scheduleId");
                $endTime = $request->input("schedule.end_time.$scheduleId");
    
                // 🚨 **Verificar que no sean `null` antes de guardar**
                if (empty($startTime) || empty($endTime)) {
                    Log::error("🚨 Error: Horarios vacíos en schedule_id $scheduleId", [
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                    continue;
                }
    
                TrainingException::updateOrCreate(
                    [
                        'training_schedule_id' => $scheduleId,
                        'date' => $selectedDate,
                    ],
                    [
                        'start_time' => "$selectedDate $startTime", // ✅ Asegura formato correcto
                        'end_time'   => "$selectedDate $endTime",
                        'status'     => 'modified',
                    ]
                );
    
                Log::info("✅ Excepción creada para el $selectedDate: $startTime - $endTime");
            }
    
            return redirect()->route('trainings.show', [
                'id' => $training->id,
                'date' => $selectedDate,
                'time' => $request->input('schedule.start_time.0') // Agrega el nuevo horario actualizado
            ])->with('success', 'Entrenamiento actualizado solo para la fecha seleccionada.');
    
        } catch (\Exception $e) {
            Log::error('❌ Error durante la actualización', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al actualizar el entrenamiento.');
        }
    }
    public function editAll(Request $request, $id)
    {
        $training = Training::with(['trainer', 'park', 'activity', 'schedules', 'prices'])->findOrFail($id);

    

        $selectedDay = ucfirst(strtolower($request->query('day')));

        if ($training->schedules->isNotEmpty()) {
            $filteredSchedules = $selectedDay
                ? $training->schedules->filter(fn($schedule) => strtolower($schedule->day) === strtolower($selectedDay))
                : $training->schedules;
        } else {
            $filteredSchedules = collect();
        }

        $activities = Activity::all();
        $parks = Park::all();

        return view('trainings.editAll', compact('training', 'activities', 'parks', 'filteredSchedules', 'selectedDay'));
    }
    public function updateAll(Request $request, $id)
    {
        // Normalizar horarios al formato H:i
        if ($request->has('schedule.start_time')) {
            $startTimes = array_map(fn($time) => date('H:i', strtotime($time)), $request->schedule['start_time']);
            $request->merge(['schedule.start_time' => $startTimes]);
        }

        if ($request->has('schedule.end_time')) {
            $endTimes = array_map(fn($time) => date('H:i', strtotime($time)), $request->schedule['end_time']);
            $request->merge(['schedule.end_time' => $endTimes]);
        }

        // Validar los datos principales del entrenamiento
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:Principiante,Intermedio,Avanzado',
            'activity_id' => 'required|exists:activities,id',
            'park_id' => 'required|exists:parks,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg',
            'photos_description.*' => 'nullable|string|max:255',
            'available_spots' => 'required|integer|min:1', // Validar cupos disponibles como un entero mínimo de 1
            
        ]);

        // Validar los horarios (schedules) y precios
        $request->validate([
            'schedule.days.*' => 'nullable|array',
            'schedule.start_time.*' => 'required|string', // Relajar para cualquier string
            'schedule.end_time.*' => 'required|string|after:schedule.start_time.*',
            'prices.weekly_sessions.*' => 'nullable|integer|min:1',
            'prices.price.*' => 'nullable|numeric|min:0',
        ]);

        // Buscar el entrenamiento
        $training = Training::findOrFail($id);

        // Verificar permisos (opcional)
        if (auth()->id() !== $training->trainer_id) {
            abort(403, 'No tienes permiso para editar este entrenamiento.');
        }

        // Actualizar los datos principales
        $training->update($validated);

        // Actualizar horarios
        $training->schedules()->delete(); // Borrar horarios existentes
        if ($request->has('schedule.days')) {
            foreach ($request->schedule['days'] as $index => $days) {
                foreach ($days as $day) {
                    $training->schedules()->create([
                        'day' => $day,
                        'start_time' => $request->schedule['start_time'][$index],
                        'end_time' => $request->schedule['end_time'][$index],
                    ]);
                }
            }
        }

        // Actualizar precios
        $training->prices()->delete(); // Borrar precios existentes
        if ($request->has('prices.weekly_sessions')) {
            foreach ($request->prices['weekly_sessions'] as $index => $weekly_sessions) {
                $training->prices()->create([
                    'weekly_sessions' => $weekly_sessions,
                    'price' => $request->prices['price'][$index],
                ]);
            }
        }
        // Manejar la subida de nuevas imágenes
        if ($request->hasFile('photos')) {
            // Eliminar las fotos existentes asociadas al entrenamiento
            foreach ($training->photos as $existingPhoto) {
                if (\Storage::disk('public')->exists($existingPhoto->photo_path)) {
                    \Storage::disk('public')->delete($existingPhoto->photo_path); // Eliminar la foto del disco
                }
                $existingPhoto->delete(); // Eliminar el registro de la base de datos
            }
        
            // Manejar la nueva foto
            foreach ($request->file('photos') as $photo) {
                $imagePath = 'training_photos/' . uniqid() . '.' . $photo->getClientOriginalExtension();
        
                // Redimensionar la imagen
                $resizedImage = Image::make($photo)->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
        
                // Guardar solo la imagen redimensionada
                $resizedImage->save(storage_path('app/public/' . $imagePath));
        
                // Registrar en la base de datos
                TrainingPhoto::create([
                    'training_id' => $training->id,
                    'photo_path' => $imagePath,
                    'training_photos_description' => $request->photos_description, // Usar la descripción del campo hidden
                ]);
            }
        }
        return redirect()->route('trainings.detail', $training->id)
                        ->with('success', 'Entrenamiento actualizado con éxito.');
    }

    public function destroy(Request $request, $id)
    {
            $selectedDate = $request->query('date'); 
        $selectedTime = $request->query('time');

        if (!$selectedDate || !$selectedTime) {
            return redirect()->back()->with('error', 'No se especificó una fecha y horario válidos.');
        }

        // 🔍 Buscar la clase (`TrainingSchedule`) exacta en esa fecha y hora
        $schedule = TrainingSchedule::where('training_id', $id)
            ->where('start_time', $selectedTime)
            ->first();

        if (!$schedule) {
            return redirect()->back()->with('error', 'No se encontró ninguna clase para eliminar.');
        }

        // 🔍 Buscar y eliminar las reservas asociadas a esa clase en esa fecha
        $reservations = TrainingReservation::where('training_id', $id)
            ->where('date', $selectedDate)
            ->where('time', $selectedTime)
            ->get();

        if ($reservations->count() > 0) {
            foreach ($reservations as $reservation) {
                $reservation->delete();
            }
        }

        // ✅ Eliminar la clase
        $schedule->delete();

        return redirect()->route('trainer.calendar')->with('success', 'Clase y reservas eliminadas con éxito.');
    }

    public function destroyAll($id)
    {
        // Buscar el entrenamiento
        $training = Training::findOrFail($id);

        // Verificar permisos (opcional)
        if (auth()->id() !== $training->trainer_id) {
            return redirect()->route('trainer.calendar')->with('error', 'No tienes permiso para eliminar este entrenamiento.');
        }
        // Eliminar las fotos asociadas al entrenamiento
        foreach ($training->photos as $photo) {
            $photoPath = $photo->photo_path;

            // Verificar si el archivo existe y eliminarlo
            if (\Storage::disk('public')->exists($photoPath)) {
                \Storage::disk('public')->delete($photoPath);
            }

            // Eliminar el registro de la foto en la base de datos
            $photo->delete();
        }

        // Eliminar el entrenamiento
        $training->delete();

        return redirect()->route('trainer.calendar')->with('success', 'Entrenamiento eliminado con éxito.');
    }

     //Cambia el estado de la clase a suspendida
     public function suspendClass(Request $request)
     {
         Log::info("🚀 Datos recibidos en suspendClass", [
             'training_id' => $request->training_id,
             'date' => $request->date
         ]);
 
         $trainingId = $request->training_id;
         $trainingDate = $request->date;
 
         // 🚨 Verificar si la fecha es válida
         if (!$trainingDate || !strtotime($trainingDate)) {
             return response()->json(['error' => 'Fecha inválida', 'received' => $trainingDate], 400);
         }
 
         // ✅ Obtener el nombre del día en español a partir de la fecha
         $dayOfWeek = ucfirst(\Carbon\Carbon::parse($trainingDate)->locale('es')->translatedFormat('l'));
 
         Log::info("📅 Día calculado para la fecha $trainingDate: $dayOfWeek");
 
         // ✅ Buscar el horario (`training_schedule_id`) correspondiente al `training_id` en ese día
         $schedule = TrainingSchedule::where('training_id', $trainingId)
             ->where('day', $dayOfWeek) // Comparar con el nombre del día
             ->first();
 
         if (!$schedule) {
             Log::error("🚨 No se encontró un training_schedule_id para training_id={$trainingId} en el día {$dayOfWeek}.");
             return response()->json([
                 'error' => 'No se encontró el horario de entrenamiento para la fecha seleccionada',
                 'dayOfWeek' => $dayOfWeek,
                 'training_id' => $trainingId
             ], 400);
         }
 
         // ✅ Guardar en la tabla `training_status`
         TrainingStatus::updateOrCreate(
             [
                 'training_schedule_id' => $schedule->id,
                 'date' => $trainingDate
             ],
             [
                 'status' => 'suspended'
             ]
         );
 
         Log::info("✅ Clase suspendida con éxito para training_schedule_id={$schedule->id} en fecha {$trainingDate}");
        // ✅ Obtener alumnos inscritos
        $students = TrainingReservation::where('training_id', $trainingId)
        ->where('date', $trainingDate)
        ->pluck('user_id');

        if ($students->isEmpty()) {
        Log::info("🚨 No hay alumnos inscritos para notificar.");
        } else {
        $emails = \App\Models\User::whereIn('id', $students)->pluck('email');

        foreach ($emails as $email) {
            Mail::to($email)->send(new TrainingSuspendedMail($schedule->training, $trainingDate));
        }

        Log::info("📩 Se enviaron correos a los alumnos inscritos.");
        }

         $deletedReservations = TrainingReservation::where('training_id', $trainingId)
         ->where('date', $trainingDate)
         ->delete();
 
         Log::info("🗑️ Se eliminaron {$deletedReservations} reservas para training_id={$trainingId} en fecha {$trainingDate}");
 
         return redirect()->route('trainer.calendar')->with('success', 'Clase suspendida con éxito y reservas eliminadas.');
     }

     public function getTrainingsForWeek(Request $request)
     {
         $weekStartDate = $request->query('week_start_date');
     
         if (!$weekStartDate || !strtotime($weekStartDate)) {
             return response()->json(['error' => 'Fecha de inicio de semana inválida.'], 400);
         }
     
         $daysOfWeek = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
     
         // Obtener el filtro de parque si existe en la solicitud
         $parkId = $request->query('park_id');
     
         // Obtener los entrenamientos de la semana con el filtro por parque (si existe)
         $trainings = TrainingSchedule::with(['training', 'exceptions', 'statuses'])
             ->whereHas('training', function ($query) use ($parkId) {
                 $query->where('trainer_id', auth()->id());
     
                 // Aplicar filtro por parque, si está presente
                 if ($parkId) {
                     $query->where('park_id', $parkId);
                 }
             })
             ->get()
             ->map(function ($schedule) use ($weekStartDate, $daysOfWeek) {
                 $dayIndex = array_search($schedule->day, $daysOfWeek);
                 if ($dayIndex === false) return null;
     
                 // Calcular la fecha de esa semana
                 $trainingDate = date('Y-m-d', strtotime("$weekStartDate +$dayIndex days"));
     
                 // Log para verificar cómo se está calculando la fecha de la clase
                 Log::info("Fecha de entrenamiento calculada: " . $trainingDate);
     
                 // Verificar si la clase está suspendida
                 $isSuspended = TrainingStatus::where('training_schedule_id', $schedule->id)
                     ->where('date', $trainingDate)
                     ->where('status', 'suspended')
                     ->exists();
     
                 // Si la clase está suspendida, no mostrarla
                 if ($isSuspended) {
                     return null;
                 }
     
                 // Buscar si hay una excepción para esa fecha
                 $exception = $schedule->exceptions->firstWhere('date', $trainingDate);
     
                 // Si hay una excepción, mostrarla con el horario de la excepción
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
                         'status'      => $exception->status, // 'modified' o 'cancelled'
                         'is_exception'=> true,
                     ];
                 }
     
                 // Si no hay excepción, mostrar el horario original
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
                     'status'      => 'active', // Horario normal
                     'is_exception'=> false,
                 ];
             })
             ->filter() // Filtrar clases que no deben mostrarse (suspendidas)
             ->values(); // Reindexar los resultados
     
         // Log para verificar el número de entrenamientos obtenidos
         Log::info("Entrenamientos obtenidos: " . $trainings->count() . " clases.");
     
         return response()->json($trainings);
     }

    ////////////// POV ALUMNOS 

   
    public function select(Request $request, $id)
    {
        $user = auth()->user();
        $training = Training::with(['trainer', 'park', 'activity', 'schedules', 'prices', 'reviews.user', 'photos'])
            ->findOrFail($id);

        ;

        $hasPurchased = false;
        if ($user) {
            $hasPurchased = \App\Models\Payment::where('user_id', $user->id)
                ->where('training_id', $training->id)
                ->exists();
        }

        $isFavorite = false;
        if ($user) {
            $isFavorite = \App\Models\Favorite::where('user_id', $user->id)
                ->where('favoritable_id', $training->id)
                ->where('favoritable_type', Training::class)
                ->exists();
        }

        $selectedDay = $request->query('day'); 
        $filteredSchedules = $selectedDay
            ? $training->schedules->filter(fn($schedule) => $schedule->day === $selectedDay)
            : $training->schedules;

        $role = auth()->user()->role;

        if ($role === 'entrenador' || $role === 'admin') {
            return view('trainer.show', compact('training', 'filteredSchedules', 'selectedDay', 'isFavorite'));
        } else {
            return view('trainings.selected', compact('training', 'filteredSchedules', 'selectedDay', 'hasPurchased', 'isFavorite'));
        }
    }
    public function showTrainings(Request $request, $parkId, $activityId)
    {
        // Buscar el parque y la actividad
        $park = Park::findOrFail($parkId);
        $activity = Activity::findOrFail($activityId);
    
        // Obtener los filtros del request
        $selectedDays = $request->input('day') ? explode(',', $request->input('day')) : [];
        $selectedHours = $request->input('start_time') ? explode(',', $request->input('start_time')) : [];
        $selectedLevels = $request->input('level') ? explode(',', $request->input('level')) : [];
    
        // Filtrar entrenamientos
        $query = Training::where('park_id', $park->id)
            ->where('activity_id', $activityId)
            ->with(['trainer', 'activity', 'schedules']);
    
        // Aplicar filtro de días
        if (!empty($selectedDays)) {
            $query->whereHas('schedules', function ($q) use ($selectedDays) {
                $q->whereIn('day', $selectedDays);
            });
        }
    
        // Aplicar filtro de horarios
        if (!empty($selectedHours)) {
            $query->whereHas('schedules', function ($q) use ($selectedHours) {
                $q->where(function ($subQuery) use ($selectedHours) {
                    foreach ($selectedHours as $hour) {
                        $startRange = date('H:i:s', strtotime($hour));
                        $endRange = date('H:i:s', strtotime($hour . ' +59 minutes'));
                        $subQuery->orWhereBetween('start_time', [$startRange, $endRange]);
                    }
                });
            });
        }
    
        // Aplicar filtro de nivel
        if (!empty($selectedLevels)) {
            $query->whereIn('level', $selectedLevels);
        }
    
        // Paginar los entrenamientos (21 por página)
        $trainings = $query->paginate(21);
    
        // Obtener lista de favoritos del usuario autenticado
        $favorites = auth()->check()
            ? auth()->user()->favorites()->where('favoritable_type', Training::class)->pluck('favoritable_id')->toArray()
            : [];
    
        // Lista de días de la semana y niveles
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $levels = ['Principiante', 'Intermedio', 'Avanzado'];
    
        return view('trainings.catalog', compact(
            'park',
            'activity',
            'trainings',
            'daysOfWeek',
            'levels',
            'selectedDays',
            'selectedHours',
            'selectedLevels',
            'favorites'
        ));
    }

  
    public function showAll(Request $request, $id)
    {   
        $selectedDate = $request->query('date'); 
        $selectedTime = $request->query('time');
    
        $training = Training::with([
            'trainer',
            'park',
            'activity',
            'schedules',
            'prices',
            'students',
            'reservations.user' 
        ])->findOrFail($id);
    
        $training->refresh();
    
        $selectedDay = $request->query('day');
        $filteredSchedules = $training->schedules;
    
        if ($selectedDay) {
            $filteredSchedules = $filteredSchedules->filter(fn($schedule) => $schedule->day === $selectedDay);
        }
    
        if ($selectedDate) {
            $filteredSchedules = $filteredSchedules->filter(function ($schedule) use ($selectedDate) {
                return !TrainingStatus::where('training_schedule_id', $schedule->id)
                    ->where('date', $selectedDate)
                    ->where('status', 'suspended')
                    ->exists();
            });
        }
    
        $filteredReservations = $selectedDate
            ? $training->reservations->where('date', $selectedDate)->groupBy('time') 
            : collect([]);
    
        if ($selectedTime) {
            $filteredSchedules = $filteredSchedules->filter(fn($schedule) => $schedule->start_time == $selectedTime);
        }
    
        // 📌 **Construir URL para tomar lista**
        $reservationDetailUrl = route('trainings.reservation-detail', [
            'id' => $training->id,
            'date' => $selectedDate,
            'time' => $selectedTime
        ]);
    
        // 📌 **Determinar si el entrenador puede tomar lista**
        $isClassAccessible = false;
        $accessMessage = '';
    
        if ($selectedDate && $selectedTime) {
            $classStartTime = Carbon::parse("$selectedDate $selectedTime");
            $classEndTime = $classStartTime->copy()->addHours(24);
            $now = now();
    
            if ($now->lessThan($classStartTime)) {
                $accessMessage = "Disponible desde " . $classStartTime->format('H:i');
            } elseif ($now->greaterThanOrEqualTo($classStartTime) && $now->lessThanOrEqualTo($classEndTime)) {
                $isClassAccessible = true;
            } else {
                $accessMessage = "Acceso cerrado";
            }
        }
    
        $role = auth()->user()->role;
        $view = ($role === 'entrenador' || $role === 'admin') ? 'trainings.show' : 'trainings.show-training';
    
        return view($view, compact(
            'training', 'filteredSchedules', 'selectedDay', 'selectedTime', 'selectedDate', 'filteredReservations',
            'isClassAccessible', 'accessMessage', 'reservationDetailUrl' // 📌 Se pasa la URL como variable
        ));
    }
    public function gallery($trainingId)
    {
        $training = Training::findOrFail($trainingId);
        return view('trainings.gallery', compact('training'));
    }

    public function storePhoto(Request $request, $trainingId)
    {
        $request->validate([
            'photos.*'           => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'photos_description.*' => 'nullable|string|max:255',
        ]);

        $training = Training::findOrFail($trainingId);

        foreach ($request->file('photos') as $index => $photo) {
            if ($photo->isValid()) {
                Log::info("📸 Procesando imagen: {$photo->getClientOriginalName()}");

                // Redimensionar y guardar la imagen
                $imagePath = $this->resizeAndSaveImage($photo, 'training_photos', 800, 600);
                Log::info("✅ Imagen redimensionada y guardada en: $imagePath");

                // Capturar la descripción de la imagen
                $description = $request->input('photos_description')[$index] ?? 'Foto de entrenamiento';

                // Guardar la imagen en la base de datos
                TrainingPhoto::create([
                    'training_id'              => $training->id,
                    'photo_path'               => str_replace('storage/', '', $imagePath),
                    'training_photos_description' => $description,
                ]);
            } else {
                Log::error("🚫 Imagen no válida: {$photo->getClientOriginalName()}");
            }
        }

        return redirect()->back()->with('success', 'Fotos agregadas exitosamente con redimensionamiento y descripción.');
    }

    // Eliminar una foto
    public function destroyPhoto($photoId)
    {
        $photo = TrainingPhoto::findOrFail($photoId);

        // Eliminar el archivo físico si existe
        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $photo->delete();

        return redirect()->back()->with('success', 'Foto eliminada exitosamente.');
    }
    public function detail($id)
    {
        $training = Training::findOrFail($id);
        $training->load(['trainer', 'park', 'activity', 'schedules', 'prices', 'students', 'reservations.user', 'photos', 'reviews.user']);

        return view('trainings.detail', compact('training'));
    }
    public function myTrainings(Request $request) {
        $userId = Auth::id();
        $today = Carbon::now()->format('Y-m-d');
    
        $trainings = Payment::where('user_id', $userId)
            ->with(['training.schedules.exceptions', 'training.park', 'training.activity', 'training.trainer'])
            ->get()
            ->pluck('training')
            ->unique()
            ->map(function ($training) use ($today) {
                return [
                    'id'        => $training->id,
                    'title'     => $training->title,
                    'trainer'   => ['name' => optional($training->trainer)->name ?? 'No disponible'],
                    'park'      => ['name' => optional($training->park)->name ?? 'No disponible'],
                    'activity'  => ['name' => optional($training->activity)->name ?? 'No disponible'],
                    'available_spots' => $training->available_spots,
                    'schedules' => $training->schedules->map(function ($schedule) use ($today, $training) {
                        $exception = $schedule->exceptions->firstWhere('date', $today);
                        $scheduleDate = $today;
    
                        $totalReservations = TrainingReservation::where('training_id', $schedule->training_id)
                            ->where('date', $scheduleDate)
                            ->where('time', $exception ? $exception->start_time : $schedule->start_time)
                            ->where('status', 'active')
                            ->count();
    
                        $availableSpots = max($training->available_spots - $totalReservations, 0);
    
                        return [
                            'id'             => $schedule->id,
                            'day'            => $schedule->day,
                            'start_time'     => $exception ? $exception->start_time : $schedule->start_time,
                            'end_time'       => $exception ? $exception->end_time : $schedule->end_time,
                            'is_exception'   => (bool) $exception,
                            'available_spots' => $availableSpots,
                            'total_spots'    => $training->available_spots
                        ];
                    })->values()
                ];
            })
            ->values();
    
        Log::info("Entrenamientos obtenidos con excepciones y cupos disponibles:", $trainings->toArray());
    
        $reservations = TrainingReservation::where('user_id', $userId)
    ->whereIn('status', ['active', 'completed', 'no-show'])
    ->with(['training.schedules', 'training.park', 'training.activity', 'training.trainer'])
    ->orderBy('date', 'asc')
    ->get()
    ->map(function ($reservation) {
        $schedule = $reservation->training->schedules
            ->firstWhere('start_time', $reservation->time);

        $reservation->end_time = $schedule ? $schedule->end_time : 'No definido';

        // 🔥 Calcular cupos disponibles en la reserva
        $totalReservations = TrainingReservation::where('training_id', $reservation->training_id)
            ->where('date', $reservation->date)
            ->where('time', $reservation->time)
            ->where('status', 'active')
            ->count();

        $availableSpots = max($reservation->training->available_spots - $totalReservations, 0);

        return [
            'id'        => $reservation->id,
            'date'      => $reservation->date,
            'time'      => $reservation->time,
            'end_time'  => $reservation->end_time,
            'status'    => $reservation->status,
            'available_spots' => $availableSpots, // 🔥 Agregado para que aparezcan los cupos en reservas
            'training'  => [
                'title'    => $reservation->training->title,
                'park'     => ['name' => optional($reservation->training->park)->name ?? 'No disponible'],
                'activity' => ['name' => optional($reservation->training->activity)->name ?? 'No disponible'],
                'trainer'  => ['name' => optional($reservation->training->trainer)->name ?? 'No disponible'],
                'available_spots' => $availableSpots, // 🔥 También aquí por si lo usa en la vista
            ],
        ];
    });
    
        return view('reservations.show', compact('trainings', 'reservations'));
    }
    
   
}