<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GradoSeccion;
use App\Models\Inscripcion;
use App\Models\AnoEscolar;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function index()
    {
        $anioActivo = AnoEscolar::where('activo', 1)->first();
        
        // Estudiantes que NO están inscritos en el año actual
        $estudiantesDisponibles = User::where('tipo_usuario', 'ESTUDIANTE')
            ->whereNotExists(function ($query) use ($anioActivo) {
                $query->select(DB::raw(1))
                    ->from('inscripcion')
                    ->whereRaw('inscripcion.id_usuario = usuario.id_usuario')
                    ->where('id_ano_escolar', $anioActivo->id_ano ?? 0);
            })->get();

        $grados = GradoSeccion::where('id_ano_escolar', $anioActivo->id_ano ?? 0)->get();

        // Lista de inscritos actualmente
        $inscripciones = Inscripcion::with(['estudiante', 'gradoSeccion'])
            ->where('id_ano_escolar', $anioActivo->id_ano ?? 0)
            ->get();

        return view('admin.inscripciones.index', compact('estudiantesDisponibles', 'grados', 'inscripciones', 'anioActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required',
            'id_grado_sec' => 'required',
            'id_ano_escolar' => 'required',
            'fecha_inscripcion' => 'required|date'
        ]);

        try {
            Inscripcion::create([
                'id_usuario' => $request->id_usuario,
                'id_grado_sec' => $request->id_grado_sec,
                'id_ano_escolar' => $request->id_ano_escolar,
                'fecha_inscripcion' => $request->fecha_inscripcion,
                'estado' => 'ACTIVO'
            ]);

            return redirect()->back()->with('success', 'Estudiante inscrito correctamente.');
        } catch (\Exception $e) {
            // Aquí capturamos el error del TRIGGER de capacidad máxima si ocurre
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion::delete();
        return redirect()->back()->with('success', 'Inscripción anulada correctamente.');
    }
}