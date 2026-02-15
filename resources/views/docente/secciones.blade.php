@extends('adminlte::page')

@section('title', 'Mis Secciones')

@section('content_header')
<h1>Mis Secciones Asignadas</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de materias y grados bajo su cargo</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Grado / Sección</th>
                    <th>Turno</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($secciones as $seccion)
                <tr>
                    <td>{{ $seccion->materia }}</td>
                    <td>{{ $seccion->grado }}</td>
                    <td>{{ $seccion->turno }}</td>
                    <td>
                        <a href="{{ route('docente.estudiantes', [$seccion->id_grado_sec, $seccion->id_materia]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-users"></i> Ver Estudiantes
                        </a>
                        <a href="{{ route('docente.reporte', [$seccion->id_grado_sec, $seccion->id_materia]) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-file-alt"></i> Ver Reporte
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No tiene secciones asignadas todavía.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop