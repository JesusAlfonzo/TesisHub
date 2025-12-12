<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Carrera; // <--- AGREGADO: Necesario para el filtro
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    // 1. Listar Usuarios (Índice)
    public function index(Request $request)
    {
        $search = $request->input('search');
        $carreraId = $request->input('carrera_id'); // <--- Capturamos el filtro

        $users = User::query()
            ->with(['roles', 'carrera'])
            // Filtro de Texto
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // CAMBIO: Usamos 'like' para que no falle en tu base de datos
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('cedula', 'like', "%{$search}%");
                });
            })
            // Filtro de Carrera (Agregado directo aquí para no tocar el modelo)
            ->when($carreraId, function ($query, $id) {
                $query->where('carrera_id', $id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Obtenemos las carreras para llenar el select del frontend
        $carreras = Carrera::orderBy('nombre')->get(['id', 'nombre']);

        // IMPORTANTE: Asegúrate que la ruta 'Admin/Usuarios/index' coincide 
        // con la carpeta real de tu archivo .vue (puede ser 'Admin/Usuarios' si renombraste)
        return Inertia::render('Admin/Usuarios/index', [
            'users' => $users,
            'filters' => $request->only(['search', 'carrera_id']),
            'carreras' => $carreras, // <--- Enviamos las carreras a la vista
        ]);
    }

    // 2. Cambiar estado (Activar/Desactivar) - ESTO QUEDA IGUAL
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $newStatus = !$user->is_active;

        $user->update([
            'is_active' => $newStatus
        ]);

        $status = $newStatus ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$status} correctamente.");
    }
}