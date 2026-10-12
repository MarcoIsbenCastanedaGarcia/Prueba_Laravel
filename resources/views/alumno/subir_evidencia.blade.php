@extends('layouts.app')

@section('title', 'Entregar Evidencia')
@section('header-title', 'Subir Evidencia: ' . $tarea->titulo)

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h3 class="font-serif">{{ $tarea->titulo }}</h3>
    <p style="margin: 0.5rem 0; color: var(--text-muted);"><strong>Tipo:</strong> {{ ucfirst($tarea->tipo) }}</p>
    <p style="margin-bottom: 1.5rem;">{{ $tarea->descripcion }}</p>

    @if($evidenciaPrevia)
        <div class="alert alert-success">
            Ya has enviado tu evidencia previamente ({{ $evidenciaPrevia->nombre_archivo_original }}).
        </div>
    @else
        <form action="{{ route('alumno.tareas.subir.store', $tarea->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="pdf_evidencia">Seleccionar único archivo PDF (Máximo 2MB)</label>
                <input type="file" id="pdf_evidencia" name="pdf_evidencia" accept="application/pdf" class="form-control" required>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
                <a href="{{ route('alumno.dashboard') }}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-success">Enviar Evidencia</button>
            </div>
        </form>
    @endif
</div>
@endsection