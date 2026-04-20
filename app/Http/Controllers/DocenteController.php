<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra las secciones asignadas al docente.
     */
    public function index()
    {
        $docenteId = Auth::id(); 

        $secciones = DB::table('asignacion_docente')
            ->join('materia', 'asignacion_docente.id_materia', '=', 'materia.id_materia')
            ->join('grado_seccion', 'asignacion_docente.id_grado_sec', '=', 'grado_seccion.id_grado_sec')
            ->where('asignacion_docente.id_usuario', $docenteId)
            ->select(
                'materia.nombre as materia',
                'grado_seccion.nombre as grado',
                'grado_seccion.turno',
                'asignacion_docente.id_grado_sec',
                'asignacion_docente.id_materia'
            )
            ->get();

        return view('docente.secciones', compact('secciones'));
    }

    /**
     * Lista los estudiantes y sus notas.
     */
    public function verEstudiantes($id_grado_sec, $id_materia)
    {
        $estudiantes = DB::table('inscripcion')
            ->join('usuario', 'inscripcion.id_usuario', '=', 'usuario.id_usuario')
            ->where('inscripcion.id_grado_sec', $id_grado_sec)
            ->where('inscripcion.estado', 'ACTIVO')
            ->select('usuario.id_usuario', 'usuario.nombre', 'usuario.apellido', 'usuario.cedula', 'inscripcion.numero_lista')
            ->orderBy('inscripcion.numero_lista', 'ASC')
            ->get();

        $notasRaw = DB::table('calificacion')
            ->where('id_materia', $id_materia)
            ->get();

        $notasDB = [];
        foreach ($notasRaw as $nota) {
            $notasDB[$nota->id_usuario][$nota->tipo_evaluacion] = $nota->valor;
        }

        // Simulación de configuración que podrá venir de la base de datos después
        $configuracion = [
            ['id' => 'EXAMEN', 'label' => 'Ev1'],
            ['id' => 'TAREA', 'label' => 'Ev2'],
            ['id' => 'PROYECTO', 'label' => 'Ev3'],
            ['id' => 'PARTICIPACION', 'label' => 'Ev4'],
            ['id' => 'LABORATORIO', 'label' => 'Ev5'],
        ];

        $materia = DB::table('materia')->where('id_materia', $id_materia)->first();

        return view('docente.estudiantes', [
            'estudiantes' => $estudiantes,
            'id_materia' => $id_materia,
            'id_grado_sec' => $id_grado_sec,
            'materia_nombre' => $materia->nombre ?? 'Materia',
            'notasDB' => $notasDB,
            'columnas' => $configuracion
        ]);
    }

    /**
     * Guarda las notas de forma masiva.
     */
    public function guardarNotasMasivo(Request $request)
    {
        $request->validate([
            'id_materia' => 'required',
            'id_grado_sec' => 'required',
            'notas' => 'required|array'
        ]);

        $asignacion = DB::table('asignacion_docente')
            ->where('id_usuario', Auth::id())
            ->where('id_materia', $request->id_materia)
            ->where('id_grado_sec', $request->id_grado_sec)
            ->first();

        if (!$asignacion) {
            return redirect()->back()->with('error', 'No autorizado.');
        }

        foreach ($request->notas as $id_estudiante => $evaluaciones) {
            foreach ($evaluaciones as $tipoEval => $valor) {
                if ($valor !== null && $valor !== '') {
                    DB::table('calificacion')->updateOrInsert(
                        [
                            'id_usuario' => $id_estudiante,
                            'id_materia' => $request->id_materia,
                            'id_periodo' => 1, 
                            'tipo_evaluacion' => $tipoEval,
                        ],
                        [
                            'id_asignacion' => $asignacion->id_asignacion,
                            'valor' => $valor,
                            'peso' => 1.00,
                            'fecha_registro' => now(),
                        ]
                    );
                }
            }
        }

        return redirect()->back()->with('info', 'Calificaciones sincronizadas correctamente.');
    }

    // Funciones adicionales que podrías estar usando:
    public function reporteSeccion($id_grado_sec, $id_materia) {
        $info = DB::table('grado_seccion')->crossJoin('materia')
            ->where('grado_seccion.id_grado_sec', $id_grado_sec)
            ->where('materia.id_materia', $id_materia)
            ->select('grado_seccion.nombre as grado', 'materia.nombre as materia')->first();

        $notas = DB::table('inscripcion')
            ->join('usuario', 'inscripcion.id_usuario', '=', 'usuario.id_usuario')
            ->leftJoin('calificacion', function ($join) use ($id_materia) {
                $join->on('usuario.id_usuario', '=', 'calificacion.id_usuario')
                    ->where('calificacion.id_materia', '=', $id_materia);
            })
            ->where('inscripcion.id_grado_sec', $id_grado_sec)
            ->select('usuario.nombre', 'usuario.apellido', 'calificacion.valor', 'calificacion.tipo_evaluacion')
            ->orderBy('usuario.apellido', 'ASC')->get();

        return view('docente.reporte', compact('notas', 'info', 'id_grado_sec', 'id_materia'));
    }
}