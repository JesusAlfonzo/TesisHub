<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'carreras';

    protected $fillable = [
        'nombre',
        'codigo',
    ];

    // --- CORRECCIÓN: Agregamos la relación que faltaba ---
    // Una Carrera tiene muchas Tesis
    public function tesis()
    {
        return $this->hasMany(Tesis::class);
    }

    // Opcional: Relación con Usuarios (Estudiantes)
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}
