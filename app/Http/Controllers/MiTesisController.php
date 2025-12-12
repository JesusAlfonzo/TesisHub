<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $carreras = Carrera::orderBy('nombre')->get();

        return Inertia::render('Estudiante/edit', [
            'tesis' => $tesis->load('carrera'),
            'carreras' => $carreras,
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
            if ($tesis->ruta_archivo && Storage::disk('public')->exists($tesis->ruta_archivo)) {
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

        if ($tesis->ruta_archivo && Storage::disk('public')->exists($tesis->ruta_archivo)) {
            Storage::disk('public')->delete($tesis->ruta_archivo);
        }

        $tesis->delete();

        return redirect()->route('mis-tesis.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    /**
     * Sirve el archivo PDF de forma segura.
     * Corrige el error 500 y maneja permisos públicos/privados.
     */
    public function verArchivo(Tesis $tesis)
    {
        $user = Auth::user();

        // 1. Permisos
        $isPubliclyAvailable = $tesis->estado === 'aprobado';

        // Verificaciones que requieren usuario logueado
        $isOwner = $user && ($tesis->user_id === $user->id);

        // Verificamos si tiene el permiso o rol (de manera segura si $user es null)
        $isEvaluator = $user && ($user->can('evaluar tesis') || ($user->hasRole && $user->hasRole('super-admin')));

        // ¿Puede acceder?
        $canAccess = $isPubliclyAvailable || $isOwner || $isEvaluator;

        if (!$canAccess) {
            // Si es un invitado intentando ver algo privado, mandarlo al login
            if (!$user) {
                return redirect()->route('login');
            }
            abort(403, 'No tienes permiso para ver este documento.');
        }

        // 2. Verificar existencia del archivo
        $filePath = $tesis->ruta_archivo;

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'El archivo físico no se encuentra en el servidor.');
        }

        // 3. Servir el archivo (CORREGIDO)
        // Usamos response()->file() que es lo estándar para PDFs inline
        $fullPath = storage_path('app/public/' . $filePath);
        $fileName = Str::slug($tesis->titulo) . '.pdf';

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
