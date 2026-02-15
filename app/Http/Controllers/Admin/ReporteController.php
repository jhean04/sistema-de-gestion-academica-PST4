<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GradoSeccion;
use App\Models\Inscripcion;
use App\Models\Calificacion;
use App\Models\AnoEscolar;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $anioActivo = AnoEscolar::where('activo', 1)->first();
        $grados = GradoSeccion::where('id_ano_escolar', $anioActivo->id_ano ?? 0)->get();

        return view('admin.reportes.index', compact('grados', 'anioActivo'));
    }

    public function verGrado($id_grado_sec)
    {
        $grado = GradoSeccion::findOrFail($id_grado_sec);
        
        // Obtenemos los alumnos y calculamos su promedio general en este grado
        $estudiantes = Inscripcion::with('estudiante')
            ->where('id_grado_sec', $id_grado_sec)
            ->get();

        foreach ($estudiantes as $est) {
            $est->promedio_general = Calificacion::where('id_usuario', $est->id_usuario)
                ->avg('valor') ?: 0;
        }

        return view('admin.reportes.ver_grado', compact('grado', 'estudiantes'));
    }
}