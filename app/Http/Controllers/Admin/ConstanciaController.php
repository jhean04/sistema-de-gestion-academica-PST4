<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ConstanciaController extends Controller
{
    public function generar($id_inscripcion)
    {
        // Obtenemos la inscripción con todas sus relaciones
        $inscripcion = Inscripcion::with(['usuario', 'gradoSeccion', 'anioEscolar'])
            ->findOrFail($id_inscripcion);

        $data = [
            'fecha' => date('d/m/Y'),
            'inscripcion' => $inscripcion,
            'estudiante' => $inscripcion->usuario,
            'grado' => $inscripcion->gradoSeccion,
            'anio' => $inscripcion->anioEscolar
        ];

        // Cargamos la vista que diseñaremos a continuación
        $pdf = Pdf::loadView('admin.reportes.constancia_inscripcion', $data);

        // Retornamos el PDF para ver en navegador o descargar
        return $pdf->stream("Constancia_{$inscripcion->usuario->cedula}.pdf");
    }
}