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

    // Una Carrera tiene muchas Tesis
    public function tesis()
    {
        return $this->hasMany(Tesis::class);
    }

    // Relación con Usuarios (Estudiantes)
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}
