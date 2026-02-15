@extends('adminlte::page')

@section('title', 'Detalle de Grado')

@section('content_header')
    <h1>Estudiantes de {{ $grado->nombre }}</h1>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="bg-navy">
                <tr>
                    <th>Cédula</th>
                    <th>Nombre Completo</th>
                    <th>Promedio General</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estudiantes as $est)
                <tr>
                    <td>{{ $est->estudiante->cedula }}</td>
                    <td>{{ $est->estudiante->nombre }} {{ $est->estudiante->apellido }}</td>
                    <td>
                        <b class="{{ $est->promedio_general < 10 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($est->promedio_general, 2) }}
                        </b>
                    </td>
                    <td>
                        @if($est->promedio_general >= 10)
                            <span class="badge badge-success">Aprobando</span>
                        @else
                            <span class="badge badge-danger">En Riesgo</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@stop
