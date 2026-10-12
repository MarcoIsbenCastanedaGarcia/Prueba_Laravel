@extends('layouts.app')

@section('title', 'Panel Docente')
@section('header-title', 'Dashboard del Docente')

@section('content')
<div class="card-grid">
    <div class="card">
        <div class="card-title">Alumnos Activos</div>
        <div class="card-value">{{ $totalAlumnos }}</div>
    </div>
    <div class="card">
        <div class="card-title">Tareas Totales</div>
        <div class="card-value">{{ $totalTareas }}</div>
    </div>
    <div class="card">
        <div class="card-title">Tareas Activas</div>
        <div class="card-value" style="color: var(--tropical-teal)">{{ $tareasActivas }}</div>
    </div>
</div>

<h3 class="font-serif" style="margin-bottom: 1rem;">Últimas Tareas Asignadas</h3>

<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Tipo</th>
                <th>Fecha Límite</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ultimasTareas as $tarea)
                <tr>
                    <td><strong>{{ $tarea->titulo }}</strong></td>
                    <td><span class="badge badge-casa">{{ $tarea->tipo }}</span></td>
                    <td>{{ $tarea->fecha_entrega->format('d/m/Y H:i') }} hrs</td>
                    <td>
                        @if($tarea->cancelada)
                            <span class="badge badge-cancelada">Cancelada</span>
                        @else
                            <span class="badge badge-clase">Activa</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('docente.tareas.detalle', $tarea->id) }}" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Ver Entregas</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay tareas asignadas aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection