<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EstudianteController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    // Módulo de Rendimiento Académico
    public function index() {
        $estudianteId = Auth::id();

        // Obtenemos las notas agrupadas por materia y lapso
        $notas = DB::table('calificacion')
            ->join('materia', 'calificacion.id_materia', '=', 'materia.id_materia')
            ->join('periodo_evaluacion', 'calificacion.id_periodo', '=', 'periodo_evaluacion.id_periodo')
            ->where('calificacion.id_usuario', $estudianteId)
            ->select(
                'materia.nombre as materia',
                'periodo_evaluacion.nombre as lapso',
                'calificacion.valor',
                'calificacion.tipo_evaluacion',
                'calificacion.fecha_registro'
            )
            ->orderBy('materia.nombre')
            ->get()
            ->groupBy('materia'); // Agrupamos para mostrarlo ordenado en la vista

        return view('estudiante.notas', compact('notas'));
    }

    // Módulo de Documentos
    public function documentos() {
        return view('estudiante.documentos');
    }

    public function descargarConstancia() {
        // Aquí luego conectaremos con la lógica de PDF
        return "Generando Constancia de Estudio para: " . Auth::user()->nombre;
    }
}