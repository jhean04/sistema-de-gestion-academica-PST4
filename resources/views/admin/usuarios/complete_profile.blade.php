@extends('adminlte::master')

@section('body')
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 100vh; background: #f4f6f9;">
    <div class="card card-outline card-primary shadow" style="width: 100%; max-width: 600px;">
        <div class="card-header text-center">
            <h3><i class="fas fa-id-card text-primary"></i> Completar Información</h3>
            <p>Hola, <b>{{ $user->nombre }}</b>. Para activar tu cuenta, completa tus datos.</p>
        </div>
        <form action="{{ route('perfil.guardar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                <div class="form-group">
                    <label>Teléfono de Contacto</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Ej: 04121234567" required>
                </div>
                <div class="form-group">
                    <label>Dirección de Habitación</label>
                    <textarea name="direccion" class="form-control" rows="2" required placeholder="Calle, Sector, Casa..."></textarea>
                </div>
                <div class="form-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Foto de Perfil <small class="text-muted">(Opcional, máx 2MB)</small></label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="foto_perfil" class="custom-file-input" id="customFile" accept="image/*">
                            <label class="custom-file-label" for="customFile">Elegir imagen...</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success btn-lg">Activar mi cuenta <i class="fas fa-check-circle"></i></button>
            </div>
        </form>
    </div>
</div>
@stop

@section('adminlte_js')
<script>
    // Para que el nombre del archivo aparezca en el input al seleccionar
    document.getElementById('customFile').onchange = function() {
        var fileName = this.value.split("\\").pop();
        this.nextElementSibling.classList.add("selected");
        this.nextElementSibling.innerHTML = fileName;
    };
</script>
@stop