<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AsignacionDocente;
use App\Models\Inscripcion;
use App\Models\Calificacion;
use App\Models\PeriodoEvaluacion;
use App\Models\AnoEscolar;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
{
    public function index()
    {
        $anioActivo = AnoEscolar::where('activo', 1)->first();
        
        $query = AsignacionDocente::with(['gradoSeccion', 'materia', 'docente'])
                ->where('id_ano_escolar', $anioActivo->id_ano ?? 0);
        
        if (Auth::user()->tipo_usuario == 'DOCENTE') {
            $query->where('id_usuario', Auth::id());
        }

        $misAsignaciones = $query->get();

        return view('admin.calificaciones.index', compact('misAsignaciones', 'anioActivo'));
    }

    public function gestionar($id_asignacion)
    {
        $asignacion = AsignacionDocente::with(['gradoSeccion', 'materia'])->findOrFail($id_asignacion);
        
        $alumnos = Inscripcion::with('estudiante')
            ->where('id_grado_sec', $asignacion->id_grado_sec)
            ->where('id_ano_escolar', $asignacion->id_ano_escolar)
            ->get();

        $periodos = PeriodoEvaluacion::where('id_ano_escolar', $asignacion->id_ano_escolar)->get();

        return view('admin.calificaciones.gestionar', compact('asignacion', 'alumnos', 'periodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notas' => 'required|array',
            'id_periodo' => 'required',
            'descripcion' => 'required',
            'peso' => 'required|numeric'
        ]);

        foreach ($request->notas as $id_estudiante => $valor) {
            if ($valor !== null) {
                Calificacion::create([
                    'id_usuario' => $id_estudiante,
                    'id_materia' => $request->id_materia,
                    'id_periodo' => $request->id_periodo,
                    'id_asignacion' => $request->id_asignacion,
                    'descripcion' => $request->descripcion,
                    'valor' => $valor,
                    'peso' => $request->peso,
                    'fecha_registro' => now(),
                    'modificado_por' => Auth::id()
                ]);
            }
        }

        return redirect()->route('calificaciones.index')->with('success', 'Calificaciones cargadas correctamente.');
    }
}