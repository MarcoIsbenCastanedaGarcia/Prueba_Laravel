<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidencia_id')->constrained('evidencias')->onDelete('cascade');
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->decimal('nota', 4, 2)->check('nota >= 0 AND nota <= 10');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['borrador', 'publicada'])->default('borrador');
            $table->timestamps();

            $table->unique('evidencia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};