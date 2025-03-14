<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingStatus;
use Carbon\Carbon;
use App\Models\TrainingSchedule;
use App\Models\TrainingException;
use App\Models\TrainingReservation;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    public function storeReservation(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . Carbon::today()->addDays(4)->toDateString(),
            'time' => 'required',
        ]);
        
        
        $user = Auth::user();
        $training = Training::findOrFail($id);
    
        // Marca las reservas pasadas como completadas
        $now = Carbon::now();
        TrainingReservation::where('user_id', $user->id)
            ->whereNull('canceled_at')
            ->where('status', 'active')
            ->whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') < ?", [$now])
            ->update(['status' => 'completed']);
        
        $payment = Payment::where('user_id', $user->id)->where('training_id', $id)->first();
        if (!$payment) {
            return response()->json(['error' => 'No se encontró el pago para este entrenamiento.'], 400);
        }
    
        $weeklySessions = $payment->weekly_sessions;
    
        // Verifica las clases completadas esta semana
        $reservationsThisWeek = TrainingReservation::where('user_id', $user->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->whereIn('status', ['completed', 'no-show'])
            ->count();
    
        if ($reservationsThisWeek >= $weeklySessions) {
            return response()->json(['error' => 'Ya has completado todas tus clases de esta semana.'], 400);
        }
    
        // Verifica si el usuario tiene 2 reservas activas
        $activeReservations = TrainingReservation::where('user_id', $user->id)
            ->where('training_id', $id)
            ->where('status', 'active')
            ->count();
    
        if ($activeReservations >= 2) {
            return response()->json(['error' => 'Solo puedes tener hasta 2 reservas activas para este entrenamiento.'], 400);
        }
    
        // Nueva validación: Verifica si ya tiene una reserva activa para el mismo día (no importa la hora)
        $existingReservationForSameDay = TrainingReservation::where('user_id', $user->id)
            ->where('training_id', $id)
            ->where('date', $request->date)
            ->where('status', 'active')
            ->first();
    
        if ($existingReservationForSameDay) {
            return response()->json(['error' => 'Ya tienes una reserva activa para este día.'], 400);
        }
    
        // Evita reservar el MISMO horario más de una vez
        $existingReservationSameTime = TrainingReservation::where('user_id', $user->id)
            ->where('training_id', $id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('status', 'active')
            ->first();
    
        if ($existingReservationSameTime) {
            return response()->json(['error' => 'Ya tienes una reserva activa para este horario.'], 400);
        }
    
        // Verifica si la fecha seleccionada está dentro de los 4 días permitidos
        $selectedDate = Carbon::parse($request->date);
        if ($selectedDate->diffInDays(Carbon::now()) > 4) {
            return response()->json(['error' => 'No puedes hacer reservas para clases a más de 4 días de anticipación.'], 400);
        }
    
        // Verifica si hay cupos disponibles
        $currentReservations = TrainingReservation::where('training_id', $id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->count();
    
        if ($currentReservations >= $training->available_spots) {
            return response()->json(['error' => 'No hay cupos disponibles para este horario.'], 400);
        }
     // 🔥 OBTENER EL HORARIO DE FINALIZACIÓN DESDE `training_schedules`


        // Crea la reserva con el horario de finalización correcto
        TrainingReservation::create([
        'user_id' => $user->id,
        'training_id' => $id,
        'date' => $request->date,
        'time' => $request->time,
        'end_time' => $request->end_time, // ✅ Ahora guardamos el horario de fin correctamente
        'status' => 'active',
        ]);
    
        return redirect()->route('reservations.show', ['#reservas'])
        ->with('success', '¡Reserva creada exitosamente!');
    }
    public function cancelReservation($id) {
        $reservation = TrainingReservation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        $reservationTime = Carbon::parse($reservation->date . ' ' . $reservation->time);
        $now = Carbon::now();
    
        if ($now->diffInHours($reservationTime) < 6) {
            return back()->with('error', 'No puedes cancelar una reserva con menos de 6 horas de anticipación.');
        }
    
        $reservation->delete();
    
        return back()->with('success', 'Reserva cancelada correctamente.');
    }
    public function getAvailableTimes(Request $request, $id)
    {
        try {
            Log::info("🚀 Iniciando getAvailableTimes() para entrenamiento ID: {$id}", [
                'date' => $request->date
            ]);
    
            $date = Carbon::parse($request->date);
            $requestedDay = $date->format('l');
    
            // Mapeo de nombres de días en inglés a español
            $daysMap = [
                'Monday'    => 'Lunes',
                'Tuesday'   => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday'  => 'Jueves',
                'Friday'    => 'Viernes',
                'Saturday'  => 'Sábado',
                'Sunday'    => 'Domingo'
            ];
            $requestedDaySpanish = $daysMap[$requestedDay] ?? null;
    
            if (!$requestedDaySpanish) {
                Log::error("❌ Día inválido recibido: {$requestedDay}");
                return response()->json(['error' => 'Día inválido.'], 400);
            }
    
            Log::info("🔍 Buscando horarios para {$requestedDaySpanish} ({$request->date}) en entrenamiento {$id}");
    
            // ✅ Obtener los horarios base con excepciones ya cargadas
            $availableTimes = TrainingSchedule::with(['exceptions', 'statuses'])
                ->where('training_id', $id)
                ->where('day', $requestedDaySpanish)
                ->get()
                ->keyBy('id'); // Indexar por ID para referencia rápida
    
            Log::info("📌 Horarios base encontrados en la BD para el entrenamiento {$id}: " . json_encode($availableTimes->toArray()));
    
            if ($availableTimes->isEmpty()) {
                Log::warning("⚠️ No se encontraron horarios para {$requestedDaySpanish}");
                return response()->json([]);
            }
    
            // ✅ Obtener excepciones en una sola consulta
            $exceptions = TrainingException::whereIn('training_schedule_id', $availableTimes->keys())
                ->where('date', $request->date)
                ->get()
                ->keyBy('training_schedule_id');
    
            Log::info("📌 Excepciones encontradas para la fecha {$request->date}: " . json_encode($exceptions->toArray()));
    
            // ✅ Obtener clases suspendidas en la fecha seleccionada
            $suspendedTrainings = TrainingStatus::whereIn('training_schedule_id', $availableTimes->keys())
                ->where('date', $request->date)
                ->where('status', 'suspended')
                ->pluck('training_schedule_id')
                ->toArray();
    
            Log::info("🚫 Clases suspendidas en la fecha {$request->date}: " . json_encode($suspendedTrainings));
    
            // ✅ **Aplicar lógica priorizando excepciones**
            $formattedTimes = collect();
    
            foreach ($availableTimes as $scheduleId => $schedule) {
                // ❌ Filtrar entrenamientos suspendidos
                if (in_array($scheduleId, $suspendedTrainings)) {
                    Log::warning("❌ Horario suspendido para entrenamiento {$scheduleId}");
                    continue;
                }
    
                if ($exceptions->has($scheduleId)) {
                    $exception = $exceptions[$scheduleId];
                    Log::info("⚠️ EXCEPCIÓN USADA para entrenamiento {$scheduleId} en {$exception->date}: 
                               Inicio: {$exception->start_time}, Fin: {$exception->end_time}");
    
                    $formattedTimes->push([
                        'id'           => $scheduleId,
                        'start_time'   => $exception->start_time,
                        'end_time'     => $exception->end_time,
                        'is_exception' => true,
                    ]);
                } else {
                    Log::info("✅ Horario original usado para entrenamiento {$scheduleId}");
                    $formattedTimes->push([
                        'id'           => $scheduleId,
                        'start_time'   => $schedule->start_time,
                        'end_time'     => $schedule->end_time,
                        'is_exception' => false,
                    ]);
                }
            }
    
            Log::info("✅ Horarios finales enviados: " . json_encode($formattedTimes->toArray()));
    
            return response()->json($formattedTimes->values()); // Reindexar los resultados
    
        } catch (\Exception $e) {
            Log::error("❌ Error en getAvailableTimes(): " . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
    
            return response()->json([
                'error' => 'Ocurrió un error al obtener los horarios disponibles.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function reserveTrainingView($id) {
        $training = Training::with('schedules')->findOrFail($id);
    
        // Obtener horarios disponibles
        $availableSchedules = TrainingSchedule::where('training_id', $id)->get();
    
        return view('reservations.create', compact('training', 'availableSchedules'));
    }

    // Tomar lista
    public function reservationDetail($id, $date, Request $request)
    {
        $selectedTime = $request->query('time'); // Obtener la hora seleccionada
    
        $training = Training::with('park')->findOrFail($id);
    
        // Filtrar las reservas por fecha y hora específica
        $reservations = TrainingReservation::where('training_id', $id)
            ->where('date', $date)
            ->when($selectedTime, function ($query) use ($selectedTime) {
                return $query->where('time', $selectedTime);
            })
            ->get();
    
        return view('reservations.attendance', compact('training', 'date', 'reservations', 'selectedTime'));
    }
    

    public function updateReservationStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:completed,no-show',
    ]);

    $reservation = TrainingReservation::findOrFail($id);

    // Verificar que el usuario que intenta cambiar el estado sea el entrenador de la clase
    if (Auth::user()->id !== $reservation->training->trainer_id) {
        return back()->with('error', 'No tienes permiso para modificar esta reserva.');
    }

    // Actualizar el estado de la reserva
    $reservation->update(['status' => $request->status]);

    return back()->with('success', 'Estado de la reserva actualizado correctamente.');
}
public function checkReservation(Request $request, $id)
{
    $user = Auth::user();
    $training = Training::findOrFail($id);

    $payment = Payment::where('user_id', $user->id)->where('training_id', $id)->first();
    if (!$payment) {
        return response()->json(['error' => 'No se encontró el pago para este entrenamiento.'], 400);
    }

    $weeklySessions = $payment->weekly_sessions;

    $reservationsThisWeek = TrainingReservation::where('user_id', $user->id)
        ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->whereIn('status', ['completed', 'no-show'])
        ->count();

    if ($reservationsThisWeek >= $weeklySessions) {
        return response()->json(['error' => 'Ya has completado todas tus clases de esta semana.'], 400);
    }

    $existingReservation = TrainingReservation::where('user_id', $user->id)
        ->where('training_id', $id)
        ->where('status', 'active')
        ->first();

    if ($existingReservation) {
        return response()->json(['error' => 'Solo puedes tener una reserva activa por entrenamiento.'], 400);
    }

    return response()->json(['success' => 'Puedes reservar esta clase.']);
}
}
