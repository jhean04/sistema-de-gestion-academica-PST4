@extends('adminlte::page')

@section('title', 'Inscripciones')

@section('content_header')
    <h1><i class="fas fa-user-plus"></i> Proceso de Inscripción</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-success shadow">
                <div class="card-header">
                    <h3 class="card-title">Inscribir Nuevo Estudiante</h3>
                </div>
                <form action="{{ route('inscripciones.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_ano_escolar" value="{{ $anioActivo->id_ano ?? '' }}">
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>Estudiante</label>
                            <select name="id_usuario" class="form-control select2" required>
                                <option value="">Seleccione Estudiante</option>
                                @foreach($estudiantesDisponibles as $e)
                                    <option value="{{ $e->id_usuario }}">{{ $e->nombre }} {{ $e->apellido }} ({{ $e->cedula }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Solo aparecen alumnos no inscritos en el año actual.</small>
                        </div>

                        <div class="form-group">
                            <label>Grado y Sección Destino</label>
                            <select name="id_grado_sec" class="form-control" required>
                                <option value="">Seleccione Grado</option>
                                @foreach($grados as $g)
                                    <option value="{{ $g->id_grado_sec }}">{{ $g->nombre }} - {{ $g->turno }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Fecha de Inscripción</label>
                            <input type="date" name="fecha_inscripcion" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">Finalizar Inscripción</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-outline card-success shadow">
                <div class="card-header">
                    <h3 class="card-title">Estudiantes Inscritos - <b>{{ $anioActivo->nombre ?? 'N/A' }}</b></h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Estudiante</th>
                                <th>Grado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inscripciones as $ins)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ins->estudiante->nombre }} {{ $ins->estudiante->apellido }}</td>
                                <td>{{ $ins->gradoSeccion->nombre }}</td>
                                <td><span class="badge badge-success">{{ $ins->estado }}</span></td>
                                <td class="d-flex">
                                    {{-- NUEVO BOTÓN: GENERAR CONSTANCIA PDF --}}
                                    <a href="{{ route('constancia.generar', $ins->id_inscripcion) }}" 
                                       class="btn btn-danger btn-sm mr-2" target="_blank" title="Descargar Constancia">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    <form action="{{ route('inscripciones.destroy', $ins->id_inscripcion) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-dark btn-sm" onclick="return confirm('¿Anular esta inscripción?')">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay alumnos inscritos todavía.</td>
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

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@stop