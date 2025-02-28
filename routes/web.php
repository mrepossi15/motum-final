<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;

use App\Http\Controllers\FavoriteController;

/*
|--------------------------------------------------------------------------
| Rutas de UserController | Autenticación
|--------------------------------------------------------------------------
*/

// Login y logout
Route::get('/', [UserController::class, 'loginForm'])
    ->name('login');
Route::post('/iniciar-sesion', [UserController::class, 'login'])
    ->name('login.process');
Route::post('/cerrar-sesion', [UserController::class, "logout"])
    ->name('logout.process');

/*
|--------------------------------------------------------------------------
| Rutas para restaurar contrasena
|--------------------------------------------------------------------------
*/ 
// Se puede usar la ñ? Preguntar

Route::get('contraseña/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
->name('password.request');
Route::post('contraseña/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
->name('password.email');

Route::get('contraseña/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
->name('password.reset');
Route::post('contraseña/reset', [ResetPasswordController::class, 'reset'])
->name('password.update');

/*
|--------------------------------------------------------------------------
| Rutas para StudentController 
|--------------------------------------------------------------------------
*/
Route::get('/registro/alumno', [StudentController::class, 'registerStudent'])
    ->name('register.student');
Route::post('/registro/alumno', [StudentController::class, 'storeStudent'])
    ->name('store.student');
Route::get('/perfil/{id}', [StudentController::class, 'studentProfile'])
    ->name('students.profile')
    ->middleware('auth'); 
Route::get('/alumno/perfil/editar', [StudentController::class, 'editStudentProfile'])
    ->name('students.editProfile')->middleware('auth');
Route::put('/alumno/perfil/editar', [StudentController::class, 'updateStudent'])
    ->name('students.updateProfile')->middleware('auth');

//Mapa principal
Route::get('/mapa', [studentController::class, 'map'])
    ->name('students.map')
    ->middleware(['auth', 'role:alumno']);


    /*
|--------------------------------------------------------------------------
| Rutas para TrainerController 
|--------------------------------------------------------------------------
*/

// Registro de entrenadores
Route::get('/registro/entrenador', [TrainerController::class, 'registerTrainer'])
    ->name('register.trainer');
Route::post('/registro/entrenador', [TrainerController::class, 'storeTrainer'])
    ->name('store.trainer');

// Calendario de entrenamientos
Route::get('/entrenador/calendario', [TrainerController::class, 'calendar'])
    ->name('trainer.calendar')
    ->middleware(['auth', 'role:entrenador']);

// Obtener entrenamientos por parque. POV calendar
Route::get('/api/trainings/park', [TrainerController::class, 'getTrainingsByPark']);

// Obtener entrenamientos por semana
Route::get('/api/trainings/week', [TrainingController::class, 'getTrainingsForWeek'])->name('api.trainings');

// Perfil del entrenador
Route::get('/entrenador/perfil', [TrainerController::class, 'showTrainerProfile'])
    ->name('trainer.profile')
    ->middleware(['auth', 'role:entrenador']);
//Vista Editar perfil
Route::get('/trainer/profile/edit', [TrainerController::class, 'editTrainerProfile'])
    ->name('trainer.edit')
    ->middleware(['auth', 'role:entrenador']);
//Modificar perfil entrenador
 Route::put('/trainer/profile/update', [TrainerController::class, 'updateTrainer'])
    ->name('trainer.update')
    ->middleware(['auth', 'role:entrenador']);
//Experiencia dnetrenador
Route::post('/store/experience', [TrainerController::class, 'storeExperience'])
    ->name('trainer.storeExperience');
Route::put('/trainer/experience/{id}', [TrainerController::class, 'updateExperience'])
    ->name('trainer.experience.update')
    ->middleware(['auth', 'role:entrenador']);

Route::delete('/trainer/experience/{id}', [TrainerController::class, 'destroyExperience'])
    ->name('trainer.experience.destroy')
    ->middleware(['auth', 'role:entrenador']);


// Todos los entrenamientos del entrenador
Route::get('/mis-entrenamientos', [TrainerController::class, 'showTrainerTrainings'])
    ->name('trainer.show-trainings')
    ->middleware(['auth', 'role:entrenador']);


// Detalle del entrenamiento. NO LO USO
Route::get('/entrenamientos/{id}', [TrainingController::class, 'showAll'])
->name('trainer.showAll')
->middleware(['auth', 'role:entrenador']);



/*
|--------------------------------------------------------------------------
| Rutas para ParkController 
|--------------------------------------------------------------------------
*/

// Visat apra que el entrendor agregue un parque       
 Route::get('/entrenador/agregar-parque', [ParkController::class, 'create'])
    ->name('parks.create')
    ->middleware(['auth', 'role:entrenador']);
// Agregar un parque  
Route::post('/entrenador/agregar-parque', [ParkController::class, 'store'])
    ->name('parks.store')
    ->middleware(['auth', 'role:entrenador']);

// Trae todos los parques. No lo use yet.
Route::get('/parques/{id}', [ParkController::class, 'show'])->name('parks.show');
Route::get('/api/nearby-parks', [ParkController::class, 'getNearbyParks']);


/*
|--------------------------------------------------------------------------
| Rutas para TrainingController 
|--------------------------------------------------------------------------
*/
// Crear entrenamientos

Route::get('/trainings/create', [TrainingController::class, 'create'])
    ->name('trainings.create')
    ->middleware(['auth', 'role:entrenador']);
// Guardar entrenamientos
Route::post('/entrenamientos/agregar', [TrainingController::class, 'store'])
    ->name('trainings.store')
    ->middleware(['auth', 'role:entrenador']);

// Vista por entrenamiento
Route::get('/entrenamientos/{id}', [TrainingController::class, 'show'])
    ->name('trainings.show')
    ->middleware(['auth', 'role:entrenador']);

// Vista editar entrenamientos   
Route::get('/entrenamientos/{id}/editar', [TrainingController::class, 'edit'])
    ->name('trainings.edit')
    ->middleware(['auth', 'role:entrenador']);
// Editar entrenamientos   
Route::put('/entrenamientos/{id}', [TrainingController::class, 'update'])
    ->name('trainings.update')
    ->middleware(['auth', 'role:entrenador']);

// Elimianr todo un entrenamiento
Route::delete('/entrenamientos/{id}', [TrainingController::class, 'destroy'])
    ->name('trainings.destroy')
    ->middleware(['auth', 'role:entrenador']);
    
    Route::delete('/entrenamientos/{id}/todos', [TrainingController::class, 'destroyAll'])
    ->name('trainings.destroyAll')
    ->middleware(['auth', 'role:entrenador']);

//Suspender una clase
Route::post('/entrenamiento/suspender', [TrainingController::class, 'suspendClass'])
    ->name('trainings.suspend')
    ->middleware(['auth', 'role:entrenador']);

//Maenjod e fotos. POV entrendor
Route::get('/trainings/{training}/gallery', [TrainingController::class, 'gallery'])->name('trainings.gallery');
Route::post('/trainings/{training}/photos', [TrainingController::class, 'storePhoto'])->name('trainings.photos.store');
Route::delete('/trainings/photos/{photo}', [TrainingController::class, 'destroyPhoto'])->name('trainings.photos.destroy');

Route::get('/trainings/{id}/edit-all', [TrainingController::class, 'editAll'])->name('trainings.editAll');

// Ruta para actualizar el entrenamiento editado
Route::put('/trainings/{id}/update-all', [TrainingController::class, 'updateAll'])->name('trainings.updateAll');
/*
|--------------------------------------------------------------------------
| Rutas de ReservationController
|--------------------------------------------------------------------------
*/

Route::get('/entrenamiento/{id}/reserva', [ReservationController::class, 'reserveTrainingView'])
    ->middleware('auth')
    ->name('reserve.training.view');

Route::post('/entrenamiento/{id}/reserva', [ReservationController::class, 'storeReservation'])
    ->middleware('auth')
    ->name('store.reservation');

Route::delete('/entrenamiento/{id}/delete', [ReservationController::class, 'cancelReservation'])
    ->middleware('auth')
    ->name('cancel.reservation');

Route::get('/entrenamiento/{id}/available-times', [ReservationController::class, 'getAvailableTimes'])
    ->middleware('auth')
    ->name('trainings.available-times');

  
    Route::get('/entrenamiento/{id}/detalle-reserva/{date}', [ReservationController::class, 'reservationDetail'])
->name('reservations.attendance');

Route::patch('/reservations/{id}/update-status', [ReservationController::class, 'updateReservationStatus'])
    ->name('reservations.updateStatus')
    ->middleware('auth');

    /*
|--------------------------------------------------------------------------
| Rutas de ReviewController
|--------------------------------------------------------------------------
*/

Route::post('/reviews', [ReviewController::class, 'store'])
    ->name('reviews.store')
    ->middleware(['auth', 'role:alumno']);

Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])
    ->name('reviews.destroy')
    ->middleware('auth');

Route::get('/trainings/{id}/detail', [TrainingController::class, 'detail'])
->name('trainings.detail')
->middleware(['auth', 'role:entrenador']);