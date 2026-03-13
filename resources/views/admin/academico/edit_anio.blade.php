@extends('adminlte::page')

@section('title', 'Editar Año Escolar')

@section('content_header')
    <h1>Editar Ciclo Lectivo</h1>
@stop

@section('content')
<div class="card card-outline card-warning">
    <div class="card-body">
        {{-- CAMBIO AQUÍ: id_ano --}}
        <form action="{{ route('academico.anio.update', $anio->id_ano) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nombre del Año</label>
                <input type="text" name="nombre" class="form-control" value="{{ $anio->nombre }}" required>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $anio->fecha_inicio }}" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Fecha de Cierre</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $anio->fecha_fin }}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="activo" class="form-control">
                    <option value="1" {{ $anio->activo == 1 ? 'selected' : '' }}>ACTIVO</option>
                    <option value="0" {{ $anio->activo == 0 ? 'selected' : '' }}>INACTIVO</option>
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Guardar Cambios</button>
            <a href="{{ route('academico.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@stop