@extends('adminlte::page')

@section('title', 'Cargar Nota')

@section('content_header')
    <h1>Evaluar Estudiante</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $estudiante->nombre }} {{ $estudiante->apellido }} - <b>{{ $materia->nombre }}</b></h3>
    </div>
    <div class="card-body">
        <form action="{{ route('docente.guardar_nota') }}" method="POST">
            @csrf
            <input type="hidden" name="id_usuario" value="{{ $estudiante->id_usuario }}">
            <input type="hidden" name="id_materia" value="{{ $materia->id_materia }}">

            <div class="row">
                <div class="col-md-4">
                    <label>Periodo / Lapso</label>
                    <select name="id_periodo" class="form-control" required>
                        @foreach($periodos as $p)
                            <option value="{{ $p->id_periodo }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Tipo de Evaluación</label>
                    <select name="tipo_evaluacion" class="form-control">
                        <option value="EXAMEN">Examen</option>
                        <option value="TAREA">Tarea</option>
                        <option value="PROYECTO">Proyecto</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Nota (0-20)</label>
                    <input type="number" name="valor" class="form-control" step="0.1" min="0" max="20" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success mt-4">Guardar Calificación</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary mt-4">Cancelar</a>
        </form>
    </div>
</div>
@stop