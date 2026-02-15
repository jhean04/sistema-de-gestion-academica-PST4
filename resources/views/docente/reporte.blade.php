@extends('adminlte::page')

@section('title', 'Reporte de Notas')

@section('content_header')
    <h1>Reporte de Calificaciones: {{ $info->materia }}</h1>
    <p class="text-muted">{{ $info->grado }}</p>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Consolidado de Notas Registradas</h3>
        <div class="card-tools">
            <button onclick="window.print()" class="btn btn-default btn-sm">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Periodo</th>
                    <th>Tipo</th>
                    <th style="width: 100px">Nota</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notas as $nota)
                    <tr>
                        <td>{{ $nota->apellido }}, {{ $nota->nombre }}</td>
                        <td>{{ $nota->periodo ?? 'N/A' }}</td>
                        <td><span class="badge badge-info">{{ $nota->tipo_evaluacion ?? 'Sin evaluar' }}</span></td>
                        <td>
                            @if($nota->valor)
                                <b class="{{ $nota->valor < 10 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($nota->valor, 1) }}
                                </b>
                            @else
                                <span class="text-muted">---</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay registros de notas para esta sección.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('docente.secciones') }}" class="btn btn-secondary">Volver a mis secciones</a>
    </div>
</div>
@stop