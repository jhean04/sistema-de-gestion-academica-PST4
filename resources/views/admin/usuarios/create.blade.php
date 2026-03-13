@extends('adminlte::page')

@section('title', 'Nuevo Usuario')

@section('content_header')
    <h1>Crear Nuevo Usuario</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Datos Personales</h3>
        </div>
        <form action="{{ route('usuarios.store') }}" method="POST" id="formUsuario">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Cédula</label>
                        <input type="text" name="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}" required>
                        @error('cedula') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Tipo de Usuario</label>
                        <select name="tipo_usuario" id="tipo_usuario" class="form-control">
                            <option value="ADMINISTRATIVO" {{ old('tipo_usuario') == 'ADMINISTRATIVO' ? 'selected' : '' }}>Administrativo</option>
                            <option value="DOCENTE" {{ old('tipo_usuario') == 'DOCENTE' ? 'selected' : '' }}>Docente</option>
                            <option value="ESTUDIANTE" {{ old('tipo_usuario') == 'ESTUDIANTE' ? 'selected' : '' }}>Estudiante</option>
                            <option value="REPRESENTANTE" {{ old('tipo_usuario') == 'REPRESENTANTE' ? 'selected' : '' }}>Representante</option>
                        </select>
                    </div>
                </div>

                <div id="campos_estudiante" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Representante</label>
                            <select name="id_representante" class="form-control select2 shadow-sm" style="width: 100%;">
                                <option value="">Seleccione el representante...</option>
                                @foreach($representantes as $rep)
                                    <option value="{{ $rep->id_usuario }}" {{ old('id_representante') == $rep->id_usuario ? 'selected' : '' }}>
                                        {{ $rep->cedula }} - {{ $rep->nombre }} {{ $rep->apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Grado y Sección</label>
                            <select name="id_grado_sec" class="form-control">
                                <option value="">Seleccione grado...</option>
                                @foreach($grados as $grado)
                                    <option value="{{ $grado->id_grado_sec }}" {{ old('id_grado_sec') == $grado->id_grado_sec ? 'selected' : '' }}>
                                        {{ $grado->nombre }} - {{ $grado->turno }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Contraseña Provisional</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        function toggleEstudianteFields() {
            if ($('#tipo_usuario').val() === 'ESTUDIANTE') {
                $('#campos_estudiante').slideDown();
                $('select[name="id_representante"], select[name="id_grado_sec"]').prop('required', true);
            } else {
                $('#campos_estudiante').slideUp();
                $('select[name="id_representante"], select[name="id_grado_sec"]').prop('required', false);
            }
        }

        // Ejecutar al cargar (por si hay errores de validación y regresa con datos)
        toggleEstudianteFields();

        // Ejecutar al cambiar el select
        $('#tipo_usuario').change(function() {
            toggleEstudianteFields();
        });
    });
</script>
@stop