@extends('layouts.app')

@section('title', 'Panel Alumno')
@section('header-title', 'Mis Tareas Pendientes')

@section('content')
@if($notificaciones->count() > 0)
    <div class="card" style="margin-bottom: 2rem; border-left: 4px solid var(--tropical-teal);">
        <h4 class="font-serif">🔔 Notificaciones Recientes</h4>
        <ul style="list-style: none; margin-top: 0.5rem;">
            @foreach($notificaciones as $notif)
                <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px dashed var(--border-color);">
                    <div>
                        <strong>{{ $notif->titulo }}:</strong> {{ $notif->mensaje }}
                    </div>
                    <form action="{{ route('alumno.notificaciones.leida', $notif->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;">Marcar Leída</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<h3 class="font-serif" style="margin-bottom: 1rem;">Tareas por Entregar</h3>

<div class="card-grid">
    @forelse($tareasPendientes as $tarea)
        <div class="card">
            <span class="badge badge-casa">{{ $tarea->tipo }}</span>
            <h4 class="font-serif" style="margin-top: 0.5rem;">{{ $tarea->titulo }}</h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.5rem 0;">
                Fecha Límite: {{ $tarea->fecha_entrega->format('d/m/Y H:i') }} hrs
            </p>
            <p style="margin-bottom: 1rem; font-size: 0.9rem;">{{ Str::limit($tarea->descripcion, 80) }}</p>
            <a href="{{ route('alumno.tareas.subir', $tarea->id) }}" class="btn btn-primary" style="width: 100%; text-align: center;">Subir Evidencia PDF</a>
        </div>
    @empty
        <p style="color: var(--text-muted)">No tienes tareas pendientes asignadas actualmente.</p>
    @endforelse
</div>
@endsection