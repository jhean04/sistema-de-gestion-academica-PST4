<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Materia;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Contadores para las tarjetas superiores
        $totalEstudiantes = User::where('tipo_usuario', 'ESTUDIANTE')->count();
        $totalDocentes = User::where('tipo_usuario', 'DOCENTE')->count();
        $totalMaterias = Materia::count();
        $totalInscritos = Inscripcion::where('estado', 'ACTIVO')->count();

        // Consulta corregida con los nombres reales de tu DB
        $estudiantesPorGrado = DB::table('grado_seccion')
            ->join('inscripcion', 'grado_seccion.id_grado_sec', '=', 'inscripcion.id_grado_sec')
            ->select(
                'grado_seccion.nombre as nombre_completo', 
                DB::raw('count(*) as total')
            )
            ->groupBy('grado_seccion.nombre')
            ->get();

        return view('home', compact(
            'totalEstudiantes', 
            'totalDocentes', 
            'totalMaterias', 
            'totalInscritos',
            'estudiantesPorGrado'
        ));
    }
}