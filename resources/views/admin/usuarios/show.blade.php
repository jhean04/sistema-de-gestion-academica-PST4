@extends('adminlte::page')

@section('title', 'Detalles del Usuario')

@section('content_header')
    <h1><i class="fas fa-user-check"></i> Ficha del Usuario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline shadow">
                <div class="card-body box-profile">
                    <div class="text-center">
                        {{-- CAMBIO AQUÍ: Usamos la lógica del modelo para la foto real --}}
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ $user->adminlte_image() }}"
                             alt="Foto de perfil de {{ $user->nombre }}">
                    </div>
                    <h3 class="profile-username text-center">{{ $user->nombre }} {{ $user->apellido }}</h3>
                    <p class="text-muted text-center">{{ $user->tipo_usuario }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Estado</b> 
                            <span class="float-right badge {{ $user->activo ? 'badge-success' : 'badge-danger' }}">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Cédula</b> <a class="float-right">{{ $user->cedula }}</a>
                        </li>
                        {{-- Agregamos teléfono para que la ficha esté completa --}}
                        <li class="list-group-item">
                            <b>Teléfono</b> <a class="float-right">{{ $user->telefono ?? 'No registrado' }}</a>
                        </li>
                    </ul>
                    <a href="{{ route('usuarios.edit', $user->id_usuario) }}" class="btn btn-primary btn-block"><b>Editar Datos</b></a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header p-2">
                    <h3 class="card-title ml-2">Información Detallada</h3>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane">
                            <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                            <p class="text-muted">{{ $user->email }}</p>
                            <hr>
                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección</strong>
                            <p class="text-muted">{{ $user->direccion ?? 'No registrada' }}</p>
                            <hr>
                            <strong><i class="fas fa-birthday-cake mr-1"></i> Fecha de Nacimiento</strong>
                            <p class="text-muted">{{ $user->fecha_nacimiento ?? 'No registrada' }}</p>
                            <hr>
                            <strong><i class="fas fa-calendar-alt mr-1"></i> Fecha de Registro</strong>
                            <p class="text-muted">{{ $user->fecha_registro }}</p>
                            <hr>
                            <strong><i class="fas fa-shield-alt mr-1"></i> Permisos del Sistema</strong>
                            <p class="text-muted">
                                Nivel: <b>{{ $user->tipo_usuario }}</b>. 
                                @if($user->tipo_usuario == 'ADMINISTRATIVO')
                                    Gestiona usuarios y configuraciones globales.
                                @elseif($user->tipo_usuario == 'DOCENTE')
                                    Gestiona secciones y notas asignadas.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-default">Volver al Listado</a>
                </div>
            </div>
        </div>
    </div>
@stop