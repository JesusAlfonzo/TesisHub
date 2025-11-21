<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvaluacionController extends Controller
{
    // 1. Listar Tesis Pendientes de Revisión
    public function index()
    {
        $pendientes = Tesis::where('estado', 'pendiente')
            ->with(['autor', 'carrera']) // Traemos datos del estudiante y carrera
            ->latest()
            ->get();

        // Usamos minúsculas para coincidir con tu estructura
        return Inertia::render('Academico/index', [
            'tesis' => $pendientes
        ]);
    }

    // 2. Historial de Evaluaciones (NUEVO MÉTODO)
    public function historial()
    {
        // Traemos todo lo que NO sea pendiente (Aprobado o Rechazado)
        $historial = Tesis::where('estado', '!=', 'pendiente')
            ->with(['autor', 'carrera'])
            ->latest('updated_at') // Ordenar por fecha de evaluación (actualización)
            ->paginate(10);

        return Inertia::render('Academico/historial', [
            'tesis' => $historial
        ]);
    }

    // 3. Guardar la evaluación (Aprobar/Rechazar)
    public function update(Request $request, Tesis $tesis)
    {
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado',
            // 'observacion' => 'required_if:estado,rechazado' // Podríamos agregar esto luego
        ]);

        $tesis->update([
            'estado' => $request->estado,
        ]);

        $mensaje = $request->estado === 'aprobado' ? 'Tesis aprobada.' : 'Tesis rechazada.';

        // Redirigimos al index para seguir evaluando
        return redirect()->route('evaluaciones.index')->with('message', $mensaje);
    }
}
