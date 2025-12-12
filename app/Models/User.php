<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder; // Importante para el autocompletado

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'carrera_id',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // --- RELACIONES ---
    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    // --- SCOPES (FILTROS) ---

    // 1. Buscador General (Nombre, Email, Cédula)
    public function scopeBuscar(Builder $query, $texto)
    {
        if ($texto) {
            return $query->where(function ($q) use ($texto) {
                // CAMBIO: Usamos 'like' para compatibilidad con MariaDB/MySQL
                // 'ilike' es solo para PostgreSQL
                $q->where('name', 'like', "%{$texto}%")
                  ->orWhere('email', 'like', "%{$texto}%")
                  ->orWhere('cedula', 'like', "%{$texto}%");
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
}