@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    <h1><i class="fas fa-chart-line"></i> Reportes Académicos</h1>
@stop

@section('content')
<div class="card card-outline card-navy">
    <div class="card-header">
        <h3 class="card-title">Resumen por Grados - Año: {{ $anioActivo->nombre ?? 'N/A' }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($grados as $g)
            <div class="col-md-3">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-navy"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $g->nombre }}</span>
                        <span class="info-box-number">{{ $g->turno }}</span>
                        <a href="{{ route('reportes.ver_grado', $g->id_grado_sec) }}" class="btn btn-xs btn-outline-primary">Ver Detalles</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@stop