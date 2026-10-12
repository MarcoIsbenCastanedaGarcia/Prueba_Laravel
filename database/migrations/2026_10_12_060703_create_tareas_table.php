<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('tipo', ['tareas en casa', 'actividades en clase', 'exposiciones']);
            $table->timestamp('fecha_entrega'); // Representa el día asignado hasta las 23:59:59
            $table->boolean('cancelada')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};