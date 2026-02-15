@extends('adminlte::page')

@section('title', 'Crear Grado')

@section('content_header')
    <h1><i class="fas fa-layer-group"></i> Registrar Nuevo Grado/Sección</h1>
@stop

@section('content')
<div class="card card-outline card-primary shadow">
    <form action="{{ route('academico.grado.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Nombre de la Sección (Ej: 1ro "A")</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: 1ro A" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Año Escolar Correspondiente</label>
                    <select name="id_ano_escolar" class="form-control" required>
                        @if($anios->count() > 0)
                            <option value="">Seleccione el ciclo lectivo...</option>
                            @foreach($anios as $a)
                                <option value="{{ $a->id_ano }}">{{ $a->nombre }} (Inicia: {{ \Carbon\Carbon::parse($a->fecha_inicio)->format('Y') }})</option>
                            @endforeach
                        @else
                            <option value="">No hay años escolares activos disponibles</option>
                        @endif
                    </select>
                    @if($anios->count() == 0)
                        <small class="text-danger">Debes crear un Año Escolar primero.</small>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label>Nivel Académico</label>
                    <input type="text" name="nivel" class="form-control" placeholder="Ej: Media General" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Turno</label>
                    <select name="turno" class="form-control" required>
                        <option value="MATUTINO">MATUTINO</option>
                        <option value="VESPERTINO">VESPERTINO</option>
                        <option value="NOCTURNO">NOCTURNO</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Capacidad Máxima de Alumnos</label>
                    <input type="number" name="capacidad_max" class="form-control" value="30" required>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="{{ route('academico.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" {{ $anios->count() == 0 ? 'disabled' : '' }}>
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
@stop