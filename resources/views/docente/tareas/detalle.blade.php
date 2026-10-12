@extends('layouts.app')

@section('title', 'Detalle de Tarea')
@section('header-title', $tarea->titulo)

@section('content')
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <span class="badge badge-casa">{{ $tarea->tipo }}</span>
            <p style="margin-top: 0.5rem;"><strong>Descripción:</strong> {{ $tarea->descripcion }}</p>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Fecha Límite: {{ $tarea->fecha_entrega->format('d/m/Y H:i') }} hrs</p>
        </div>
        @if(!$tarea->cancelada)
            <form action="{{ route('docente.tareas.cancelar', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Seguro que desea cancelar esta tarea? Se notificará a los alumnos.');">
                @csrf
                <button type="submit" class="btn btn-danger">Cancelar Tarea</button>
            </form>
        @else
            <span class="badge badge-cancelada">Esta tarea está Cancelada</span>
        @endif
    </div>
</div>

<h3 class="font-serif" style="margin-bottom: 1rem;">Seguimiento de Entregas y Evaluación</h3>

<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Estatus Envío</th>
                <th>Fecha de Envío</th>
                <th>Evidencia PDF</th>
                <th>Calificación (0 - 10)</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnos as $alumno)
                @php
                    $evidencia = $evidencias->get($alumno->id);
                    $calificacion = $evidencia ? $evidencia->calificacion : null;
                @endphp
                <tr>
                    <td><strong>{{ $alumno->nombre }}</strong></td>
                    <td>
                        @if($evidencia)
                            @if($evidencia->entregadaATiempo())
                                <span class="badge badge-clase">Entregado a Tiempo</span>
                            @else
                                <span class="badge badge-cancelada">Entrega Tardía</span>
                            @endif
                        @else
                            <span class="badge badge-cancelada">Pendiente</span>
                        @endif
                    </td>
                    <td>{{ $evidencia ? $evidencia->fecha_envio->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        @if($evidencia)
                            <a href="{{ Storage::url($evidencia->archivo_path) }}" target="_blank" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">📄 Ver PDF</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($calificacion)
                            <strong>{{ $calificacion->nota }} / 10</strong>
                            <small>({{ ucfirst($calificacion->estado) }})</small>
                        @else
                            <em>Sin evaluar</em>
                        @endif
                    </td>
                    <td>
                        @if($evidencia && $tarea->plazoVencido())
                            <button type="button" 
                                    class="btn btn-primary btn-evaluar" 
                                    data-evidencia-id="{{ $evidencia->id }}"
                                    data-nota="{{ $calificacion ? $calificacion->nota : '' }}"
                                    data-observaciones="{{ $calificacion ? $calificacion->observaciones : '' }}"
                                    style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                                {{ $calificacion ? 'Editar Nota' : 'Evaluar' }}
                            </button>
                        @elseif($evidencia && !$tarea->plazoVencido())
                            <small style="color: var(--text-muted)">Evaluación disponible tras la fecha límite</small>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal para Evaluar Evidencia -->
<div id="modalEvaluar" class="modal">
    <div class="modal-content">
        <h3 class="font-serif" style="margin-bottom: 1rem;">Evaluar Evidencia</h3>
        <form id="formEvaluar" method="POST">
            @csrf
            <div class="form-group">
                <label>Calificación (Escala 0 a 10)</label>
                <input type="number" id="eval_nota" name="nota" min="0" max="10" step="0.1" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Observaciones / Feedback</label>
                <textarea id="eval_obs" name="observaciones" class="form-control" rows="3"></textarea>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 0.5rem;">
                <button type="button" id="btnCerrarModalEvaluar" class="btn btn-danger">Cancelar</button>
                <div>
                    <button type="submit" name="accion" value="borrador" class="btn btn-secondary">Guardar Borrador</button>
                    <button type="submit" name="accion" value="publicar" class="btn btn-success">Publicar Nota</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Evento para abrir modal de evaluar
    document.querySelectorAll('.btn-evaluar').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const evidenciaId = this.getAttribute('data-evidencia-id');
            const nota = this.getAttribute('data-nota');
            const observaciones = this.getAttribute('data-observaciones');

            document.getElementById('formEvaluar').action = '/docente/evidencias/' + evidenciaId + '/evaluar';
            document.getElementById('eval_nota').value = nota;
            document.getElementById('eval_obs').value = observaciones;

            openModal('modalEvaluar');
        });
    });

    // Evento para cerrar modal
    document.getElementById('btnCerrarModalEvaluar')?.addEventListener('click', function() {
        closeModal('modalEvaluar');
    });
});
</script>
@endpush
@endsection