<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AlumnoController;

// Redirección inicial
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas públicas / Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas para DOCENTE
Route::middleware(['auth', 'role:docente'])->prefix('docente')->name('docente.')->group(function () {
    Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('dashboard');

    // Gestión de Alumnos
    Route::get('/alumnos', [DocenteController::class, 'alumnosIndex'])->name('alumnos.index');
    Route::post('/alumnos', [DocenteController::class, 'alumnoStore'])->name('alumnos.store');
    Route::post('/alumnos/carga-masiva', [DocenteController::class, 'alumnoCargaMasiva'])->name('alumnos.carga_masiva');
    Route::put('/alumnos/{alumno}', [DocenteController::class, 'alumnoUpdate'])->name('alumnos.update');
    Route::patch('/alumnos/{alumno}/toggle', [DocenteController::class, 'alumnoToggleEstado'])->name('alumnos.toggle');

    // Gestión de Tareas
    Route::get('/tareas', [DocenteController::class, 'tareasIndex'])->name('tareas.index');
    Route::get('/tareas/nueva', [DocenteController::class, 'tareaCreate'])->name('tareas.create');
    Route::post('/tareas', [DocenteController::class, 'tareaStore'])->name('tareas.store');
    Route::post('/tareas/{tarea}/cancelar', [DocenteController::class, 'tareaCancelar'])->name('tareas.cancelar');
    Route::get('/tareas/{tarea}', [DocenteController::class, 'tareaDetalle'])->name('tareas.detalle');

    // Evaluación
    Route::post('/evidencias/{evidencia}/evaluar', [DocenteController::class, 'evaluarEvidencia'])->name('evidencias.evaluar');
});

// Rutas protegidas para ALUMNO
Route::middleware(['auth', 'role:alumno'])->prefix('alumno')->name('alumno.')->group(function () {
    Route::get('/dashboard', [AlumnoController::class, 'dashboard'])->name('dashboard');
    Route::get('/historial', [AlumnoController::class, 'historial'])->name('historial');
    Route::get('/tareas/{tarea}/subir', [AlumnoController::class, 'subirEvidenciaForm'])->name('tareas.subir');
    Route::post('/tareas/{tarea}/subir', [AlumnoController::class, 'subirEvidenciaStore'])->name('tareas.subir.store');
    Route::post('/notificaciones/{notificacion}/leida', [AlumnoController::class, 'marcarNotificacionLeida'])->name('notificaciones.leida');
});