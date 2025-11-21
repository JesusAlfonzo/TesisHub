<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        // 1. Estadísticas: Tesis por Carrera y Estado
        // Esto nos dará algo como: { 'Informática': { aprobadas: 5, total: 10 }, ... }
        $stats = Carrera::withCount([
            'tesis as total',
            'tesis as aprobadas' => function ($query) {
                $query->where('estado', 'aprobado');
            },
            'tesis as pendientes' => function ($query) {
                $query->where('estado', 'pendiente');
            }
        ])->get();

        // 2. Listado de las últimas 50 tesis aprobadas para el reporte detallado
        $listado = Tesis::where('estado', 'aprobado')
            ->with(['autor', 'carrera'])
            ->latest('updated_at') // Ordenadas por fecha de aprobación
            ->take(50)
            ->get();

        return Inertia::render('Admin/Reportes/index', [
            'stats' => $stats,
            'listado' => $listado,
            'fecha' => now()->format('d/m/Y H:i'),
        ]);
    }
}
