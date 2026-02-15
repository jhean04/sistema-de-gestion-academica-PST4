<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GradoSeccion;
use App\Models\Materia;
use App\Models\AsignacionDocente;
use App\Models\AnoEscolar;

class AsignacionController extends Controller
{
    public function index()
    {
        $anioActivo = AnoEscolar::where('activo', 1)->first();
        $docentes = User::where('tipo_usuario', 'DOCENTE')->get();
        $grados = GradoSeccion::where('id_ano_escolar', $anioActivo->id_ano ?? 0)->get();
        $materias = Materia::where('activa', 1)->get();

        $asignaciones = AsignacionDocente::with(['docente', 'gradoSeccion', 'materia'])
            ->where('id_ano_escolar', $anioActivo->id_ano ?? 0)
            ->get();

        return view('admin.asignaciones.index', compact('docentes', 'grados', 'materias', 'asignaciones', 'anioActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required',
            'id_grado_sec' => 'required',
            'id_materia' => 'required',
            'id_ano_escolar' => 'required',
            'fecha_asignacion' => 'required|date'
        ]);

        $existe = AsignacionDocente::where([
            ['id_usuario', $request->id_usuario],
            ['id_grado_sec', $request->id_grado_sec],
            ['id_materia', $request->id_materia],
            ['id_ano_escolar', $request->id_ano_escolar],
        ])->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Este docente ya tiene asignada esta materia en ese grado.');
        }

        AsignacionDocente::create($request->all());

        return redirect()->back()->with('success', 'Asignación realizada con éxito.');
    }

    public function destroy($id)
    {
        $asignacion = AsignacionDocente::findOrFail($id);
        $asignacion->delete();
        return redirect()->back()->with('success', 'Asignación eliminada.');
    }
}