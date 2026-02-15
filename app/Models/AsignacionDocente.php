<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionDocente extends Model
{
    protected $table = 'asignacion_docente';
    protected $primaryKey = 'id_asignacion';
    public $timestamps = false;

    // Se agregaron los campos faltantes a fillable para que Laravel no los ignore
    protected $fillable = [
        'id_usuario', 
        'id_grado_sec', 
        'id_materia', 
        'id_ano_escolar', 
        'fecha_asignacion', 
        'horario',
        'carga_horaria',
        'titular',
        'activo'
    ];

    public function docente() {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function gradoSeccion() {
        return $this->belongsTo(GradoSeccion::class, 'id_grado_sec', 'id_grado_sec');
    }

    public function materia() {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }
}