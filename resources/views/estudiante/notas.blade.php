@extends('adminlte::page')

@section('title', 'Mis Notas')

@section('content_header')
    <h1>Mi Rendimiento Académico</h1>
@stop

@section('content')
    @foreach($notas as $materia => $detalles)
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $materia }}</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Lapso / Momento</th>
                        <th>Evaluación</th>
                        <th>Fecha</th>
                        <th style="width: 40px">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $n)
                    <tr>
                        <td>{{ $n->lapso }}</td>
                        <td>{{ $n->tipo_evaluacion }}</td>
                        <td>{{ date('d/m/Y', strtotime($n->fecha_registro)) }}</td>
                        <td>
                            <span class="badge {{ $n->valor >= 10 ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format($n->valor, 1) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@stop