<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class TesisController extends Controller
{
    public function index(Request $request)
    {
        // 1. Capturamos search, carrera_id Y AHORA year
        $filters = $request->only(['search', 'carrera_id', 'year']);

        $tesis = Tesis::query()
            ->with(['autor', 'carrera'])
            ->where('estado', 'aprobado')
            ->buscar($request->input('search'))
            ->porCarrera($request->input('carrera_id'))
            ->porAnio($request->input('year'))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Repositorio/index', [
            'tesis' => $tesis,
            'filters' => $filters,
        ]);
    }

    // Ver Detalles de Tesis
    public function show(Tesis $tesis)
    {
        // 1. Política de Seguridad: Solo mostrar si está APROBADA
        if ($tesis->estado !== 'aprobado') {
            // Si no está aprobado, lo redirigimos a una página de acceso denegado (403)
            abort(403, 'Esta tesis aún no ha sido aprobada para su publicación.');
        }

        // 2. Cargamos el detalle con autor y carrera
        $tesis->load(['autor', 'carrera']);

        // 3. Renderizamos la vista de detalle
        return Inertia::render('Repositorio/show', [
            'tesis' => $tesis,
        ]);
    }
}
