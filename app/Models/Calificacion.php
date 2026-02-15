<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificacion';
    protected $primaryKey = 'id_calificacion';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',      // El estudiante
        'id_materia', 
        'id_periodo', 
        'id_asignacion',   // Relación con el docente
        'descripcion',     // Ejemplo: "Examen de Matemáticas"
        'valor',           // La nota (0-20 o según tu escala)
        'peso',            // Porcentaje de la nota
        'fecha_registro',
        'modificado_por'
    ];

    public function estudiante() {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function periodo() {
        return $this->belongsTo(PeriodoEvaluacion::class, 'id_periodo', 'id_periodo');
    }
}