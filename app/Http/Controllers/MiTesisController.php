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
        // CORRECCIÓN: 'Estudiante/create' en minúscula
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

        // CORRECCIÓN: 'Estudiante/edit' en minúscula
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
            'archivo' => 'nullable|file|mimes:pdf|max:10240', // Archivo opcional para actualizar
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

        // POLÍTICA DE AUTORIZACIÓN: Permite acceso si el usuario cumple al menos UNA de estas condiciones:
        $canAccess = $tesis->user_id === $user->id ||         // 1. Es el dueño (Estudiante)
                     $user->hasPermissionTo('evaluar tesis') || // 2. Es Tutor o Coordinador
                     $user->hasRole('super-admin');            // 3. Es Super Admin

        if (!$canAccess) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

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
