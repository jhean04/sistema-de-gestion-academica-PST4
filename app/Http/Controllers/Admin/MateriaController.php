<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Materia;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::all();
        return view('admin.materias.index', compact('materias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:materia,nombre',
            'codigo' => 'required|unique:materia,codigo'
        ]);

        Materia::create($request->all());

        return redirect()->route('materias.index')->with('success', 'Materia creada correctamente.');
    }

    public function destroy($id)
    {
        $materia = Materia::findOrFail($id);
        
        try {
            $materia->delete();
            return redirect()->route('materias.index')->with('success', 'Materia eliminada.');
        } catch (\Exception $e) {
            return redirect()->route('materias.index')->with('error', 'No se puede eliminar la materia porque está asignada a un plan de estudios.');
        }
    }
}