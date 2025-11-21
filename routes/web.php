<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\TesisController;
use App\Http\Controllers\MiTesisController;

// 
// 1. Rutas publicas
// 

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// 
// 2. Rutas de autenticacion
// 

// LOGIN
Route::get('/login', function () {
    return Inertia::render('Auth/Login', [
        'name' => config('app.name'),
        'image' => asset('images/auth/login.webp'),
        'canRegister' => Features::enabled(Features::registration()),
        'quote' => [
            'message' => 'La educación no cambia el mundo, cambia a las personas que van a cambiar el mundo.',
            'author' => 'Paulo Freire',
        ],
    ]);
})->middleware(['guest'])->name('login');

// REGISTER
if (Features::enabled(Features::registration())) {
    Route::get('/register', function () {
        return Inertia::render('Auth/Register', [
            'name' => config('app.name'),
            'image' => asset('images/auth/register.webp'),
            'quote' => [
                'message' => 'El primer paso hacia la grandeza es la voluntad de intentarlo.',
                'author' => 'Anónimo',
            ],
        ]);
    })->middleware(['guest'])->name('register');
}

// FORGOT PASSWORD
Route::get('/forgot-password', function () {
    return Inertia::render('Auth/ForgotPassword', [
        'name' => config('app.name'),
    ]);
})->middleware(['guest'])->name('password.request');

// 
// 3. Rutas Protegidas (Requieren Login)
// 

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Principal
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Repositorio de Tesis
    Route::get('/tesis', [TesisController::class, 'index'])->name('tesis.index');

    // Modulo Estudiantes (mis tesis)
    Route::resource('mis-tesis', MiTesisController::class)
        ->parameters(['mis-tesis' => 'tesis']);

    // Aquí agregaremos luego las rutas de Estudiante, Tutor y Admin...

});

require __DIR__ . '/settings.php';
