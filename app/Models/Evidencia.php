<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';

    protected $fillable = [
        'tarea_id',
        'alumno_id',
        'archivo_path',
        'nombre_archivo_original',
        'tamano_kb',
        'fecha_envio',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }

    public function calificacion()
    {
        return $this->hasOne(Calificacion::class);
    }

    public function entregadaATiempo(): bool
    {
        return $this->fecha_envio->lessThanOrEqualTo($this->tarea->fecha_entrega);
    }
}