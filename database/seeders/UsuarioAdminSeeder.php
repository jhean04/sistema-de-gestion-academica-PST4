<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuario')->insert([
            'cedula' => 'V-12345678',
            'nombre' => 'Admin',
            'apellido' => 'Principal',
            'email' => 'admin@sigal.com',
            'tipo_usuario' => 'ADMINISTRATIVO',
            'password_hash' => Hash::make('123456'), // Contraseña temporal
            'activo' => 1,
            'fecha_registro' => now(),
        ]);
    }
}