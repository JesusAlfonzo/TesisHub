<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- RUTAS DE AUTENTICACIÓN (Login, Register, etc.) ---

// Ruta para mostrar la vista de LOGIN
Route::get('/login', function () {
    return Inertia::render('auth/Login', [
        'name' => config('app.name'),
        'image' => asset('images/auth/login.webp'), // <-- IMAGEN PARA LOGIN
        'canRegister' => Features::enabled(Features::registration()),
        // Puedes añadir aquí el prop 'quote' si lo usas en tu layout
        'quote' => [
            'message' => 'La educación no cambia el mundo, cambia a las personas que van a cambiar el mundo.',
            'author' => 'Paulo Freire',
        ],
    ]);
})->middleware(['guest'])->name('login'); // Asegúrate de que el nombre sea 'login'

// Ruta para mostrar la vista de REGISTER (solo si está habilitada)
if (Features::enabled(Features::registration())) {
    Route::get('/register', function () {
        return Inertia::render('auth/Register', [
            'name' => config('app.name'),
            'image' => asset('images/auth/register.webp'), // <-- IMAGEN PARA REGISTER
            'quote' => [
                'message' => 'El primer paso hacia la grandeza es la voluntad de intentarlo.',
                'author' => 'Anónimo',
            ],
        ]);
    })->middleware(['guest'])->name('register'); // Asegúrate de que el nombre sea 'register'
}

// Ruta para mostrar la vista de Forgot Password (si necesitas una imagen diferente)
Route::get('/forgot-password', function () {
    return Inertia::render('auth/ForgotPassword', [
        'name' => config('app.name'),
        // 'image' => asset('images/auth/recovery.jpg'), // <-- IMAGEN PARA RECOVERY
    ]);
})->middleware(['guest'])->name('password.request');

require __DIR__.'/settings.php';
