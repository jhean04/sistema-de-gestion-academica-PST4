@extends('adminlte::page')

@section('title', 'Mis Materias')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Gestión de Calificaciones</h1>
@stop

@section('content')
<div class="card card-outline card-primary shadow">
    <div class="card-header">
        <h3 class="card-title">Seleccione una Materia para Evaluar</h3>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($misAsignaciones as $asig)
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h5>{{ $asig->materia->nombre }}</h5>
                            <p>{{ $asig->gradoSeccion->nombre }} - {{ $asig->gradoSeccion->turno }}</p>
                            @if(Auth::user()->tipo_usuario == 'ADMIN')
                                <small>Docente: {{ $asig->docente->nombre }}</small>
                            @endif
                        </div>
                        <div class="icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <a href="{{ route('calificaciones.gestionar', $asig->id_asignacion) }}" class="small-box-footer">
                            Cargar Notas <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning">No tienes materias asignadas para este año escolar activo.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@stop
