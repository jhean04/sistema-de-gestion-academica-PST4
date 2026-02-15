@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <h1><i class="fas fa-user-circle"></i> Mi Perfil</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-primary card-outline shadow">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" 
                         src="{{ Auth::user()->adminlte_image() }}" 
                         alt="Foto de perfil">
                </div>
                <h3 class="profile-username text-center">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</h3>
                <p class="text-muted text-center">{{ Auth::user()->tipo_usuario }}</p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Cédula</b> <a class="float-right">{{ Auth::user()->cedula }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card shadow">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#datos" data-toggle="tab"><i class="fas fa-edit"></i> Datos Personales</a></li>
                    <li class="nav-item"><a class="nav-link" href="#seguridad" data-toggle="tab"><i class="fas fa-lock"></i> Seguridad</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="datos">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="{{ $user->nombre }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Apellido</label>
                                    <input type="text" name="apellido" class="form-control" value="{{ $user->apellido }}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="{{ $user->telefono }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Foto de Perfil</label>
                                    <div class="custom-file">
                                        <input type="file" name="foto_perfil" class="custom-file-input" id="profilePhoto">
                                        <label class="custom-file-label" for="profilePhoto">Cambiar foto...</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Dirección</label>
                                <textarea name="direccion" class="form-control" rows="2" required>{{ $user->direccion }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Información</button>
                        </form>
                    </div>

                    <div class="tab-pane" id="seguridad">
                        <form action="{{ route('perfil.password') }}" method="POST">
                            @csrf @method('PUT')
                            <div class="form-group">
                                <label>Contraseña Actual</label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Cambiar Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $('#profilePhoto').on('change',function(){
        var fileName = $(this).val().split("\\").pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    })
</script>
@stop