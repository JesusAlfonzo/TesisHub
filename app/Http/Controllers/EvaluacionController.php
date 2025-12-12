<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvaluacionController extends Controller
{
    /**
     * 1. Listar Tesis Pendientes de Revisión (Con Filtros)
     */
    public function index(Request $request)
    {
        // 1. Capturamos los filtros para devolverlos a la vista (persistencia visual)
        $filters = $request->only(['search', 'carrera_id']);

        // 2. Consulta principal
        $pendientes = Tesis::query()
            ->with(['autor', 'carrera'])
            ->where('estado', 'pendiente')

            // --- APLICAMOS LOS SCOPES DEL MODELO ---
            ->buscar($request->input('search'))
            ->porCarrera($request->input('carrera_id'))
            // ---------------------------------------

            ->latest() // Ordenar por fecha de creación descendente
            ->paginate(10) // Paginación de 10 elementos
            ->withQueryString(); // Mantiene los filtros en la URL al cambiar de página

        // 3. Obtenemos la lista de carreras para el <select> del filtro en el frontend
        $carreras = Carrera::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Academico/index', [
            'tesis' => $pendientes,
            'filters' => $filters,  // Pasamos los filtros activos
            'carreras' => $carreras // Pasamos la lista para el dropdown
        ]);
    }

    /**
     * 2. Historial de Evaluaciones
     */
    public function historial(Request $request)
    {
        // Capturamos filtros para el historial también
        $filters = $request->only(['search', 'carrera_id']);

        $historial = Tesis::query()
            ->with(['autor', 'carrera'])
            ->where('estado', '!=', 'pendiente') // Trae 'aprobado' y 'rechazado'

            // Filtros
            ->buscar($request->input('search'))
            ->porCarrera($request->input('carrera_id'))

            ->latest('updated_at') // Ordenar por fecha de evaluación
            ->paginate(10)
            ->withQueryString();

        $carreras = Carrera::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Academico/historial', [
            'tesis' => $historial,
            'filters' => $filters,
            'carreras' => $carreras,
        ]);
    }

    /**
     * 3. Guardar la evaluación (Aprobar/Rechazar)
     */
    public function update(Request $request, Tesis $tesis)
    {
        // Validamos que el estado sea uno de los permitidos
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado',
        ]);

        // Actualizamos el estado
        $tesis->update([
            'estado' => $request->estado,
        ]);

        $mensaje = $request->estado === 'aprobado' ? 'Tesis aprobada correctamente.' : 'Tesis rechazada.';

        // Redirigimos a la ruta de pendientes para seguir evaluando
        return redirect()->route('evaluaciones.index')->with('message', $mensaje);
    }
}
