@extends('adminlte::page')

@section('title', 'Asignación de Docentes')

@section('content_header')
    <h1><i class="fas fa-chalkboard-teacher"></i> Asignación de Docentes</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title">Nueva Asignación</h3>
                </div>
                <form action="{{ route('asignaciones.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_ano_escolar" value="{{ $anioActivo->id_ano ?? '' }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Docente</label>
                            <select name="id_usuario" class="form-control select2" required>
                                <option value="">Seleccione Docente</option>
                                @foreach($docentes as $d)
                                    <option value="{{ $d->id_usuario }}">{{ $d->nombre }} {{ $d->apellido }} ({{ $d->cedula }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Grado y Sección</label>
                            <select name="id_grado_sec" class="form-control" required>
                                <option value="">Seleccione Grado</option>
                                @foreach($grados as $g)
                                    <option value="{{ $g->id_grado_sec }}">{{ $g->nombre }} - {{ $g->turno }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Materia</label>
                            <select name="id_materia" class="form-control select2" required>
                                <option value="">Seleccione Materia</option>
                                @foreach($materias as $m)
                                    <option value="{{ $m->id_materia }}">{{ $m->nombre }} ({{ $m->codigo }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Fecha de Asignación</label>
                            <input type="date" name="fecha_asignacion" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Horario (Opcional)</label>
                            <textarea name="horario" class="form-control" rows="2" placeholder="Ej: Lunes 07:00 - 09:00"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">Confirmar Asignación</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title">Docentes Asignados - Año Escolar: <b>{{ $anioActivo->nombre ?? 'N/A' }}</b></h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Docente</th>
                                <th>Grado</th>
                                <th>Materia</th>
                                <th>Horario</th>
                                <th width="100px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asignaciones as $asig)
                            <tr>
                                <td>{{ $asig->docente->nombre }} {{ $asig->docente->apellido }}</td>
                                <td>{{ $asig->gradoSeccion->nombre }}</td>
                                <td><span class="badge badge-info">{{ $asig->materia->nombre }}</span></td>
                                <td><small>{{ $asig->horario ?? 'No definido' }}</small></td>
                                <td>
                                    <form action="{{ route('asignaciones.destroy', $asig->id_asignacion) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta asignación?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay docentes asignados para este año escolar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@stop