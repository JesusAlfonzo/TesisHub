<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Carrera;
use Database\Seeders\RolesAndPermissionsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear Carreras del IUJO
        $carreras = [
            ['nombre' => 'Administración. Mención Administración de Empresas', 'codigo' => 'ADM-EMP'],
            ['nombre' => 'Contaduría', 'codigo' => 'CON'],
            ['nombre' => 'Educación. Mención Educación Integral', 'codigo' => 'EDU-INT'],
            ['nombre' => 'Educación. Mención Educación Preescolar', 'codigo' => 'EDU-PRE'],
            ['nombre' => 'Electrotécnia', 'codigo' => 'ELT'],
            ['nombre' => 'Electrónica', 'codigo' => 'ELC'],
            ['nombre' => 'Informática', 'codigo' => 'INF'],
        ];

        foreach ($carreras as $carrera) {
            Carrera::firstOrCreate(
                ['codigo' => $carrera['codigo']],
                ['nombre' => $carrera['nombre']]
            );
        }

        // 2. Llamamos al Seeder de Roles 
        $this->call(RolesAndPermissionsSeeder::class);

        // 3. Usuario de prueba adicional (Opcional)
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'cedula' => '11223344', 
                'carrera_id' => Carrera::where('codigo', 'INF')->first()->id ?? null,
            ]);
        }
    }
}
