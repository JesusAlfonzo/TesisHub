<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    // 1. Listar Usuarios (Índice)
    public function index(Request $request)
    {
        $search = $request->input('search');
        $carreraId = $request->input('carrera_id');

        $users = User::query()
            ->with(['roles', 'carrera'])
            // Filtro de Texto
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('cedula', 'like', "%{$search}%");
                });
            })
            // Filtro de Carrera
            ->when($carreraId, function ($query, $id) {
                $query->where('carrera_id', $id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Obtenemos las carreras para llenar el select del frontend
        $carreras = Carrera::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Admin/Usuarios/index', [
            'users' => $users,
            'filters' => $request->only(['search', 'carrera_id']),
            'carreras' => $carreras, // <--- Enviamos las carreras a la vista
        ]);
    }

    // 2. Cambiar estado (Activar/Desactivar)
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