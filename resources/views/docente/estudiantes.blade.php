@extends('adminlte::page')

@section('title', 'Lista de Estudiantes')

@section('content_header')
    {{-- Corregimos la validación para evitar el error de stdClass --}}
    <h1>Estudiantes: {{ $materia_nombre }}</h1>
    <p class="text-muted">Listado oficial de la sección</p>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Seleccione un estudiante para evaluar</h3>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>N° Lista</th>
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estudiantes as $estudiante)
                        <tr>
                            <td>{{ $estudiante->numero_lista }}</td>
                            <td>{{ $estudiante->cedula }}</td>
                            <td>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
                            <td>
                                {{-- Usamos las variables que vienen del controlador --}}
                                <a href="{{ route('docente.evaluar', [$estudiante->id_usuario, $id_materia]) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Evaluar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="alert alert-warning m-2">
                                    <i class="fas fa-exclamation-triangle"></i> No hay estudiantes inscritos en esta sección todavía.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                <a href="{{ route('docente.secciones') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Mis Secciones
                </a>
            </div>
        </div>
    </div>
@stop