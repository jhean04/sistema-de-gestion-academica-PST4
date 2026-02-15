@extends('adminlte::page')

@section('title', 'Plan de Estudio')

@section('content_header')
    <h1><i class="fas fa-book text-primary"></i> Plan de Estudio: {{ $grado->nombre }}</h1>
@stop

@section('content')
<div class="row">
    {{-- Formulario de Asignación --}}
    <div class="col-md-4">
        <div class="card card-outline card-primary shadow">
            <div class="card-header">
                <h3 class="card-title">Asignar Materia</h3>
            </div>
            <form action="{{ route('academico.plan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_grado_sec" value="{{ $grado->id_grado_sec }}">
                <div class="card-body">
                    <div class="form-group">
                        <label>Materia</label>
                        <select name="id_materia" class="form-control select2" required>
                            <option value="">Seleccione...</option>
                            @foreach($materiasDisponibles as $m)
                                <option value="{{ $m->id_materia }}">{{ $m->nombre }} ({{ $m->codigo }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Horas Semanales</label>
                        <input type="number" name="horas_semanales" class="form-control" value="4" min="1" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Añadir al Plan</button>
                    <a href="{{ route('academico.index') }}" class="btn btn-default btn-block">Volver</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Materias Asignadas --}}
    <div class="col-md-8">
        <div class="card card-outline card-primary shadow">
            <div class="card-header">
                <h3 class="card-title">Materias en este Grado</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Código</th>
                            <th>Horas</th>
                            <th style="width: 40px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plan as $item)
                        <tr>
                            <td>{{ $item->materia->nombre }}</td>
                            <td><span class="badge badge-secondary">{{ $item->materia->codigo }}</span></td>
                            <td>{{ $item->horas_semanales }} h</td>
                            <td>
                                <button class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay materias asignadas a este plan aún.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop