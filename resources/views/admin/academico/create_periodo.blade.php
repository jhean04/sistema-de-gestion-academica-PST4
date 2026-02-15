@extends('adminlte::page')
@section('title', 'Nuevo Periodo')
@section('content_header')<h1>Configurar Lapso de Evaluación</h1>@stop
@section('content')
<div class="card card-outline card-primary shadow">
    <form action="{{ route('academico.periodo.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-6"><label>Nombre del Lapso (Ej: 1er Momento)</label><input type="text" name="nombre" class="form-control" required></div>
                <div class="form-group col-md-6"><label>Año Escolar</label>
                    <select name="id_ano_escolar" class="form-control" required>
                        @foreach($anios as $a)<option value="{{ $a->id_ano }}">{{ $a->nombre }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group col-md-4"><label>Peso (%)</label><input type="number" step="0.01" name="peso_porcentaje" class="form-control" placeholder="33.33" required></div>
                <div class="form-group col-md-4"><label>Inicio</label><input type="date" name="fecha_inicio" class="form-control" required></div>
                <div class="form-group col-md-4"><label>Fin</label><input type="date" name="fecha_fin" class="form-control" required></div>
            </div>
        </div>
        <div class="card-footer text-right"><a href="{{ route('academico.index') }}" class="btn btn-secondary">Cancelar</a><button type="submit" class="btn btn-primary">Registrar Lapso</button></div>
    </form>
</div>
@stop