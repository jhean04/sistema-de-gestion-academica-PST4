<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanEstudio extends Model
{
    protected $table = 'plan_estudio';
    protected $primaryKey = 'id_plan';
    public $timestamps = false;

    protected $fillable = [
        'id_grado_sec', 
        'id_materia', 
        'horas_semanales'
    ];

    // Relación para obtener el nombre de la materia
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    // Relación para obtener el grado
    public function gradoSeccion()
    {
        return $this->belongsTo(GradoSeccion::class, 'id_grado_sec', 'id_grado_sec');
    }
}