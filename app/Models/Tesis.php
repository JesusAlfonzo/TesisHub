<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tesis extends Model
{
    use HasFactory;

    protected $table = 'tesis';

    protected $fillable = [
        'titulo',
        'resumen',
        'ruta_archivo',
        'estado', // 'pendiente', 'aprobado', 'rechazado'
        'user_id',
        'carrera_id',
    ];

    // Relación: Una tesis pertenece a un Autor (User)
    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Una tesis pertenece a una Carrera
    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    // Scope para buscar fácil (Buscador)
    public function scopeBuscar($query, $texto)
    {
        if ($texto) {
            return $query->where('titulo', 'ilike', "%$texto%") // ilike es case-insensitive en Postgres
                         ->orWhere('resumen', 'ilike', "%$texto%");
        }
    }
    
    // Scope para filtrar por carrera
    public function scopePorCarrera($query, $carreraId)
    {
        if ($carreraId) {
            return $query->where('carrera_id', $carreraId);
        }
    }
}