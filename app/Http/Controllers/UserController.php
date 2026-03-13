<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        // Cargamos datos necesarios para el formulario dinámico
        $representantes = User::where('tipo_usuario', 'REPRESENTANTE')->get();
        $grados = DB::table('grado_seccion')->get(); // Ajusta el nombre de la tabla si es distinto
        
        return view('admin.usuarios.create', compact('representantes', 'grados'));
    }

    public function store(Request $request)
    {
        $rules = [
            'cedula' => 'required|unique:usuario,cedula',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:usuario,email',
            'password' => 'required|min:6',
            'tipo_usuario' => 'required'
        ];

        // Validaciones extra si es estudiante
        if ($request->tipo_usuario === 'ESTUDIANTE') {
            $rules['id_representante'] = 'required';
            $rules['id_grado_sec'] = 'required';
        }

        $request->validate($rules);

        // Iniciamos una transacción para asegurar que se creen ambos registros o ninguno
        DB::beginTransaction();

        try {
            $user = User::create([
                'cedula' => $request->cedula,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'tipo_usuario' => $request->tipo_usuario,
                'activo' => 1,
                'fecha_registro' => now()
            ]);

            // Si es estudiante, registramos su inscripción
            if ($request->tipo_usuario === 'ESTUDIANTE') {
                DB::table('inscripcion')->insert([
                    'id_usuario' => $user->id_usuario,
                    'id_grado_sec' => $request->id_grado_sec,
                    'id_representante' => $request->id_representante,
                    'fecha_inscripcion' => now(),
                    'estado' => 'ACTIVO'
                ]);
            }

            DB::commit();
            return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al procesar el registro: ' . $e->getMessage())->withInput();
        }
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

    public function completeProfileView()
    {
        return view('admin.usuarios.complete_profile', ['user' => Auth::user()]);
    }

    public function storeProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        
        $request->validate([
            'telefono' => 'required|min:10',
            'direccion' => 'required|min:5',
            'fecha_nacimiento' => 'required|date',
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('foto_perfil')) {
            if (!Storage::disk('public')->exists('profiles')) {
                Storage::disk('public')->makeDirectory('profiles');
            }
            $path = $request->file('foto_perfil')->store('profiles', 'public');
            $user->foto_perfil = $path;
        }

        $user->telefono = $request->telefono;
        $user->direccion = $request->direccion;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        
        if ($user->save()) {
            return redirect()->route('home')->with('success', '¡Perfil activado correctamente!');
        }

        return back()->withErrors('Error al guardar los datos. Intente nuevamente.');
    }
}