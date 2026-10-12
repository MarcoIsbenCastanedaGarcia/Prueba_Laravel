<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tarea;
use App\Models\Evidencia;
use App\Models\Calificacion;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DocenteController extends Controller
{
    public function dashboard()
    {
        $totalAlumnos = User::where('role', 'alumno')->where('activo', true)->count();
        $totalTareas = Tarea::count();
        $tareasActivas = Tarea::where('cancelada', false)->count();

        $ultimasTareas = Tarea::latest()->take(5)->get();

        return view('docente.dashboard', compact('totalAlumnos', 'totalTareas', 'tareasActivas', 'ultimasTareas'));
    }

    // --- GESTIÓN DE ALUMNOS ---
    public function alumnosIndex()
    {
        $alumnos = User::where('role', 'alumno')->orderBy('nombre')->get();
        return view('docente.alumnos.index', compact('alumnos'));
    }

    public function alumnoStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'alumno',
            'activo' => true,
        ]);

        return redirect()->route('docente.alumnos.index')->with('success', 'Alumno registrado correctamente.');
    }

    public function alumnoCargaMasiva(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('archivo_csv')->getRealPath();
        $file = fopen($path, 'r');
        
        // Omitir encabezado si existe
        fgetcsv($file);

        $registrados = 0;
        $errores = 0;

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (count($row) >= 3) {
                $nombre = trim($row[0]);
                $email = trim($row[1]);
                $password = trim($row[2]);

                if (!User::where('email', $email)->exists() && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    User::create([
                        'nombre' => $nombre,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role' => 'alumno',
                        'activo' => true,
                    ]);
                    $registrados++;
                } else {
                    $errores++;
                }
            }
        }
        fclose($file);

        return redirect()->route('docente.alumnos.index')->with('success', "Carga masiva completada: $registrados registrados con éxito. ($errores omitidos).");
    }

    public function alumnoUpdate(Request $request, User $alumno)
    {
        $alumnoId = $alumno->id;

        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $alumnoId,
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $alumno->update($data);

        return redirect()->route('docente.alumnos.index')->with('success', 'Datos del alumno actualizados.');
    }

    public function alumnoToggleEstado(User $alumno)
    {
        $alumno->activo = !$alumno->activo;
        $alumno->save();

        $estado = $alumno->activo ? 'activado' : 'desactivado';
        return redirect()->route('docente.alumnos.index')->with('success', "Alumno $estado correctamente.");
    }

    // --- GESTIÓN DE TAREAS ---
    public function tareasIndex()
    {
        $tareas = Tarea::withCount('evidencias')->latest()->get();
        return view('docente.tareas.index', compact('tareas'));
    }

    public function tareaCreate()
    {
        return view('docente.tareas.create');
    }

    public function tareaStore(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo' => 'required|in:tareas en casa,actividades en clase,exposiciones',
            'fecha_entrega_dia' => 'required|date|after_or_equal:today',
        ]);

        $fechaLimite = Carbon::parse($request->fecha_entrega_dia)->endOfDay();

        /** @var User $user */
        $user = Auth::user();

        $tarea = Tarea::create([
            'docente_id' => $user->id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'fecha_entrega' => $fechaLimite,
            'cancelada' => false,
        ]);

        // Notificar a todos los alumnos
        $alumnos = User::where('role', 'alumno')->where('activo', true)->get();
        foreach ($alumnos as $alumno) {
            Notificacion::create([
                'user_id' => $alumno->id,
                'titulo' => 'Nueva tarea asignada',
                'mensaje' => "Se ha asignado la tarea '{$tarea->titulo}' de tipo {$tarea->tipo}. Fecha límite: {$fechaLimite->format('d/m/Y H:i')}.",
            ]);
        }

        return redirect()->route('docente.tareas.index')->with('success', 'Tarea creada y notificada a los alumnos.');
    }

    public function tareaCancelar(Tarea $tarea)
    {
        $tarea->update(['cancelada' => true]);

        $alumnos = User::where('role', 'alumno')->get();
        foreach ($alumnos as $alumno) {
            Notificacion::create([
                'user_id' => $alumno->id,
                'titulo' => 'Tarea Cancelada',
                'mensaje' => "La tarea '{$tarea->titulo}' ha sido cancelada por el docente.",
            ]);
        }

        return redirect()->route('docente.tareas.index')->with('success', 'Tarea cancelada correctamente.');
    }

    public function tareaDetalle(Tarea $tarea)
    {
        $alumnos = User::where('role', 'alumno')->get();
        $evidencias = Evidencia::where('tarea_id', $tarea->id)->with('calificacion', 'alumno')->get()->keyBy('alumno_id');

        return view('docente.tareas.detalle', compact('tarea', 'alumnos', 'evidencias'));
    }

    // --- EVALUACIÓN DE TAREAS ---
    public function evaluarEvidencia(Request $request, Evidencia $evidencia)
    {
        if (!$evidencia->tarea->plazoVencido()) {
            return back()->with('error', 'Únicamente se pueden evaluar evidencias después de la fecha y hora de entrega límite.');
        }

        $request->validate([
            'nota' => 'required|numeric|min:0|max:10',
            'observaciones' => 'nullable|string',
            'accion' => 'required|in:borrador,publicar',
        ]);

        $estado = $request->accion === 'publicar' ? 'publicada' : 'borrador';

        /** @var User $user */
        $user = Auth::user();

        Calificacion::updateOrCreate(
            ['evidencia_id' => $evidencia->id],
            [
                'docente_id' => $user->id,
                'nota' => $request->nota,
                'observaciones' => $request->observaciones,
                'estado' => $estado,
            ]
        );

        $mensaje = $estado === 'publicada' 
            ? 'Calificación publicada y ahora es visible para el alumno.' 
            : 'Calificación guardada como borrador.';

        return back()->with('success', $mensaje);
    }
}