<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'role',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function esDocente(): bool
    {
        return $this->role === 'docente';
    }

    public function esAlumno(): bool
    {
        return $this->role === 'alumno';
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'alumno_id');
    }

    public function tareasCreadas()
    {
        return $this->hasMany(Tarea::class, 'docente_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'user_id');
    }
}