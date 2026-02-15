<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    | Este controlador maneja la autenticación de usuarios para SIGAL y
    | los redirige a la pantalla de inicio.
    */

    use AuthenticatesUsers;

    // A donde va el usuario tras loguearse
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // Coherencia: Solo pueden entrar los que tengan activo = 1
    protected function credentials(Request $request)
    {
        return [
            'email' => $request->email,
            'password' => $request->password,
            'activo' => 1,
        ];
    }

    // Si quieres usar el username de AdminLTE (email)
    public function username()
    {
        return 'email';
    }
}