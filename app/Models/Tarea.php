<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tarea extends Model
{
    protected $fillable = [
        'docente_id',
        'titulo',
        'descripcion',
        'tipo',
        'fecha_entrega',
        'cancelada',
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
        'cancelada' => 'boolean',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class);
    }

    public function plazoVencido(): bool
    {
        return Carbon::now()->greaterThan($this->fecha_entrega);
    }

    public function evidenciaDeAlumno($alumnoId)
    {
        return $this->evidencias()->where('alumno_id', $alumnoId)->first();
    }
}