<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradoSeccion extends Model
{
    protected $table = 'grado_seccion';
    protected $primaryKey = 'id_grado_sec';

    protected $fillable = [
        'nombre',
        'nivel',
        'turno',
        'capacidad_max',
        'id_ano_escolar'
    ];

    public $timestamps = false;

    // Relación inversa con Año Escolar
    public function anoEscolar()
    {
        return $this->belongsTo(AnoEscolar::class, 'id_ano_escolar', 'id_ano');
    }
}