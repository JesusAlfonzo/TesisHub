<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MiTesisController extends Controller
{
    // 1. Listar Mis tesis
    public function index()
    {
        $misTesis = Tesis::where('user_id', Auth::id())
            ->with('carrera')
            ->latest()
            ->get();

        return Inertia::render('Estudiante/index', [
            'tesis' => $misTesis
        ]);
    }

    // 2. Mostrar formulario de creación
    public function create()
    {
        return Inertia::render('Estudiante/create');
    }

    // 3. Guardar la tesis
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'required|string',
            'carrera_id' => 'required|exists:carreras,id',
            'archivo' => 'required|file|mimes:pdf|max:10240', // Max 10MB, solo PDF
        ]);

        // Subir archivo al disco 'public' carpeta 'tesis'
        $path = $request->file('archivo')->store('tesis', 'public');

        // Crear registro en BD
        Tesis::create([
            'user_id' => Auth::id(),
            'carrera_id' => $request->carrera_id,
            'titulo' => $request->titulo,
            'resumen' => $request->resumen,
            'ruta_archivo' => $path,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('mis-tesis.index')->with('message', 'Tesis subida correctamente.');
    }

    // 4. Mostrar formulario de edición
    public function edit(Tesis $tesis)
    {
        // Seguridad: Solo el dueño puede editar
        if ($tesis->user_id !== Auth::id()) {
            abort(403);
        }

        // Regla de negocio: No editar si ya está aprobada
        if ($tesis->estado === 'aprobado') {
            return redirect()->back()->with('error', 'No puedes editar una tesis aprobada.');
        }

        return Inertia::render('Estudiante/edit', [
            'tesis' => $tesis
        ]);
    }

    // 5. Actualizar tesis
    public function update(Request $request, Tesis $tesis)
    {
        if ($tesis->user_id !== Auth::id()) abort(403);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'required|string',
            'carrera_id' => 'required|exists:carreras,id',
            'archivo' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = $request->only(['titulo', 'resumen', 'carrera_id']);

        // Si subió un archivo nuevo, borramos el viejo y subimos el nuevo
        if ($request->hasFile('archivo')) {
            if ($tesis->ruta_archivo) {
                Storage::disk('public')->delete($tesis->ruta_archivo);
            }
            $data['ruta_archivo'] = $request->file('archivo')->store('tesis', 'public');
        }

        // Al editar, vuelve a estado pendiente para revisión
        $data['estado'] = 'pendiente';

        $tesis->update($data);

        return redirect()->route('mis-tesis.index')->with('message', 'Tesis actualizada.');
    }

    // 6. Eliminar tesis
    public function destroy(Tesis $tesis)
    {
        if ($tesis->user_id !== Auth::id()) abort(403);

        // Borrar archivo físico
        if ($tesis->ruta_archivo) {
            Storage::disk('public')->delete($tesis->ruta_archivo);
        }

        $tesis->delete();

        return redirect()->back()->with('message', 'Tesis eliminada.');
    }
}