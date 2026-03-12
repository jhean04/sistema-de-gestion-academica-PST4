@extends('adminlte::page')

@section('title', 'Documentos')

@section('content_header')
    <h1>Mis Documentos</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="info-box shadow">
            <span class="info-box-icon bg-danger"><i class="fas fa-file-pdf"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Constancia de Estudio</span>
                <a href="{{ route('estudiante.constancia') }}" class="btn btn-sm btn-outline-danger">Descargar</a>
            </div>
        </div>
    </div>
    {{-- Aquí puedes agregar más documentos como Boletas o Carnet --}}
</div>
@stop