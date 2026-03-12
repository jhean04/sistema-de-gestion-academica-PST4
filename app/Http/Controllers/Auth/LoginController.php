<?php

namespace App\Http\Controllers; // Asegúrate de que coincida con tu estructura, usualmente es App\Http\Controllers\Auth;

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

    /**
     * Validar el login incluyendo el Captcha
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|captcha' // Regla del paquete mews/captcha
        ], [
            'captcha.required' => 'El código de seguridad es obligatorio.',
            'captcha.captcha' => 'El código de seguridad es incorrecto, intente de nuevo.'
        ]);
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