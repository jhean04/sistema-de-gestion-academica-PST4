<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // ... (métodos index, create, store, show, edit, update, etc. se mantienen igual) ...
    // Asegúrate de mantener tus métodos anteriores intactos.

    public function index()
    {
        $usuarios = User::all();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|unique:usuario,cedula',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:usuario,email',
            'password' => 'required|min:6',
            'tipo_usuario' => 'required'
        ]);

        User::create([
            'cedula' => $request->cedula,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'tipo_usuario' => $request->tipo_usuario,
            'activo' => 1,
            'fecha_registro' => now()
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.usuarios.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.usuarios.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'cedula' => 'required|unique:usuario,cedula,'.$id.',id_usuario',
            'email' => 'required|email|unique:usuario,email,'.$id.',id_usuario',
        ]);

        $user->cedula = $request->cedula;
        $user->nombre = $request->nombre;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->tipo_usuario = $request->tipo_usuario;
        $user->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->activo = !$user->activo;
        $user->save();
        return back()->with('success', 'Estado actualizado.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password_hash = Hash::make('Sigal123');
        $user->save();
        return back()->with('success', 'Contraseña restablecida a: Sigal123');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['success' => true, 'message' => 'Usuario eliminado.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }

    // --- MÉTODOS DE PERFIL DE USUARIO (CORREGIDOS) ---

    public function completeProfileView()
    {
        return view('admin.usuarios.complete_profile', ['user' => Auth::user()]);
    }

    public function storeProfile(Request $request)
    {
        // 1. Obtener el usuario actual buscando por su ID para asegurar que Eloquent lo rastree
        $user = User::findOrFail(Auth::id());
        
        // 2. Validación (Asegúrate de que los nombres coincidan con tu HTML)
        $request->validate([
            'telefono' => 'required|min:10',
            'direccion' => 'required|min:5',
            'fecha_nacimiento' => 'required|date',
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // 3. Manejo de la foto
        if ($request->hasFile('foto_perfil')) {
            if (!Storage::disk('public')->exists('profiles')) {
                Storage::disk('public')->makeDirectory('profiles');
            }
            $path = $request->file('foto_perfil')->store('profiles', 'public');
            $user->foto_perfil = $path;
        }

        // 4. Inyectar datos manualmente para asegurar que save() los detecte
        $user->telefono = $request->telefono;
        $user->direccion = $request->direccion;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        
        // 5. Guardar y verificar
        if ($user->save()) {
            return redirect()->route('home')->with('success', '¡Perfil activado correctamente!');
        }

        return back()->withErrors('Error al guardar los datos. Intente nuevamente.');
    }
}