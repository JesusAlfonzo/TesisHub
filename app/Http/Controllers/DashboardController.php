<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [];

        // 1. Métricas para Administradores y Coordinadores
        if ($user->hasRole(['super-admin', 'coordinador'])) {
            $stats = [
                'total_usuarios' => User::count(),
                'total_tesis' => Tesis::count(),
                'tesis_aprobadas' => Tesis::where('estado', 'aprobado')->count(),
                'tesis_pendientes' => Tesis::where('estado', 'pendiente')->count(),
                'estudiantes' => User::role('estudiante')->count(),
                'tutores' => User::role('tutor')->count(),
            ];
        }

        // 2. Métricas para Tutores
        elseif ($user->hasRole('tutor')) {
            $stats = [
                'asignadas' => Tesis::where('estado', 'pendiente')->count(),
                'evaluadas' => Tesis::where('estado', '!=', 'pendiente')->count(),
            ];
        }

        // 3. Métricas para Estudiantes
        elseif ($user->hasRole('estudiante')) {
            $stats = [
                'mis_tesis' => Tesis::where('user_id', $user->id)->count(),
                'aprobadas' => Tesis::where('user_id', $user->id)->where('estado', 'aprobado')->count(),
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}
