@extends('adminlte::page')

@section('title', 'Respaldos')

@section('content_header')
<h1><i class="fas fa-database"></i> Respaldo de Base de Datos</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
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
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($respaldos as $r)
                    <tr>
                        <td>{{ $r->nombre_archivo }}</td>
                        <td>{{ number_format($r->tamaño_bytes / 1048576, 2) }} MB</td>
                        <td>{{ \Carbon\Carbon::parse($r->fecha_backup)->format('d/m/Y h:i A') }}</td>
                        <td>{{ $r->usuario->nombre ?? 'Sistema' }} {{ $r->usuario->apellido ?? '' }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                {{-- Descargar --}}
                                <a href="{{ route('respaldos.descargar', $r->id_backup) }}" class="btn btn-info btn-sm" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </a>

                                {{-- Restaurar --}}
                                <form action="{{ route('respaldos.restaurar', $r->id_backup) }}" method="POST" style="display:inline;" onsubmit="return confirm('¡ADVERTENCIA! Se reemplazará toda la base de datos actual con este respaldo. ¿Desea continuar?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="Restaurar Base de Datos">
                                        <i class="fas fa-undo"></i> 
                                    </button>
                                </form>

                                {{-- Eliminar --}}
                                <form action="{{ route('respaldos.eliminar', $r->id_backup) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este archivo de respaldo de forma permanente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar Archivo">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
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