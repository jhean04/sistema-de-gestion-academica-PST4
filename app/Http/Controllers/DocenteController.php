<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de que el modelo User esté importado

class DocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $docenteId = Auth::id(); // Obtenemos el ID del docente logueado

        // Buscamos sus secciones asignadas
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

    public function verEstudiantes($id_grado_sec, $id_materia)
    {
        // Buscamos los estudiantes inscritos en este grado/sección
        $estudiantes = DB::table('inscripcion')
            ->join('usuario', 'inscripcion.id_usuario', '=', 'usuario.id_usuario')
            ->join('grado_seccion', 'inscripcion.id_grado_sec', '=', 'grado_seccion.id_grado_sec')
            ->where('inscripcion.id_grado_sec', $id_grado_sec)
            ->where('inscripcion.estado', 'ACTIVO')
            ->select(
                'usuario.id_usuario', // AGREGADO: Necesario para el botón de evaluar
                'usuario.nombre',
                'usuario.apellido',
                'usuario.cedula',
                'inscripcion.numero_lista',
                'grado_seccion.nombre as grado_nombre'
            )
            ->orderBy('inscripcion.numero_lista', 'ASC')
            ->get();

        // Obtenemos el nombre de la materia para el encabezado
        $materia = DB::table('materia')->where('id_materia', $id_materia)->first();
        $materia_nombre = $materia ? $materia->nombre : 'Materia';

        // Retornamos la vista con todas las variables necesarias para los botones
        return view('docente.estudiantes', [
            'estudiantes' => $estudiantes,
            'id_materia' => $id_materia,
            'id_grado_sec' => $id_grado_sec,
            'materia_nombre' => $materia_nombre
        ]);
    }

    public function formularioNota($id_usuario, $id_materia)
    {
        // Usamos el modelo User para encontrar al estudiante
        $estudiante = User::findOrFail($id_usuario);

        $materia = DB::table('materia')->where('id_materia', $id_materia)->first();

        // Obtenemos los periodos de evaluación activos
        $periodos = DB::table('periodo_evaluacion')->get();

        return view('docente.evaluar', compact('estudiante', 'materia', 'periodos'));
    }

    public function guardarNota(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required',
            'id_materia' => 'required',
            'valor' => 'required|numeric|min:0|max:20',
            'id_periodo' => 'required',
            'tipo_evaluacion' => 'required'
        ]);

        // 1. Buscamos el id_asignacion que corresponde a este docente y esta materia
        // Necesitamos saber el grado_sec, así que lo buscamos primero desde la inscripción del alumno
        $inscripcion = DB::table('inscripcion')
            ->where('id_usuario', $request->id_usuario)
            ->first();

        if (!$inscripcion) {
            return redirect()->back()->with('error', 'El estudiante no tiene una inscripción activa.');
        }

        $asignacion = DB::table('asignacion_docente')
            ->where('id_usuario', Auth::id())
            ->where('id_materia', $request->id_materia)
            ->where('id_grado_sec', $inscripcion->id_grado_sec)
            ->first();

        // 2. Si no existe la asignación, no podemos guardar la nota
        if (!$asignacion) {
            return redirect()->back()->with('error', 'No tienes permiso para evaluar esta materia en esta sección.');
        }

        // 3. Ahora sí insertamos incluyendo el id_asignacion
        DB::table('calificacion')->insert([
            'id_usuario' => $request->id_usuario,
            'id_materia' => $request->id_materia,
            'id_periodo' => $request->id_periodo,
            'id_asignacion' => $asignacion->id_asignacion, // ESTO ERA LO QUE FALTABA
            'valor' => $request->valor,
            'tipo_evaluacion' => $request->tipo_evaluacion,
            'fecha_registro' => now(),
        ]);

        return redirect()->route('docente.secciones')->with('info', 'Calificación registrada con éxito.');
    }

    public function reporteSeccion($id_grado_sec, $id_materia)
    {
        // Obtenemos información de la sección y materia
        $info = DB::table('grado_seccion')
            ->crossJoin('materia')
            ->where('grado_seccion.id_grado_sec', $id_grado_sec)
            ->where('materia.id_materia', $id_materia)
            ->select('grado_seccion.nombre as grado', 'materia.nombre as materia')
            ->first();

        // Obtenemos los estudiantes y sus notas en esta materia
        $notas = DB::table('inscripcion')
            ->join('usuario', 'inscripcion.id_usuario', '=', 'usuario.id_usuario')
            ->leftJoin('calificacion', function ($join) use ($id_materia) {
                $join->on('usuario.id_usuario', '=', 'calificacion.id_usuario')
                    ->where('calificacion.id_materia', '=', $id_materia);
            })
            ->leftJoin('periodo_evaluacion', 'calificacion.id_periodo', '=', 'periodo_evaluacion.id_periodo')
            ->where('inscripcion.id_grado_sec', $id_grado_sec)
            ->select(
                'usuario.nombre',
                'usuario.apellido',
                'calificacion.valor',
                'calificacion.tipo_evaluacion',
                'periodo_evaluacion.nombre as periodo'
            )
            ->orderBy('usuario.apellido', 'ASC')
            ->get();

        return view('docente.reporte', compact('notas', 'info', 'id_grado_sec', 'id_materia'));
    }
}
