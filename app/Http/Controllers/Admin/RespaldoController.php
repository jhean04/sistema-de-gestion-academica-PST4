<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Respaldo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RespaldoController extends Controller
{
    public function index()
    {
        // Sincronizado con tu columna 'fecha_backup'
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

        if (!file_exists(storage_path("app/public/backups"))) {
            mkdir(storage_path("app/public/backups"), 0777, true);
        }

        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // --- INICIO DE LA MODIFICACIÓN ---
        // Definimos la ruta exacta donde XAMPP tiene el ejecutable
        $rutaMysql = 'C:\xampp\mysql\bin\mysqldump.exe'; 
        
        // Creamos el comando usando la ruta absoluta
        $comando = "{$rutaMysql} --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$rutaCompleta}";
        
        // Ejecutamos el comando
        exec($comando, $output, $returnVar);
        // --- FIN DE LA MODIFICACIÓN ---

        if ($returnVar === 0) {
            try {
                // MAGIA: Inserción directa con tus nombres de tabla y columnas
                Respaldo::create([
                    'nombre_archivo' => $nombreArchivo,
                    'ruta'           => $rutaRelativa,
                    'tamaño_bytes'   => filesize($rutaCompleta),
                    'realizado_por'  => Auth::id(), // FK vinculada a tu tabla usuario
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

        return redirect()->back()->with('error', 'Error al ejecutar mysqldump. Verifica que la ruta C:\xampp\mysql\bin\mysqldump.exe sea correcta.');
    }

    public function descargar($id)
    {
        $respaldo = Respaldo::findOrFail($id);
        // Usamos 'ruta' que es el nombre de tu columna en backup_log
        $ruta = storage_path("app/public/" . $respaldo->ruta);
        
        if (file_exists($ruta)) {
            return response()->download($ruta);
        }

        return redirect()->back()->with('error', 'El archivo físico no existe en el servidor.');
    }
}