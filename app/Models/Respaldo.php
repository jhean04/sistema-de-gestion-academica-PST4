<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respaldo extends Model
{
    protected $table = 'backup_log';
    protected $primaryKey = 'id_backup';
    public $timestamps = false; // Usamos la columna fecha_backup de la DB

    protected $fillable = [
        'nombre_archivo',
        'ruta',
        'tamaño_bytes',
        'realizado_por',
        'tipo',
        'estado',
        'observaciones'
    ];

    // Relación con el usuario que hizo el respaldo
    public function usuario()
    {
        return $this->belongsTo(User::class, 'realizado_por', 'id_usuario');
    }
}