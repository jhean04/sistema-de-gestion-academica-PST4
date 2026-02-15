@extends('adminlte::page')

@section('title', 'Gestión de Materias')

@section('content_header')
    <h1><i class="fas fa-book"></i> Gestión de Materias</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary shadow">
            <div class="card-header">
                <h3 class="card-title">Nueva Materia</h3>
            </div>
            <form action="{{ route('materias.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Nombre de la Materia</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Física" required>
                    </div>
                    <div class="form-group">
                        <label>Código</label>
                        <input type="text" name="codigo" class="form-control" placeholder="Ej: FIS-01" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción (Opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Guardar Materia</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-outline card-primary shadow">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materias as $m)
                        <tr>
                            <td><span class="badge badge-info">{{ $m->codigo }}</span></td>
                            <td>{{ $m->nombre }}</td>
                            <td>
                                <form action="{{ route('materias.destroy', $m->id_materia) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs" onclick="return confirm('¿Eliminar esta materia?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop