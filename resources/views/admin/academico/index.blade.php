@extends('adminlte::page')

@section('title', 'Configuración Académica')

@section('content_header')
<h1><i class="fas fa-graduation-cap text-primary"></i> Configuración Académica</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card card-outline card-primary shadow">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="academicTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="anios-tab" data-toggle="pill" href="#anios" role="tab">
                            <i class="fas fa-calendar-alt"></i> Ciclos Lectivos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="grados-tab" data-toggle="pill" href="#grados" role="tab">
                            <i class="fas fa-layer-group"></i> Grados y Secciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="periodos-tab" data-toggle="pill" href="#periodos" role="tab">
                            <i class="fas fa-clock"></i> Lapsos / Períodos
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="academicTabContent">

                    {{-- TAB 1: AÑOS --}}
                    <div class="tab-pane fade show active" id="anios" role="tabpanel">
                        <div class="text-right mb-3">
                            <a href="{{ route('academico.anio.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nuevo Año
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover border">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Año</th>
                                        <th>Inicio</th>
                                        <th>Cierre</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anios as $anio)
                                    <tr>
                                        <td><strong>{{ $anio->nombre }}</strong></td>
                                        <td>{{ $anio->fecha_inicio }}</td>
                                        <td>{{ $anio->fecha_fin }}</td>
                                        <td>
                                            <span class="badge {{ $anio->activo ? 'badge-success' : 'badge-secondary' }}">
                                                {{ $anio->activo ? 'ACTIVO' : 'INACTIVO' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('academico.anio.edit', $anio->id_ano) }}" class="btn btn-xs btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: GRADOS --}}
                    <div class="tab-pane fade" id="grados" role="tabpanel">
                        <div class="text-right mb-3">
                            <a href="{{ route('academico.grado.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nueva Sección
                            </a>
                        </div>
                        <div class="row">
                            @php $tieneGrados = false; @endphp
                            @foreach($anios->where('activo', 1) as $activo)
                            @foreach($activo->gradosSecciones as $grado)
                            @php $tieneGrados = true; @endphp
                            <div class="col-md-4">
                                <div class="card card-outline card-info shadow-sm">
                                    <div class="card-header">
                                        <h3 class="card-title"><b>{{ $grado->nombre }}</b></h3>
                                        <div class="card-tools">
                                            <a href="{{ route('academico.grado.edit', $grado->id_grado_sec) }}" class="btn btn-tool text-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <span class="badge badge-info">{{ $grado->turno }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1 text-muted small">Nivel: {{ $grado->nivel }}</p>
                                        <p class="mb-3 text-muted small">Capacidad: {{ $grado->capacidad_max }} alumnos</p>
                                        <a href="{{ route('academico.grado.plan', $grado->id_grado_sec) }}" class="btn btn-sm btn-primary btn-block">
                                            <i class="fas fa-book-open"></i> Plan de Estudio
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endforeach
                            @if(!$tieneGrados)
                            <div class="col-12 text-center py-4">
                                <p class="text-muted">No hay secciones registradas en el año escolar activo.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- TAB 3: PERIODOS (Se mantiene igual) --}}
                    <div class="tab-pane fade" id="periodos" role="tabpanel">
                        <div class="text-right mb-3">
                            <a href="{{ route('academico.periodo.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Definir Lapso
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-dark text-center">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Año Escolar</th>
                                        <th>Peso</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach($periodos as $p)
                                    <tr>
                                        <td>{{ $p->nombre }}</td>
                                        <td>{{ $p->anoEscolar->nombre }}</td>
                                        <td><span class="badge badge-primary">{{ $p->peso_porcentaje }}%</span></td>
                                        <td>
                                            <button class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop