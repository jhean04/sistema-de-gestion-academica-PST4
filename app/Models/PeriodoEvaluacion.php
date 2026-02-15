<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoEvaluacion extends Model
{
    protected $table = 'periodo_evaluacion';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;

    protected $fillable = [
        'id_ano_escolar',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'peso_porcentaje'
    ];

    // Relación con el Año Escolar
    public function anoEscolar()
    {
        return $this->belongsTo(AnoEscolar::class, 'id_ano_escolar', 'id_ano');
    }
}