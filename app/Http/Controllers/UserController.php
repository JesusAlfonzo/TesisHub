<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    // 1. Listar Usuarios
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->with(['roles', 'carrera']) // Traemos roles y carrera para la tabla
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%")
                      ->orWhere('email', 'ilike', "%{$search}%")
                      ->orWhere('cedula', 'ilike', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Renderizamos la vista en Admin/Usuarios/index
        return Inertia::render('Admin/Usuarios/index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    // 2. Cambiar estado (Activar/Desactivar)
    public function toggleStatus(User $user)
    {
        // Evitar que el admin se desactive a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activado' : 'desactivado';
        return back()->with('message', "Usuario {$status} correctamente.");
    }
}
