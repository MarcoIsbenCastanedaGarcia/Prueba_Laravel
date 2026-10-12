@extends('layouts.app')

@section('title', 'Historial de Entregas')
@section('header-title', 'Historial de Evidencias Enviadas')

@section('content')
<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Tarea</th>
                <th>Fecha Envío</th>
                <th>Archivo PDF</th>
                <th>Calificación</th>
                <th>Observaciones del Docente</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evidencias as $evidencia)
                <tr>
                    <td><strong>{{ $evidencia->tarea->titulo }}</strong></td>
                    <td>{{ $evidencia->fecha_envio->format('d/m/Y H:i') }} hrs</td>
                    <td>
                        <a href="{{ Storage::url($evidencia->archivo_path) }}" target="_blank" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                            📄 {{ $evidencia->nombre_archivo_original }}
                        </a>
                    </td>
                    <td>
                        @if($evidencia->calificacion)
                            <strong style="color: var(--cerulean); font-size: 1.1rem;">{{ $evidencia->calificacion->nota }} / 10</strong>
                        @else
                            <span class="badge badge-casa">Pendiente de Revisión</span>
                        @endif
                    </td>
                    <td>
                        {{ $evidencia->calificacion ? ($evidencia->calificacion->observaciones ?? 'Sin observaciones') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted)">Aún no has enviado ninguna evidencia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection