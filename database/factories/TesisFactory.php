<?php

namespace Database\Factories;

use App\Models\Carrera;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TesisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Genera un título de tesis de 6 a 12 palabras
            'titulo' => $this->faker->sentence(rand(6, 12)), 
            
            // Genera un resumen de 3 párrafos
            'resumen' => $this->faker->paragraph(3),
            
            // Simulamos una ruta de archivo PDF
            'ruta_archivo' => 'tesis/demo.pdf', 
            
            // Estado aleatorio: mayor probabilidad de estar 'aprobado' para llenar el repositorio
            'estado' => $this->faker->randomElement(['pendiente', 'rechazado', 'aprobado', 'aprobado', 'aprobado']),
            
            // Asigna un usuario existente al azar, o crea uno nuevo si no hay
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            
            // Asigna una carrera existente al azar
            'carrera_id' => Carrera::inRandomOrder()->first()->id ?? 1,
            
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}