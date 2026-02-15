<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';
    protected $primaryKey = 'id_inscripcion';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_grado_sec',
        'id_ano_escolar',
        'fecha_inscripcion',
        'estado',
        'numero_lista',
        'observaciones'
    ];

    public function estudiante() {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function gradoSeccion() {
        return $this->belongsTo(GradoSeccion::class, 'id_grado_sec', 'id_grado_sec');
    }
}