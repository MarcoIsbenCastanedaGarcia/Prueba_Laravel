@extends('layouts.app')

@section('title', 'Gestión de Alumnos')
@section('header-title', 'Directorio de Alumnos')

@section('content')
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
    <button type="button" id="btnAbrirNuevoAlumno" class="btn btn-primary">+ Captura Individual</button>
    <button type="button" id="btnAbrirCargaMasiva" class="btn btn-secondary">📂 Carga Masiva (CSV)</button>
</div>

<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumnos as $alumno)
                <tr>
                    <td>{{ $alumno->nombre }}</td>
                    <td>{{ $alumno->email }}</td>
                    <td>
                        @if($alumno->activo)
                            <span class="badge badge-clase">Activo</span>
                        @else
                            <span class="badge badge-cancelada">Inactivo</span>
                        @endif
                    </td>
                    <td style="display: flex; gap: 0.5rem;">
                        <button type="button" 
                                class="btn btn-secondary btn-editar-alumno" 
                                data-id="{{ $alumno->id }}" 
                                data-nombre="{{ $alumno->nombre }}" 
                                data-email="{{ $alumno->email }}"
                                style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                            Editar
                        </button>
                        
                        <form action="{{ route('docente.alumnos.toggle', $alumno) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn {{ $alumno->activo ? 'btn-danger' : 'btn-success' }}" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                {{ $alumno->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted)">No hay alumnos registrados actualmente.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Nuevo Alumno -->
<div id="modalNuevoAlumno" class="modal">
    <div class="modal-content">
        <h3 class="font-serif" style="margin-bottom: 1rem;">Registrar Alumno</h3>
        <form action="{{ route('docente.alumnos.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-danger btn-cerrar-modal" data-modal="modalNuevoAlumno">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Carga Masiva CSV -->
<div id="modalCargaMasiva" class="modal">
    <div class="modal-content">
        <h3 class="font-serif" style="margin-bottom: 1rem;">Carga Masiva de Alumnos</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
            Adjunte un archivo CSV con el formato: <code>Nombre, Email, Password</code>
        </p>
        <form action="{{ route('docente.alumnos.carga_masiva') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Archivo CSV</label>
                <input type="file" name="archivo_csv" accept=".csv" class="form-control" required>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-danger btn-cerrar-modal" data-modal="modalCargaMasiva">Cancelar</button>
                <button type="submit" class="btn btn-primary">Procesar Archivo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Alumno -->
<div id="modalEditarAlumno" class="modal">
    <div class="modal-content">
        <h3 class="font-serif" style="margin-bottom: 1rem;">Editar Alumno</h3>
        <form id="formEditarAlumno" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" id="edit_email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nueva Contraseña (Opcional)</label>
                <input type="password" name="password" class="form-control" minlength="6" placeholder="Dejar en blanco para conservar">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-danger btn-cerrar-modal" data-modal="modalEditarAlumno">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Abrir modales principales
    document.getElementById('btnAbrirNuevoAlumno')?.addEventListener('click', function() {
        openModal('modalNuevoAlumno');
    });

    document.getElementById('btnAbrirCargaMasiva')?.addEventListener('click', function() {
        openModal('modalCargaMasiva');
    });

    // Cerrar modales genérico
    document.querySelectorAll('.btn-cerrar-modal').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal');
            closeModal(modalId);
        });
    });

    // Evento delegación para el botón de editar
    document.querySelectorAll('.btn-editar-alumno').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const email = this.getAttribute('data-email');

            document.getElementById('formEditarAlumno').action = '/docente/alumnos/' + id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_email').value = email;

            openModal('modalEditarAlumno');
        });
    });
});
</script>
@endpush
@endsection