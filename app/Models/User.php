<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'cedula',
        'nombre',
        'apellido',
        'email',
        'password_hash',
        'tipo_usuario',
        'activo',
        'fecha_registro',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'foto_perfil'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function adminlte_profile_url()
    {
        return route('profile.show');
    }

    public function adminlte_desc()
    {
        return 'Rol: ' . $this->tipo_usuario;
    }

    public function adminlte_image()
    {
        // Si el usuario tiene foto, devuelve su ruta, si no, una de placeholder
        if ($this->foto_perfil) {
            return asset('storage/' . $this->foto_perfil);
        }

        // Imagen por defecto (puedes usar una local o un servicio de placeholder)
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nombre) . '&background=random';
    }

    public function getNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}
