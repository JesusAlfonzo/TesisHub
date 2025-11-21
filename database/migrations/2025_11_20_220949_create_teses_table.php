<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tesis', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('resumen')->nullable(); // Abstract

            // RF 3.3 y 3.4: Carga y Edición
            $table->string('ruta_archivo'); // Path del PDF

            // RF 3.6: Aprobación/Rechazo (Estados)
            // Estados: 'pendiente', 'aprobado', 'rechazado'
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');

            // Relaciones
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // El estudiante autor
            $table->foreignId('carrera_id')->constrained('carreras'); // Para filtrar tesis por carrera

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teses');
    }
};
