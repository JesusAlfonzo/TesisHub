<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\Carrera; // IMPORTANTE: Agregado para los selectores
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
        // Pasamos las carreras para llenar el <select> del formulario
        $carreras = Carrera::orderBy('nombre')->get();

        return Inertia::render('Estudiante/create', [
            'carreras' => $carreras,
        ]);
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

        // Necesitamos las carreras para el select
        $carreras = Carrera::orderBy('nombre')->get();

        return Inertia::render('Estudiante/edit', [
            'tesis' => $tesis->load('carrera'),
            'carreras' => $carreras, // Agregado
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
            // Borrar archivo anterior si existe
            if ($tesis->ruta_archivo && Storage::disk('public')->exists($tesis->ruta_archivo)) {
                Storage::disk('public')->delete($tesis->ruta_archivo);
            }
            
            $filePath = $request->file('archivo')->store('tesis/estudiantes', 'public');
            $tesis->ruta_archivo = $filePath;

            // Si fue rechazado y sube uno nuevo, vuelve a pendiente
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

        if ($tesis->ruta_archivo && Storage::disk('public')->exists($tesis->ruta_archivo)) {
            Storage::disk('public')->delete($tesis->ruta_archivo);
        }

        $tesis->delete();

        return redirect()->route('mis-tesis.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    /**
     * Sirve el archivo PDF de forma segura (Control de Acceso Completo).
     */
    public function verArchivo(Tesis $tesis)
    {
        $user = Auth::user();

        // 1. ¿Es tesis pública (aprobada)?
        $isPubliclyAvailable = $tesis->estado === 'aprobado';

        // 2. ¿Es el dueño?
        $isOwner = $tesis->user_id === $user->id;

        // 3. ¿Es evaluador? (Usamos 'can' para consistencia con Laravel Gates)
        // Esto cubre Tutores y Admins si el Gate está definido correctamente
        $isEvaluator = $user->can('evaluar tesis') || ($user->hasRole && $user->hasRole('super-admin'));

        // Permitir acceso si cumple ALGUNA condición
        $canAccess = $isPubliclyAvailable || $isOwner || $isEvaluator;

        if (!$canAccess) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        // --------------------------------------------------

        $filePath = $tesis->ruta_archivo;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'El archivo no fue encontrado en el servidor.');
        }

        // Limpiamos el nombre para evitar errores en la descarga
        $fileName = \Str::slug($tesis->titulo) . '.pdf';

        // Devolver el archivo inline (visualización en navegador)
        return response()->file(storage_path('app/public/' . $filePath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}