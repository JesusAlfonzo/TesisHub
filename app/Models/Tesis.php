<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Tesis extends Model
{
    use HasFactory;

    protected $table = 'tesis';

    protected $fillable = [
        'titulo', 'resumen', 'ruta_archivo', 'estado', 'user_id', 'carrera_id',
    ];

    // --- RELACIONES ---
    public function autor() { return $this->belongsTo(User::class, 'user_id'); }
    public function carrera() { return $this->belongsTo(Carrera::class); }

    // --- SCOPES (FILTROS) ---

    // 1. Buscador General
    public function scopeBuscar(Builder $query, $texto)
    {
        if ($texto) {
            return $query->where(function ($q) use ($texto) {
                $q->where('titulo', 'like', "%{$texto}%")
                  ->orWhere('resumen', 'like', "%{$texto}%");
            });
        }
    }
    
    // 2. Filtro por Carrera
    public function scopePorCarrera(Builder $query, $carreraId)
    {
        if ($carreraId) {
            return $query->where('carrera_id', $carreraId);
        }
    }

    // 3. NUEVO: Filtro por Año (Esto soluciona tu error 500)
    public function scopePorAnio(Builder $query, $year)
    {
        if ($year) {
            return $query->whereYear('created_at', $year);
        }
    }
}