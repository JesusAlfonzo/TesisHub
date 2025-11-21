<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MiTesisController extends Controller
{
    /**
     * Muestra la lista de tesis del estudiante logeado.
     */
    public function index()
    {
        $tesis = Tesis::where('user_id', Auth::id())
            ->with('carrera')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Estudiante/index', [
            'tesis' => $tesis,
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva tesis.
     */
    public function create()
    {
        return Inertia::render('Estudiante/create');
    }

    /**
     * Almacena una nueva tesis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'required|string',
            'carrera_id' => 'required|exists:carreras,id',
            'archivo' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $filePath = $request->file('archivo')->store('tesis/estudiantes', 'public');

        Tesis::create([
            'user_id' => Auth::id(),
            'carrera_id' => $request->carrera_id,
            'titulo' => $request->titulo,
            'resumen' => $request->resumen,
            'ruta_archivo' => $filePath,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('mis-tesis.index')
            ->with('success', 'Proyecto subido y enviado para revisión.');
    }

    /**
     * Muestra el formulario para editar la tesis.
     */
    public function edit(Tesis $tesis)
    {
        if ($tesis->user_id !== Auth::id() || $tesis->estado === 'aprobado') {
            abort(403, 'No tienes permiso para editar este proyecto o ya ha sido aprobado.');
        }

        return Inertia::render('Estudiante/edit', [
            'tesis' => $tesis->load('carrera'),
        ]);
    }

    /**
     * Actualiza la tesis.
     */
    public function update(Request $request, Tesis $tesis)
    {
        if ($tesis->user_id !== Auth::id() || $tesis->estado === 'aprobado') {
            abort(403);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'required|string',
            'carrera_id' => 'required|exists:carreras,id',
            'archivo' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $tesis->titulo = $request->titulo;
        $tesis->resumen = $request->resumen;
        $tesis->carrera_id = $request->carrera_id;

        if ($request->hasFile('archivo')) {
            if ($tesis->ruta_archivo) {
                Storage::disk('public')->delete($tesis->ruta_archivo);
            }
            $filePath = $request->file('archivo')->store('tesis/estudiantes', 'public');
            $tesis->ruta_archivo = $filePath;

            if ($tesis->estado === 'rechazado') {
                $tesis->estado = 'pendiente';
            }
        }

        $tesis->save();

        return redirect()->route('mis-tesis.index')
            ->with('success', 'Proyecto actualizado y enviado nuevamente para revisión.');
    }

    /**
     * Elimina la tesis.
     */
    public function destroy(Tesis $tesis)
    {
        if ($tesis->user_id !== Auth::id()) {
            abort(403);
        }

        if ($tesis->ruta_archivo) {
            Storage::disk('public')->delete($tesis->ruta_archivo);
        }

        $tesis->delete();

        return redirect()->route('mis-tesis.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    /**
     * Sirve el archivo PDF de forma segura.
     */
    public function verArchivo(Tesis $tesis)
    {
        $user = Auth::user();

        // 1. Si la tesis está aprobada, CUALQUIER USUARIO AUTENTICADO tiene acceso.
        $isPubliclyAvailable = $tesis->estado === 'aprobado';

        // 2. Si el usuario es el dueño.
        $isOwner = $tesis->user_id === $user->id;

        // 3. Si el usuario es un evaluador (Tutor/Coordinador/Super-Admin).
        $isEvaluator = $user->hasPermissionTo('evaluar tesis') || $user->hasRole('super-admin');

        // Acceso permitido si cumple CUALQUIERA de las condiciones
        $canAccess = $isPubliclyAvailable || $isOwner || $isEvaluator;

        if (!$canAccess) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        // --------------------------------------------------

        $filePath = $tesis->ruta_archivo;
        $fileName = $tesis->titulo . '.pdf';

        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'El archivo no fue encontrado en el servidor.');
        }

        // Devolver el archivo como respuesta HTTP forzando la visualización inline
        return Storage::disk('public')->response($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
