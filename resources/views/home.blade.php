@extends('adminlte::page')

@section('title', 'Dashboard - SIGAL')

@section('content_header')
    <h1><i class="fas fa-tachometer-alt"></i> Panel de Control</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow">
                <div class="inner">
                    <h3>{{ $totalEstudiantes }}</h3>
                    <p>Estudiantes Registrados</p>
                </div>
                <div class="icon"><i class="fas fa-user-graduate"></i></div>
                <a href="{{ route('usuarios.index') }}" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow">
                <div class="inner">
                    <h3>{{ $totalDocentes }}</h3>
                    <p>Personal Docente</p>
                </div>
                <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <a href="{{ route('usuarios.index') }}" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow">
                <div class="inner">
                    <h3>{{ $totalMaterias }}</h3>
                    <p>Materias</p>
                </div>
                <div class="icon"><i class="fas fa-book"></i></div>
                <a href="{{ route('materias.index') }}" class="small-box-footer">Gestionar <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow">
                <div class="inner">
                    <h3>{{ $totalInscritos }}</h3>
                    <p>Inscripciones Activas</p>
                </div>
                <div class="icon"><i class="fas fa-check-double"></i></div>
                <a href="{{ route('inscripciones.index') }}" class="small-box-footer">Ir a Inscripciones <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary shadow">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Estudiantes por Grado/Sección
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="gradosChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-dark shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Resumen SIGAL</h3>
                </div>
                <div class="card-body">
                    <p>Bienvenido Administrador. Este panel muestra el estado de la matrícula en tiempo real.</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Año Escolar</b> <a class="float-right text-primary">Activo</a>
                        </li>
                        <li class="list-group-item">
                            <b>Base de Datos</b> <a class="float-right text-success">Conectada</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {
        const ctx = document.getElementById('gradosChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($estudiantesPorGrado->pluck('nombre_completo')) !!},
                datasets: [{
                    label: 'Número de Alumnos',
                    data: {!! json_encode($estudiantesPorGrado->pluck('total')) !!},
                    backgroundColor: '#3c8dbc',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@stop