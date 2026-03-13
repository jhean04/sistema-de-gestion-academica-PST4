@extends('adminlte::page')

@section('title', 'Editar Grado')

@section('content_header')
    <h1>Editar Grado / Sección</h1>
@stop

@section('content')
<div class="card card-outline card-warning">
    <div class="card-body">
        {{-- CAMBIO AQUÍ: id_grado_sec --}}
        <form action="{{ route('academico.grado.update', $grado->id_grado_sec) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Nombre / Sección</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $grado->nombre }}" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Turno</label>
                    <select name="turno" class="form-control">
                        <option value="MAÑANA" {{ $grado->turno == 'MAÑANA' ? 'selected' : '' }}>MAÑANA</option>
                        <option value="TARDE" {{ $grado->turno == 'TARDE' ? 'selected' : '' }}>TARDE</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Capacidad Máxima</label>
                    <input type="number" name="capacidad_max" class="form-control" value="{{ $grado->capacidad_max }}" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Nivel</label>
                    <input type="text" name="nivel" class="form-control" value="{{ $grado->nivel }}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Año Escolar</label>
                <select name="id_ano_escolar" class="form-control">
                    @foreach($anios as $a)
                        <option value="{{ $a->id_ano }}" {{ $grado->id_ano_escolar == $a->id_ano ? 'selected' : '' }}>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Actualizar Cambios</button>
            <a href="{{ route('academico.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@stop