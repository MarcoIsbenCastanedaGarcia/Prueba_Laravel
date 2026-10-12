<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tarea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el usuario Docente (Super Usuario)
        $docente = User::create([
            'nombre' => 'Prof. Roberto Gómez',
            'email' => 'docente@escuela.edu.mx',
            'password' => Hash::make('Docente123!'),
            'role' => 'docente',
            'activo' => true,
        ]);

        // 2. Crear 15 Alumnos de prueba
        $alumnosData = [
            ['nombre' => 'Juan Pérez', 'email' => 'juan.perez@alumnos.edu.mx'],
            ['nombre' => 'María López', 'email' => 'maria.lopez@alumnos.edu.mx'],
            ['nombre' => 'Carlos Hernández', 'email' => 'carlos.h@alumnos.edu.mx'],
            ['nombre' => 'Ana Martínez', 'email' => 'ana.m@alumnos.edu.mx'],
            ['nombre' => 'Luis González', 'email' => 'luis.g@alumnos.edu.mx'],
            ['nombre' => 'Sofia Rodriguez', 'email' => 'sofia.r@alumnos.edu.mx'],
            ['nombre' => 'Diego Sánchez', 'email' => 'diego.s@alumnos.edu.mx'],
            ['nombre' => 'Laura Ramírez', 'email' => 'laura.r@alumnos.edu.mx'],
            ['nombre' => 'Fernando Torres', 'email' => 'fernando.t@alumnos.edu.mx'],
            ['nombre' => 'Patricia Flores', 'email' => 'patricia.f@alumnos.edu.mx'],
            ['nombre' => 'Jorge Rivera', 'email' => 'jorge.r@alumnos.edu.mx'],
            ['nombre' => 'Carmen Gomez', 'email' => 'carmen.g@alumnos.edu.mx'],
            ['nombre' => 'Ricardo Diaz', 'email' => 'ricardo.d@alumnos.edu.mx'],
            ['nombre' => 'Gabriela Cruz', 'email' => 'gabriela.c@alumnos.edu.mx'],
            ['nombre' => 'Alejandro Morales', 'email' => 'alejandro.m@alumnos.edu.mx'],
        ];

        foreach ($alumnosData as $data) {
            User::create([
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'password' => Hash::make('Alumno123!'),
                'role' => 'alumno',
                'activo' => true,
            ]);
        }

        // 3. Crear tareas de ejemplo para pruebas
        Tarea::create([
            'docente_id' => $docente->id,
            'titulo' => 'Investigación sobre Arquitectura MVC',
            'descripcion' => 'Subir un ensayo explicativo del patrón de diseño MVC y su uso en frameworks modernos.',
            'tipo' => 'tareas en casa',
            'fecha_entrega' => Carbon::now()->addDays(3)->endOfDay(),
            'cancelada' => false,
        ]);

        Tarea::create([
            'docente_id' => $docente->id,
            'titulo' => 'Exposición de Bases de Datos Relacionales',
            'descripcion' => 'Subir diapositivas en formato PDF de la presentación del equipo.',
            'tipo' => 'exposiciones',
            'fecha_entrega' => Carbon::now()->subDays(1)->endOfDay(), // Tarea con plazo ya vencido
            'cancelada' => false,
        ]);
    }
}