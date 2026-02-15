@extends('adminlte::page')

@section('title', 'Respaldos')

@section('content_header')
<h1><i class="fas fa-database"></i> Respaldo de Base de Datos</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card card-outline card-warning shadow">
        <div class="card-header">
            <h3 class="card-title">Historial de Copias de Seguridad</h3>
            <div class="card-tools">
                <form action="{{ route('respaldos.crear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-plus"></i> Crear Nuevo Respaldo Ahora
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($respaldos as $r)
                    <tr>
                        <td>{{ $r->nombre_archivo }}</td>
                        {{-- Conversión de bytes a Megabytes para que se vea bien --}}
                        <td>{{ number_format($r->tamaño_bytes / 1048576, 2) }} MB</td>
                        <td>{{ $r->fecha_backup }}</td>
                        <td>{{ $r->usuario->nombre ?? 'Sistema' }} {{ $r->usuario->apellido ?? '' }}</td>
                        <td>
                            <a href="{{ route('respaldos.descargar', $r->id_backup) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-4">No hay respaldos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop