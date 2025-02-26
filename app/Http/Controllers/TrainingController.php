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

        $selectedParkId = $request->query('park_id'); // Obtén el parque seleccionado
        $parks = Auth::user()->parks; // Obtén todos los parques del entrenador
        $activities = Activity::all(); // Todas las actividades disponibles
        
        return view('trainings.create', compact('parks', 'selectedParkId', 'activities',));
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
        $selectedDate = $request->query('date'); 
        $selectedTime = $request->query('time');
        
        $training = Training::with([
            'trainer',
            'park',
            'activity',
            'schedules.exceptions', // Agregado para cargar excepciones
            'prices',
            'students',
            'reservations.user',
            'photos'
        ])->findOrFail($id);
    
        $training->refresh();
    
        $selectedDay = $request->query('day');
        $filteredSchedules = $training->schedules;
    
        // 👉 Agregar excepciones a los horarios
        if ($selectedDate) {
            $filteredSchedules = $filteredSchedules->map(function ($schedule) use ($selectedDate) {
                $exception = $schedule->exceptions->firstWhere('date', $selectedDate);
    
                return (object) [
                    'id' => $schedule->id,
                    'day' => $schedule->day,
                    'start_time' => $exception ? $exception->start_time : $schedule->start_time,
                    'end_time' => $exception ? $exception->end_time : $schedule->end_time,
                    'is_exception' => $exception ? true : false,
                ];
            });
        }
    
        // Filtrar por día seleccionado
        if ($selectedDay) {
            $filteredSchedules = $filteredSchedules->filter(fn($schedule) => $schedule->day === $selectedDay);
        }
    
        // Filtrar por fecha y suspensiones
        if ($selectedDate) {
            $filteredSchedules = $filteredSchedules->filter(function ($schedule) use ($selectedDate) {
                return !TrainingStatus::where('training_schedule_id', $schedule->id)
                    ->where('date', $selectedDate)
                    ->where('status', 'suspended')
                    ->exists();
            });
        }
    
        // Filtrar por hora seleccionada
        if ($selectedTime) {
            $filteredSchedules = $filteredSchedules->filter(fn($schedule) => $schedule->start_time == $selectedTime);
        }
    
        // 📅 Obtener reservas filtradas por fecha
        $filteredReservations = $selectedDate
            ? $training->reservations->where('date', $selectedDate)->groupBy('time') 
            : collect([]);
    
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
        $view = ($role === 'entrenador' || $role === 'admin') ? 'trainings.show' : 'student.show-training';
    
        return view($view, compact(
            'training', 'filteredSchedules', 'selectedDay', 'selectedTime', 'selectedDate', 'filteredReservations',
            'isClassAccessible', 'accessMessage', 'reservationDetailUrl'
        ));
    }
    ///Mis entrenamientos del entrenador
    public function showAll($id)
    {
        $training = Training::with(['schedules', 'photos', 'students'])->findOrFail($id);
        return view('trainings.showAll', compact('training'));
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
        try {
            Log::info('🚀 Iniciando actualización del entrenamiento', ['training_id' => $id]);
            $selectedDate = $request->input('selected_date');
            Log::info('📅 Fecha seleccionada:', ['selected_date' => $selectedDate]);

            // Validación
            $validated = $request->validate([
                'title'              => 'required|string|max:255',
                'description'        => 'nullable|string',
                'level'              => 'required|in:Principiante,Intermedio,Avanzado',
                'activity_id'        => 'required|exists:activities,id',
                'park_id'            => 'required|exists:parks,id',
                'available_spots'    => 'required|integer|min:1',
                'schedule.start_time.*' => 'required|date_format:H:i',
                'schedule.end_time.*'   => 'required|date_format:H:i|after:schedule.start_time.*',
            ]);

            // Actualizar detalles generales del entrenamiento
            $training = Training::findOrFail($id);
            $training->update([
                'title'           => $validated['title'],
                'description'     => $request->input('description'),
                'level'           => $validated['level'],
                'activity_id'     => $validated['activity_id'],
                'park_id'         => $validated['park_id'],
                'available_spots' => $validated['available_spots'],
            ]);

            Log::info('✍️ Entrenamiento actualizado con éxito');

            // Crear excepciones por fecha específica, sin tocar el horario base
            if ($request->has('schedule_id')) {
                foreach ($request->input('schedule_id') as $index => $scheduleId) {
                    $startTime = $request->input("schedule.start_time.$index");
                    $endTime = $request->input("schedule.end_time.$index");

                    TrainingException::updateOrCreate(
                        [
                            'training_schedule_id' => $scheduleId,
                            'date' => $selectedDate,  // Excepción para la fecha específica
                        ],
                        [
                            'start_time' => $startTime,
                            'end_time'   => $endTime,
                            'status'     => 'modified',
                        ]
                    );

                    Log::info("✅ Excepción creada para el $selectedDate: $startTime - $endTime");
                }
            }

            return redirect()->route('trainings.show', ['id' => $training->id, 'date' => $selectedDate])
                ->with('success', 'Entrenamiento actualizado solo para la fecha seleccionada.');
        } catch (\Exception $e) {
            Log::error('❌ Error durante la actualización', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al actualizar el entrenamiento.');
        }
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
         \Log::info("🚀 Datos recibidos en suspendClass", [
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
 
         \Log::info("📅 Día calculado para la fecha $trainingDate: $dayOfWeek");
 
         // ✅ Buscar el horario (`training_schedule_id`) correspondiente al `training_id` en ese día
         $schedule = TrainingSchedule::where('training_id', $trainingId)
             ->where('day', $dayOfWeek) // Comparar con el nombre del día
             ->first();
 
         if (!$schedule) {
             \Log::error("🚨 No se encontró un training_schedule_id para training_id={$trainingId} en el día {$dayOfWeek}.");
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
 
         \Log::info("✅ Clase suspendida con éxito para training_schedule_id={$schedule->id} en fecha {$trainingDate}");
        // ✅ Obtener alumnos inscritos
        $students = TrainingReservation::where('training_id', $trainingId)
        ->where('date', $trainingDate)
        ->pluck('user_id');

        if ($students->isEmpty()) {
        \Log::info("🚨 No hay alumnos inscritos para notificar.");
        } else {
        $emails = \App\Models\User::whereIn('id', $students)->pluck('email');

        foreach ($emails as $email) {
            Mail::to($email)->send(new TrainingSuspendedMail($schedule->training, $trainingDate));
        }

        \Log::info("📩 Se enviaron correos a los alumnos inscritos.");
        }

         $deletedReservations = TrainingReservation::where('training_id', $trainingId)
         ->where('date', $trainingDate)
         ->delete();
 
         \Log::info("🗑️ Se eliminaron {$deletedReservations} reservas para training_id={$trainingId} en fecha {$trainingDate}");
 
         return response()->json([
             'message' => 'Clase suspendida con éxito y reservas eliminadas',
             'date' => $trainingDate,
             'deleted_reservations' => $deletedReservations
         ]);
     }

     //Filtra las clases
     public function getTrainingsForWeek(Request $request)
     {
         $weekStartDate = $request->query('week_start_date');
         
         if (!$weekStartDate || !strtotime($weekStartDate)) {
             Log::error('❌ Fecha de inicio de semana inválida.');
             return response()->json(['error' => 'Fecha de inicio de semana inválida.'], 400);
         }
         
         $daysOfWeek = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
     
         Log::info("📅 Semana de inicio: $weekStartDate");
     
         // Obtener entrenamientos con excepciones y precios
         $trainings = TrainingSchedule::with(['training.prices', 'exceptions'])
             ->whereHas('training', function ($query) {
                 $query->where('trainer_id', auth()->id());
             })
             ->get()
             ->map(function ($schedule) use ($weekStartDate, $daysOfWeek) {
                 $dayIndex = array_search($schedule->day, $daysOfWeek);
                 if ($dayIndex === false) {
                     Log::warning("⚠️ Día no encontrado: {$schedule->day}");
                     return null;
                 }
     
                 // Calcular la fecha específica de la semana
                 $trainingDate = date('Y-m-d', strtotime("$weekStartDate +$dayIndex days"));
                 Log::info("📅 Fecha calculada: $trainingDate para {$schedule->day}");
     
                 // Buscar si hay una excepción para esa fecha específica
                 $exception = $schedule->exceptions->firstWhere('date', $trainingDate);
     
                 // Log para depuración
                 Log::info("🔍 Excepción encontrada para {$schedule->day} - {$trainingDate}: ", [
                     'exception' => $exception ? $exception->toArray() : 'Ninguna'
                 ]);
     
                 // Si la excepción es de tipo "cancelled", omitir la clase
                 if ($exception && $exception->status === 'cancelled') {
                     Log::info("❌ Clase cancelada para $trainingDate.");
                     return null;
                 }
     
                 // Determinar el horario a mostrar (priorizar excepción)
                 $startTime = $exception && $exception->start_time ? $exception->start_time : $schedule->start_time;
                 $endTime   = $exception && $exception->end_time   ? $exception->end_time   : $schedule->end_time;
     
                 Log::info("🕰️ Horario final para $trainingDate:", [
                     'start_time' => $startTime,
                     'end_time'   => $endTime,
                     'exception'  => $exception ? 'Sí' : 'No',
                 ]);
     
                 // Obtener el precio más bajo o el primero
                 $price = $schedule->training->prices->first();
     
                 // Devolver datos del entrenamiento
                 return [
                     'id'           => $schedule->id,
                     'training_id'  => $schedule->training_id,
                     'date'         => $trainingDate,
                     'day'          => $schedule->day,
                     'title'        => $schedule->training->title,
                     'start_time'   => $startTime,
                     'end_time'     => $endTime,
                     'price'        => $price ? $price->price : 0,
                     'sessions'     => $price ? $price->weekly_sessions : 0,
                     'status'       => $exception ? $exception->status : 'active',
                     'is_exception' => $exception ? true : false,
                 ];
             })
             ->filter()
             ->values();
     
         Log::info("🚀 Entrenamientos para la semana con horarios actualizados: ", $trainings->toArray());
     
         return response()->json($trainings);
     }


    ////////////// POV ALUMNOS 

    public function myTrainings() 
    {
        $userId = Auth::id();
    
        // Obtener los entrenamientos que el alumno ha comprado
        $trainings = Payment::where('user_id', $userId)
            ->with('training')
            ->get()
            ->pluck('training')
            ->unique();
    
        // Obtener solo las reservas activas
        $reservations = TrainingReservation::where('user_id', $userId)
            ->whereIn('status', ['active', 'completed', 'no-show']) // Filtrar por estado
            ->with('training')
            ->orderBy('date', 'asc')
            ->get();
        
        return view('student.training.my-trainings', compact('trainings', 'reservations'));
    }

    public function select(Request $request, $id)
    {
        $user = auth()->user();
        $training = Training::with(['trainer', 'park', 'activity', 'schedules', 'prices', 'reviews.user'])->findOrFail($id);

        // Verificar si el usuario ha comprado este entrenamiento
        $hasPurchased = false;
        if ($user) {
            $hasPurchased = \App\Models\Payment::where('user_id', $user->id)
                ->where('training_id', $training->id)
                ->exists();
        }

        // Validar si el usuario ya ha guardado este entrenamiento en favoritos
        $isFavorite = false;
        if ($user) {
            $isFavorite = \App\Models\Favorite::where('user_id', $user->id)
                ->where('favoritable_id', $training->id)
                ->where('favoritable_type', Training::class)
                ->exists();
        }
        

        // Validar si se está enviando un día (por ejemplo, 'Lunes')
        $selectedDay = $request->query('day'); 
        $filteredSchedules = $selectedDay
            ? $training->schedules->filter(function ($schedule) use ($selectedDay) {
                return $schedule->day === $selectedDay;
            })
            : $training->schedules;

        $role = auth()->user()->role;

        if ($role === 'entrenador' || $role === 'admin') {
            return view('trainer.show', compact('training', 'filteredSchedules', 'selectedDay', 'isFavorite'));
        } else {
            return view('student.training.show-training', compact('training', 'filteredSchedules', 'selectedDay', 'hasPurchased', 'isFavorite'));
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
        $selectedLevels = $request->input('level') ? explode(',', $request->input('level')) : []; // 🔹 Filtro de nivel
    
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
    
        // Aplicar filtro de horarios (permitiendo que los entrenamientos comiencen en la hora seleccionada o dentro de los siguientes 59 minutos)
        if (!empty($selectedHours)) {
            $query->whereHas('schedules', function ($q) use ($selectedHours) {
                $q->where(function ($subQuery) use ($selectedHours) {
                    foreach ($selectedHours as $hour) {
                        $startRange = date('H:i:s', strtotime($hour)); // Hora seleccionada
                        $endRange = date('H:i:s', strtotime($hour . ' +59 minutes')); // Hasta 59 min después
                        $subQuery->orWhereBetween('start_time', [$startRange, $endRange]);
                    }
                });
            });
        }
    
        // 🔹 Aplicar filtro de nivel
        if (!empty($selectedLevels)) {
            $query->whereIn('level', $selectedLevels);
        }
    
        // Obtener entrenamientos filtrados
        $trainings = $query->get();
    
        // Lista de días de la semana y niveles
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $levels = ['Principiante', 'Intermedio', 'Avanzado']; // 🔹 Niveles disponibles
    
        return view('parks.trainings', compact('park', 'activity', 'trainings', 'daysOfWeek', 'levels', 'selectedDays', 'selectedHours', 'selectedLevels'));
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

}


