<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion'
    ];

    public $timestamps = false;
}