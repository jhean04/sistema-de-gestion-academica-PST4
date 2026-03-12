<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\AcademicoController;

use App\Http\Controllers\Admin\MateriaController;

use App\Http\Controllers\Admin\AsignacionController;

use App\Http\Controllers\Admin\InscripcionController;

use App\Http\Controllers\Admin\CalificacionController;

use App\Http\Controllers\Admin\ReporteController;

use App\Http\Controllers\Admin\RespaldoController;

// NUEVA IMPORTACIÓN PARA CONSTANCIAS:

use App\Http\Controllers\Admin\ConstanciaController;



Route::get('/', function () {

    return redirect()->route('login');

});



Auth::routes();



Route::middleware(['auth', 'profile.complete'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



    // Gestión Administrativa de Usuarios

    Route::resource('admin/usuarios', UserController::class)->names('usuarios');

    Route::patch('admin/usuarios/{id}/status', [UserController::class, 'toggleStatus'])->name('usuarios.status');

    Route::post('admin/usuarios/{id}/reset', [UserController::class, 'resetPassword'])->name('usuarios.reset');



    // MÓDULO ACADÉMICO

    Route::get('admin/academico', [AcademicoController::class, 'index'])->name('academico.index');

    Route::get('admin/academico/anio/crear', [AcademicoController::class, 'createAnio'])->name('academico.anio.create');

    Route::post('admin/academico/anio', [AcademicoController::class, 'storeAnio'])->name('academico.anio.store');

    Route::get('admin/academico/grado/crear', [AcademicoController::class, 'createGrado'])->name('academico.grado.create');

    Route::post('admin/academico/grado', [AcademicoController::class, 'storeGrado'])->name('academico.grado.store');

    Route::get('admin/academico/periodo/crear', [AcademicoController::class, 'createPeriodo'])->name('academico.periodo.create');

    Route::post('admin/academico/periodo', [AcademicoController::class, 'storePeriodo'])->name('academico.periodo.store');

    Route::get('admin/academico/grado/{id}/plan', [AcademicoController::class, 'planEstudio'])->name('academico.grado.plan');

    Route::post('admin/academico/plan/store', [AcademicoController::class, 'storePlan'])->name('academico.plan.store');



    // MATERIAS

    Route::get('admin/materias', [MateriaController::class, 'index'])->name('materias.index');

    Route::post('admin/materias', [MateriaController::class, 'store'])->name('materias.store');

    Route::delete('admin/materias/{id}', [MateriaController::class, 'destroy'])->name('materias.destroy');



    // ASIGNACIÓN DOCENTE

    Route::get('admin/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');

    Route::post('admin/asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');

    Route::delete('admin/asignaciones/{id}', [AsignacionController::class, 'destroy'])->name('asignaciones.destroy');



    // INSCRIPCIONES

    Route::resource('admin/inscripciones', InscripcionController::class)->names('inscripciones');

    // NUEVA RUTA PARA GENERAR PDF DE CONSTANCIA:

    Route::get('admin/inscripciones/constancia/{id}', [ConstanciaController::class, 'generar'])->name('constancia.generar');



    // CALIFICACIONES

    Route::get('admin/calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');

    Route::get('admin/calificaciones/gestionar/{id_asignacion}', [CalificacionController::class, 'gestionar'])->name('calificaciones.gestionar');

    Route::post('admin/calificaciones/store', [CalificacionController::class, 'store'])->name('calificaciones.store');



    // REPORTES

    Route::get('admin/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    Route::get('admin/reportes/grado/{id}', [ReporteController::class, 'verGrado'])->name('reportes.ver_grado');



    // MÓDULO DE RESPALDOS

    Route::get('admin/config/respaldos', [RespaldoController::class, 'index'])->name('respaldos.index');

    Route::post('admin/config/respaldos/crear', [RespaldoController::class, 'crear'])->name('respaldos.crear');

    Route::get('admin/config/respaldos/descargar/{id}', [RespaldoController::class, 'descargar'])->name('respaldos.descargar');

   

    // PERFIL

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.show');

    Route::put('/perfil/update', [ProfileController::class, 'update'])->name('perfil.update');

    Route::put('/perfil/password', [ProfileController::class, 'changePassword'])->name('perfil.password');

});



Route::middleware(['auth'])->group(function () {

    Route::get('/completar-perfil', [UserController::class, 'completeProfileView'])->name('perfil.completar');

    Route::post('/completar-perfil', [UserController::class, 'storeProfile'])->name('perfil.guardar');

});



use App\Http\Controllers\DocenteController;



Route::get('/docente/secciones', [DocenteController::class, 'index'])->name('docente.secciones');

Route::get('/docente/secciones/{id_grado_sec}/{id_materia}/estudiantes', [DocenteController::class, 'verEstudiantes'])->name('docente.estudiantes');

Route::get('/docente/evaluar/{id_usuario}/{id_materia}', [DocenteController::class, 'formularioNota'])->name('docente.evaluar');

Route::post('/docente/guardar-nota', [DocenteController::class, 'guardarNota'])->name('docente.guardar_nota');

Route::get('/docente/reporte/{id_grado_sec}/{id_materia}', [DocenteController::class, 'reporteSeccion'])->name('docente.reporte');



use App\Http\Controllers\EstudianteController;

// Rutas para el Perfil Estudiante
Route::get('/estudiante/notas', [EstudianteController::class, 'index'])->name('estudiante.notas');
Route::get('/estudiante/documentos', [EstudianteController::class, 'documentos'])->name('estudiante.documentos');
Route::get('/estudiante/constancia/estudio', [EstudianteController::class, 'descargarConstancia'])->name('estudiante.constancia');