<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnoEscolar;
use App\Models\GradoSeccion;
use App\Models\PeriodoEvaluacion;
use App\Models\Materia;
use App\Models\PlanEstudio;

class AcademicoController extends Controller
{
    public function index()
    {
        $anios = AnoEscolar::with('gradosSecciones')->orderBy('fecha_inicio', 'desc')->get();
        $materias = Materia::all();
        $periodos = PeriodoEvaluacion::with('anoEscolar')->get();
        return view('admin.academico.index', compact('anios', 'materias', 'periodos'));
    }

    public function createAnio() { return view('admin.academico.create_anio'); }

    public function createGrado()
    {
        $anios = AnoEscolar::where('activo', 1)->orderBy('fecha_inicio', 'desc')->get();
        return view('admin.academico.create_grado', compact('anios'));
    }

    public function createPeriodo()
    {
        $anios = AnoEscolar::where('activo', 1)->orderBy('fecha_inicio', 'desc')->get();
        return view('admin.academico.create_periodo', compact('anios'));
    }

    public function planEstudio($id)
    {
        $grado = GradoSeccion::findOrFail($id);
        $materiasDisponibles = Materia::all();
        $plan = PlanEstudio::where('id_grado_sec', $id)->with('materia')->get();
        return view('admin.academico.plan', compact('grado', 'materiasDisponibles', 'plan'));
    }

    public function storeAnio(Request $request)
    {
        $request->validate(['nombre' => 'required', 'fecha_inicio' => 'required|date', 'fecha_fin' => 'required|date']);
        AnoEscolar::create(['nombre' => $request->nombre, 'fecha_inicio' => $request->fecha_inicio, 'fecha_fin' => $request->fecha_fin, 'activo' => 1]);
        return redirect()->route('academico.index')->with('success', 'Año escolar creado.');
    }

    public function storeGrado(Request $request)
    {
        $request->validate(['nombre' => 'required', 'nivel' => 'required', 'turno' => 'required', 'capacidad_max' => 'required|integer', 'id_ano_escolar' => 'required']);
        GradoSeccion::create($request->all());
        return redirect()->route('academico.index')->with('success', 'Grado creado correctamente.');
    }

    public function storePeriodo(Request $request)
    {
        $request->validate(['nombre' => 'required', 'fecha_inicio' => 'required|date', 'fecha_fin' => 'required|date', 'peso_porcentaje' => 'required|numeric', 'id_ano_escolar' => 'required']);
        PeriodoEvaluacion::create($request->all());
        return redirect()->route('academico.index')->with('success', 'Periodo de evaluación registrado.');
    }

    public function storePlan(Request $request)
    {
        $request->validate(['id_grado_sec' => 'required', 'id_materia' => 'required', 'horas_semanales' => 'required|integer']);
        PlanEstudio::create($request->all());
        return back()->with('success', 'Materia asignada al grado.');
    }

    // NUEVOS MÉTODOS DE EDICIÓN Y ACTUALIZACIÓN
    public function editAnio($id)
    {
        $anio = AnoEscolar::findOrFail($id);
        return view('admin.academico.edit_anio', compact('anio'));
    }

    public function updateAnio(Request $request, $id)
    {
        $request->validate(['nombre' => 'required', 'fecha_inicio' => 'required|date', 'fecha_fin' => 'required|date', 'activo' => 'required']);
        $anio = AnoEscolar::findOrFail($id);
        $anio->update($request->all());
        return redirect()->route('academico.index')->with('success', 'Año escolar actualizado.');
    }

    public function editGrado($id)
    {
        $grado = GradoSeccion::findOrFail($id);
        $anios = AnoEscolar::where('activo', 1)->get();
        return view('admin.academico.edit_grado', compact('grado', 'anios'));
    }

    public function updateGrado(Request $request, $id)
    {
        $request->validate(['nombre' => 'required', 'nivel' => 'required', 'turno' => 'required', 'capacidad_max' => 'required|integer', 'id_ano_escolar' => 'required']);
        $grado = GradoSeccion::findOrFail($id);
        $grado->update($request->all());
        return redirect()->route('academico.index')->with('success', 'Grado actualizado correctamente.');
    }
}