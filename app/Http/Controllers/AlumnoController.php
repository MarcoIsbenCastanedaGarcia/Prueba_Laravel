<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tarea;
use App\Models\Evidencia;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AlumnoController extends Controller
{
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();
        $alumnoId = $user->id;

        $tareasPendientes = Tarea::where('cancelada', false)
            ->where('fecha_entrega', '>=', Carbon::now())
            ->whereDoesntHave('evidencias', function ($query) use ($alumnoId) {
                $query->where('alumno_id', $alumnoId);
            })
            ->orderBy('fecha_entrega', 'asc')
            ->get();

        $notificaciones = Notificacion::where('user_id', $alumnoId)
            ->where('leida', false)
            ->latest()
            ->take(5)
            ->get();

        return view('alumno.dashboard', compact('tareasPendientes', 'notificaciones'));
    }

    public function historial()
    {
        /** @var User $user */
        $user = Auth::user();
        $alumnoId = $user->id;

        $evidencias = Evidencia::where('alumno_id', $alumnoId)
            ->with(['tarea', 'calificacion' => function ($query) {
                $query->where('estado', 'publicada');
            }])
            ->latest()
            ->get();

        return view('alumno.historial', compact('evidencias'));
    }

    public function subirEvidenciaForm(Tarea $tarea)
    {
        if ($tarea->cancelada) {
            return redirect()->route('alumno.dashboard')->with('error', 'Esta tarea ha sido cancelada.');
        }

        if ($tarea->plazoVencido()) {
            return redirect()->route('alumno.dashboard')->with('error', 'El plazo de entrega para esta tarea ha expirado.');
        }

        /** @var User $user */
        $user = Auth::user();

        $evidenciaPrevia = Evidencia::where('tarea_id', $tarea->id)
            ->where('alumno_id', $user->id)
            ->first();

        return view('alumno.subir_evidencia', compact('tarea', 'evidenciaPrevia'));
    }

    public function subirEvidenciaStore(Request $request, Tarea $tarea)
    {
        if ($tarea->cancelada || $tarea->plazoVencido()) {
            return back()->with('error', 'No es posible entregar la evidencia fuera de tiempo o en tareas canceladas.');
        }

        $request->validate([
            'pdf_evidencia' => 'required|file|mimes:pdf|max:2048',
        ], [
            'pdf_evidencia.required' => 'Debe adjuntar un archivo PDF.',
            'pdf_evidencia.mimes' => 'El archivo debe ser exclusivamente en formato PDF.',
            'pdf_evidencia.max' => 'El tamaño del archivo PDF no debe exceder los 2 MB.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $alumnoId = $user->id;

        $evidenciaExistente = Evidencia::where('tarea_id', $tarea->id)->where('alumno_id', $alumnoId)->first();
        if ($evidenciaExistente) {
            return back()->with('error', 'Ya has enviado una evidencia para esta tarea previamente.');
        }

        $file = $request->file('pdf_evidencia');
        $filename = time() . '_' . $alumnoId . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('evidencias', $filename, 'public');

        Evidencia::create([
            'tarea_id' => $tarea->id,
            'alumno_id' => $alumnoId,
            'archivo_path' => $path,
            'nombre_archivo_original' => $file->getClientOriginalName(),
            'tamano_kb' => round($file->getSize() / 1024),
            'fecha_envio' => Carbon::now(),
        ]);

        return redirect()->route('alumno.historial')->with('success', 'Evidencia subida correctamente.');
    }

    public function marcarNotificacionLeida(Notificacion $notificacion)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($notificacion->user_id === $user->id) {
            $notificacion->update(['leida' => true]);
        }
        return back();
    }
}