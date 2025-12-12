<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'carreras';

    protected $fillable = [
        'nombre',
        'codigo',
    ];

    // --- RELACIONES ---

    // Una Carrera tiene muchas Tesis
    public function tesis()
    {
        return $this->hasMany(Tesis::class);
    }

    // Una Carrera tiene muchos Usuarios (Estudiantes/Tutores)
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    // --- SCOPES (FILTROS) ---

    /**
     * Filtra carreras por nombre o código.
     * Utilizado en el buscador de Admin/Carreras.vue
     */
    public function scopeBuscar(Builder $query, $texto)
    {
        if ($texto) {
            return $query->where(function($q) use ($texto) {
                $q->where('nombre', 'like', "%{$texto}%")
                  ->orWhere('codigo', 'like', "%{$texto}%");
            });
        }
    }
}