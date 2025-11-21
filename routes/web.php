<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\TesisController;
use App\Http\Controllers\MiTesisController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\ReporteController;

//
// 1. Rutas para todos
//

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

//
// 2. Rutas de autenticacion, imagenes de fondo y frases (quotes)
//

Route::get('/login', function () {
    return Inertia::render('auth/Login', [
        'name' => config('app.name'),
        'image' => asset('images/auth/login.webp'),
        'canRegister' => Features::enabled(Features::registration()),
        'canResetPassword' => Features::enabled(Features::resetPasswords()),
        'status' => session('status'),
        'quote' => [
            'message' => 'La educación no cambia el mundo, cambia a las personas que van a cambiar el mundo.',
            'author' => 'Paulo Freire',
        ],
    ]);
})->middleware(['guest'])->name('login');

if (Features::enabled(Features::registration())) {
    Route::get('/register', function () {
        return Inertia::render('auth/Register', [
            'name' => config('app.name'),
            'image' => asset('images/auth/register.webp'),
            'quote' => [
                'message' => 'El primer paso hacia la grandeza es la voluntad de intentarlo.',
                'author' => 'Anónimo',
            ],
        ]);
    })->middleware(['guest'])->name('register');
}

Route::get('/forgot-password', function () {
    return Inertia::render('auth/ForgotPassword', [
        'name' => config('app.name'),
        'image' => asset('images/auth/register.webp'),
        'status' => session('status'),
        'quote' => [
            'message' => 'El primer paso hacia la grandeza es la voluntad de intentarlo.',
            'author' => 'Anónimo',
        ],
    ]);
})->middleware(['guest'])->name('password.request');


//
// 3. Rutas protegidas
//

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    // Repositorio de tesis
    Route::get('/tesis', [TesisController::class, 'index'])->name('tesis.index');
    Route::get('/tesis/{tesis}', [TesisController::class, 'show'])->name('tesis.show');

    // Modulo de estudiantes
    Route::resource('mis-tesis', MiTesisController::class)
        ->parameters(['mis-tesis' => 'tesis']);

    // Ver PDF
    Route::get('/tesis/ver/{tesis}', [MiTesisController::class, 'verArchivo'])
        ->name('tesis.ver');

    // Modulo academico / tutor
    Route::middleware(['can:evaluar tesis'])->group(function () {
        Route::get('/evaluaciones/pendientes', [EvaluacionController::class, 'index'])->name('evaluaciones.index');
        Route::get('/evaluaciones/historial', [EvaluacionController::class, 'historial'])->name('evaluaciones.historial');
        Route::patch('/evaluaciones/{tesis}', [EvaluacionController::class, 'update'])->name('evaluaciones.update');
    });

    // Modulo administrativo
    Route::middleware(['can:gestionar usuarios'])->prefix('admin')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::patch('/usuarios/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::resource('carreras', CarreraController::class)->except(['create', 'edit', 'show']);
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    });
});

require __DIR__ . '/settings.php';
