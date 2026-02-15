<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckProfileComplete
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Si faltan campos obligatorios y NO estamos ya en la página de completar perfil
            if (empty($user->telefono) || empty($user->direccion) || empty($user->fecha_nacimiento)) {
                if (!$request->is('completar-perfil*') && !$request->is('logout')) {
                    return redirect()->route('perfil.completar')
                        ->with('info', 'Debes completar tus datos antes de continuar.');
                }
            }
        }

        return $next($request);
    }
}