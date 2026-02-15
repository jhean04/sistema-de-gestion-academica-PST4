<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnoEscolar extends Model
{
    protected $table = 'ano_escolar';
    protected $primaryKey = 'id_ano';
    
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'activo'
    ];

    public $timestamps = false;

    // Relación con Grados
    public function gradosSecciones()
    {
        return $this->hasMany(GradoSeccion::class, 'id_ano_escolar', 'id_ano');
    }
}