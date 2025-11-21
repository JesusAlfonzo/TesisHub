<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarreraController extends Controller
{
    public function index(Request $request)
    {
        $carreras = Carrera::query()
            ->when($request->search, function ($q, $search) {
                $q->where('nombre', 'ilike', "%{$search}%")
                  ->orWhere('codigo', 'ilike', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Carreras/index', [
            'carreras' => $carreras,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:20|unique:carreras,codigo',
        ]);

        Carrera::create($request->all());

        return redirect()->back()->with('message', 'Carrera creada correctamente.');
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:20|unique:carreras,codigo,' . $carrera->id,
        ]);

        $carrera->update($request->all());

        return redirect()->back()->with('message', 'Carrera actualizada.');
    }

    public function destroy(Carrera $carrera)
    {
        $carrera->delete();
        return redirect()->back()->with('message', 'Carrera eliminada.');
    }
}
