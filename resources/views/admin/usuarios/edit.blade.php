@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1><i class="fas fa-user-edit"></i> Editar Usuario</h1>
@stop

@section('content')
    <div class="card card-primary shadow">
        <div class="card-header">
            <h3 class="card-title">Modificar datos de: {{ $user->nombre }} {{ $user->apellido }}</h3>
        </div>
        <form action="{{ route('usuarios.update', $user->id_usuario) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="cedula">Cédula</label>
                        <input type="text" name="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula', $user->cedula) }}" required>
                        @error('cedula') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="tipo_usuario">Rol / Tipo de Usuario</label>
                        <select name="tipo_usuario" class="form-control">
                            <option value="ADMINISTRATIVO" {{ $user->tipo_usuario == 'ADMINISTRATIVO' ? 'selected' : '' }}>Administrativo</option>
                            <option value="DOCENTE" {{ $user->tipo_usuario == 'DOCENTE' ? 'selected' : '' }}>Docente</option>
                            <option value="ESTUDIANTE" {{ $user->tipo_usuario == 'ESTUDIANTE' ? 'selected' : '' }}>Estudiante</option>
                            <option value="REPRESENTANTE" {{ $user->tipo_usuario == 'REPRESENTANTE' ? 'selected' : '' }}>Representante</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $user->nombre) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="{{ old('apellido', $user->apellido) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@stop