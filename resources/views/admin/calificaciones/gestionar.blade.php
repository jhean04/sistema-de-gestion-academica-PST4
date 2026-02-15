@extends('adminlte::page')

@section('title', 'Cargar Notas')

@section('content_header')
    <h1>Cargar Notas: {{ $asignacion->materia->nombre }} ({{ $asignacion->gradoSeccion->nombre }})</h1>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('calificaciones.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_asignacion" value="{{ $asignacion->id_asignacion }}">
        <input type="hidden" name="id_materia" value="{{ $asignacion->id_materia }}">

        <div class="card card-outline card-success shadow">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4">
                        <label>Periodo (Lapso)</label>
                        <select name="id_periodo" class="form-control" required>
                            @foreach($periodos as $p)
                                <option value="{{ $p->id_periodo }}">{{ $p->nombre }} ({{ $p->peso_porcentaje }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label>Descripción de la Actividad</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Ej: Examen Final, Exposición..." required>
                    </div>
                    <div class="col-md-3">
                        <label>Peso (%) de esta nota</label>
                        <input type="number" name="peso" class="form-control" min="1" max="100" value="25" required>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <table class="table table-striped table-hover m-0">
                    <thead class="bg-dark">
                        <tr>
                            <th>Cédula</th>
                            <th>Estudiante</th>
                            <th width="200px">Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumnos as $ins)
                        <tr>
                            <td>{{ $ins->estudiante->cedula }}</td>
                            <td>{{ $ins->estudiante->nombre }} {{ $ins->estudiante->apellido }}</td>
                            <td>
                                <input type="number" step="0.01" name="notas[{{ $ins->estudiante->id_usuario }}]" 
                                       class="form-control" min="0" max="20" placeholder="0.00">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success float-right">
                    <i class="fas fa-save"></i> Guardar Calificaciones
                </button>
                <a href="{{ route('calificaciones.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </form>
</div>
@stop