@extends('layouts.app')

@section('title', 'Nueva Tarea')
@section('header-title', 'Asignar Nueva Tarea')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('docente.tareas.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="titulo">Título de la Tarea</label>
            <input type="text" id="titulo" name="titulo" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo de Tarea</label>
            <select id="tipo" name="tipo" class="form-control" required>
                <option value="tareas en casa">Tarea en casa</option>
                <option value="actividades en clase">Actividad en clase</option>
                <option value="exposiciones">Exposición</option>
            </select>
        </div>

        <div class="form-group">
            <label for="fecha_entrega_dia">Fecha de Entrega Límite (Hasta las 23:59 hrs)</label>
            <input type="date" id="fecha_entrega_dia" name="fecha_entrega_dia" class="form-control" min="{{ date('Y-m-d') }}" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Instrucciones o Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
            <a href="{{ route('docente.tareas.index') }}" class="btn btn-danger">Cancelar</a>
            <button type="submit" class="btn btn-primary">Asignar Tarea</button>
        </div>
    </form>
</div>
@endsection