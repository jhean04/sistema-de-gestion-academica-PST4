@extends('adminlte::page')

@section('content')
<div class="card card-dark">
    <div class="card-header">
        <h3 class="card-title">Carga Masiva: {{ $materia_nombre }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('docente.guardar_notas_masivo') }}" method="POST">
            @csrf
            <input type="hidden" name="id_materia" value="{{ $id_materia }}">
            <input type="hidden" name="id_grado_sec" value="{{ $id_grado_sec }}">

            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center">
                    <thead class="bg-navy">
                        <tr>
                            <th>N°</th>
                            <th>Estudiante</th>
                            {{-- Cabeceras Dinámicas --}}
                            @foreach($columnas as $col)
                            <th>{{ $col['label'] }}</th>
                            @endforeach
                            <th class="bg-primary">PROMEDIO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estudiantes as $estudiante)
                        <tr class="fila-estudiante">
                            <td>{{ $estudiante->numero_lista }}</td>
                            <td class="text-left">{{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>

                            {{-- Inputs Dinámicos --}}
                            @foreach($columnas as $col)
                            @php
                            $valor = $notasDB[$estudiante->id_usuario][$col['id']] ?? '';
                            @endphp
                            <td>
                                <input type="number"
                                    name="notas[{{ $estudiante->id_usuario }}][{{ $col['id'] }}]"
                                    class="form-control form-control-sm input-nota text-center"
                                    value="{{ $valor }}"
                                    min="0" max="20" step="0.01">
                            </td>
                            @endforeach
                            <td class="celda-promedio font-weight-bold">0.00</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success float-right mt-3">
                <i class="fas fa-save"></i> Guardar Calificaciones
            </button>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    // Pasamos la variable de PHP a JS de forma segura
    // Esto elimina el error de sintaxis en el editor
    const TOTAL_COLUMNAS = Number("{{ count($columnas) }}");

    function calcular(fila) {
        let suma = 0;

        // Buscamos todos los inputs de nota en esta fila
        fila.find('.input-nota').each(function() {
            let v = $(this).val();
            if (v !== "" && !isNaN(v)) {
                suma += parseFloat(v);
            }
        });

        // Calculamos el promedio basado en el total de evaluaciones configuradas
        let prom = (suma / TOTAL_COLUMNAS).toFixed(2);
        fila.find('.celda-promedio').text(prom);
    }

    $(document).ready(function() {
        // Ejecutar cálculo inicial por si ya hay notas cargadas
        $('.fila-estudiante').each(function() {
            calcular($(this));
        });

        // Escuchar cambios en los inputs
        $('.input-nota').on('input', function() {
            calcular($(this).closest('.fila-estudiante'));
        });
    });
</script>
@stop