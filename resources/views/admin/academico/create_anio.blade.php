@extends('adminlte::page')
@section('title', 'Nuevo Año Escolar')
@section('content_header')<h1>Abrir Nuevo Año Escolar</h1>@stop
@section('content')
<div class="card card-outline card-primary shadow">
    <form action="{{ route('academico.anio.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-12"><label>Nombre del Ciclo (Ej: 2024-2025)</label><input type="text" name="nombre" class="form-control" required></div>
                <div class="form-group col-md-6"><label>Fecha Inicio</label><input type="date" name="fecha_inicio" class="form-control" required></div>
                <div class="form-group col-md-6"><label>Fecha Cierre</label><input type="date" name="fecha_fin" class="form-control" required></div>
            </div>
        </div>
        <div class="card-footer text-right"><a href="{{ route('academico.index') }}" class="btn btn-secondary">Cancelar</a><button type="submit" class="btn btn-primary">Crear Año</button></div>
    </form>
</div>
@stop