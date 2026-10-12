@extends('layouts.app')

@section('title', 'Listado de Tareas')
@section('header-title', 'Gestión de Tareas Asignadas')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('docente.tareas.create') }}" class="btn btn-primary">+ Asignar Nueva Tarea</a>
</div>

<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Tipo</th>
                <th>Fecha Límite</th>
                <th>Entregas</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tareas as $tarea)
                <tr>
                    <td><strong>{{ $tarea->titulo }}</strong></td>
                    <td><span class="badge badge-casa">{{ $tarea->tipo }}</span></td>
                    <td>{{ $tarea->fecha_entrega->format('d/m/Y H:i') }} hrs</td>
                    <td>{{ $tarea->evidencias_count }} enviadas</td>
                    <td>
                        @if($tarea->cancelada)
                            <span class="badge badge-cancelada">Cancelada</span>
                        @else
                            <span class="badge badge-clase">Activa</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('docente.tareas.detalle', $tarea->id) }}" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                            Ver Entregas / Evaluar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted);">No hay tareas creadas todavía.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection