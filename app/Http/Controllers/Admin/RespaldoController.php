<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Respaldo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class RespaldoController extends Controller
{
    public function index()
    {
        $respaldos = Respaldo::with('usuario')
            ->orderBy('fecha_backup', 'desc') 
            ->get();
            
        return view('admin.config.respaldos', compact('respaldos'));
    }

    public function crear()
    {
        $nombreArchivo = "sigal_db_" . date('Y-m-d_His') . ".sql";
        $rutaRelativa = "backups/" . $nombreArchivo;
        $rutaCompleta = storage_path("app/public/" . $rutaRelativa);

        if (!File::exists(storage_path("app/public/backups"))) {
            File::makeDirectory(storage_path("app/public/backups"), 0777, true);
        }

        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        $rutaMysql = 'C:\xampp\mysql\bin\mysqldump.exe'; 
        $comando = "{$rutaMysql} --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$rutaCompleta}";
        
        exec($comando, $output, $returnVar);

        if ($returnVar === 0) {
            try {
                Respaldo::create([
                    'nombre_archivo' => $nombreArchivo,
                    'ruta'           => $rutaRelativa,
                    'tamaño_bytes'   => filesize($rutaCompleta),
                    'realizado_por'  => Auth::id(),
                    'tipo'           => 'COMPLETO',
                    'estado'         => 'EXITOSO',
                    'observaciones'  => 'Respaldo manual exitoso'
                ]);
                return redirect()->back()->with('success', 'Respaldo generado y guardado en historial.');
            } catch (\Exception $e) {
                Log::error("Error al registrar en DB: " . $e->getMessage());
                return redirect()->back()->with('error', 'Archivo creado, pero no se pudo registrar en la base de datos.');
            }
        }

        return redirect()->back()->with('error', 'Error al ejecutar mysqldump.');
    }

    public function descargar($id)
    {
        $respaldo = Respaldo::findOrFail($id);
        $ruta = storage_path("app/public/" . $respaldo->ruta);
        
        if (file_exists($ruta)) {
            return response()->download($ruta);
        }
        return redirect()->back()->with('error', 'El archivo físico no existe.');
    }

    public function eliminar($id)
    {
        $respaldo = Respaldo::findOrFail($id);
        $ruta = storage_path("app/public/" . $respaldo->ruta);

        if (file_exists($ruta)) {
            unlink($ruta);
        }

        $respaldo->delete();
        return redirect()->back()->with('success', 'Respaldo eliminado correctamente.');
    }

    public function restaurar($id)
    {
        $respaldo = Respaldo::findOrFail($id);
        $rutaCompleta = storage_path("app/public/" . $respaldo->ruta);

        if (!file_exists($rutaCompleta)) {
            return redirect()->back()->with('error', 'El archivo de respaldo no existe físicamente.');
        }

        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // IMPORTANTE: Ruta al ejecutable mysql.exe para restaurar
        $rutaMysql = 'C:\xampp\mysql\bin\mysql.exe';
        $comando = "{$rutaMysql} --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} < {$rutaCompleta}";

        exec($comando, $output, $returnVar);

        if ($returnVar === 0) {
            return redirect()->back()->with('success', 'Base de datos restaurada con éxito desde el archivo: ' . $respaldo->nombre_archivo);
        }

        return redirect()->back()->with('error', 'Error al restaurar la base de datos.');
    }
}