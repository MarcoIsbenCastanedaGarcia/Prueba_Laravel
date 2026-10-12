<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('alumno_id')->constrained('users')->onDelete('cascade');
            $table->string('archivo_path');
            $table->string('nombre_archivo_original');
            $table->integer('tamano_kb');
            $table->timestamp('fecha_envio');
            $table->timestamps();

            // Un alumno solo puede subir 1 evidencia por tarea
            $table->unique(['tarea_id', 'alumno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};