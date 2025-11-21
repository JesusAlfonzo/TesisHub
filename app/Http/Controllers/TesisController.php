<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TesisController extends Controller
{
    public function index(Request $request)
    {
        // Capturamos los filtros de la URL
        $filters = $request->only(['search', 'carrera_id']);

        $tesis = Tesis::query()
            ->with(['autor', 'carrera']) // Traemos datos relacionados para no hacer mil consultas
            ->where('estado', 'aprobado') // Solo mostramos lo aprobado
            ->buscar($request->input('search')) // Scope del Modelo
            ->porCarrera($request->input('carrera_id')) // Scope del Modelo
            ->latest() // Las más recientes primero
            ->paginate(9) // Paginación de 9 en 9 para grid de 3x3
            ->withQueryString(); // Mantiene los filtros al cambiar de página

        return Inertia::render('Repositorio/index', [
            'tesis' => $tesis,
            'filters' => $filters,
        ]);
    }
}
