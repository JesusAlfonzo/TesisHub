<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    // 1. Listar Usuarios (Índice)
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->with(['roles', 'carrera'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%")
                      ->orWhere('email', 'ilike', "%{$search}%")
                      ->orWhere('cedula', 'ilike', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Usuarios/index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    // 2. Cambiar estado (Activar/Desactivar)
    public function toggleStatus(User $user)
    {
        // 1. Evitar que el admin se desactive a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        // 2. Ejecutar la actualización en la base de datos
        $newStatus = !$user->is_active;

        $user->update([
            'is_active' => $newStatus
        ]);

        // 3. Devolver mensaje basado en el nuevo estado
        $status = $newStatus ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$status} correctamente.");
    }
}
