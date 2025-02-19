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
    ->name('student.profile')
    ->middleware('auth'); // Solo autenticados pueden ver perfiles
Route::get('/alumno/perfil/editar', [StudentController::class, 'editStudentProfile'])
    ->name('student.editProfile')->middleware('auth');
Route::put('/alumno/perfil/editar', [StudentController::class, 'updateStudent'])
    ->name('student.updateProfile')->middleware('auth');


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
    ->middleware(['auth']);

Route::get('/api/trainings', [TrainerController::class, 'getTrainingsByPark']);

// Perfil del entrenador
Route::get('/entrenador/perfil', [TrainerController::class, 'showTrainerProfile'])
    ->name('trainer.profile')
    ->middleware(['auth', 'role:entrenador']);

// Todos los entrenamientos del entrenador
Route::get('/mis-entrenamientos', [TrainerController::class, 'showTrainerTrainings'])
    ->name('trainer.index')
    ->middleware(['auth', 'role:entrenador']);


    /*
|--------------------------------------------------------------------------
| Rutas para ParkController 
|--------------------------------------------------------------------------
*/

        
 Route::get('/entrenador/agregar-parque', [ParkController::class, 'create'])
    ->name('trainer.add.park')
    ->middleware(['auth', 'role:entrenador']);
Route::post('/entrenador/agregar-parque', [ParkController::class, 'store'])
    ->name('trainer.store.park')
    ->middleware(['auth', 'role:entrenador']);
    
Route::get('/parques/{id}', [ParkController::class, 'show'])->name('parks.show');


