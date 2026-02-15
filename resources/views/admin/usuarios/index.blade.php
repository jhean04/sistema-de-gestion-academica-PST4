@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('css')
    {{-- Librerías necesarias para que la tabla funcione --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@stop

@section('content_header')
    <h1><i class="fas fa-users-cog"></i> Gestión de Usuarios</h1>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-header bg-dark">
            <h3 class="card-title">Listado de Personal y Estudiantes</h3>
            <div class="card-tools d-flex">
                
                {{-- BUSCADOR --}}
                <div class="input-group input-group-sm mr-3" style="width: 250px;">
                    <input type="text" id="tablaBuscador" class="form-control" placeholder="Buscar por cédula o nombre...">
                    <div class="input-group-append">
                    </div>
                </div>

                <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="usuarios-table" class="table table-hover table-bordered text-center">
                <thead class="thead-light">
                    <tr>
                        <th>Foto</th>
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Rol / Permiso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                        <tr id="fila-{{ $user->id_usuario }}">
                            <td>
                                <img src="{{ $user->adminlte_image() }}" 
                                     class="img-circle elevation-2" 
                                     width="35" height="35" 
                                     style="object-fit: cover;">
                            </td>
                            <td>{{ $user->cedula }}</td>
                            <td>{{ $user->nombre }} {{ $user->apellido }}</td>
                            <td><span class="badge badge-info">{{ $user->tipo_usuario }}</span></td>
                            <td>
                                @if($user->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('usuarios.show', $user->id_usuario) }}" class="btn btn-sm btn-default text-teal" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('usuarios.edit', $user->id_usuario) }}" class="btn btn-sm btn-default text-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('usuarios.reset', $user->id_usuario) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Restablecer contraseña a Sigal123?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-default text-warning" title="Restablecer Contraseña">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('usuarios.status', $user->id_usuario) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-default {{ $user->activo ? 'text-secondary' : 'text-success' }}" title="{{ $user->activo ? 'Desactivar' : 'Activar' }}">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                    {{-- El botón de eliminar con los datos necesarios --}}
                                    <button class="btn btn-sm btn-default text-danger btn-eliminar" 
                                            data-id="{{ $user->id_usuario }}" 
                                            data-nombre="{{ $user->nombre }} {{ $user->apellido }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // 1. Inicializar DataTable
        var table = $('#usuarios-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "dom": 'tpi',
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            }
        });

        // 2. Buscador en tiempo real
        $('#tablaBuscador').on('keyup', function() {
            table.search(this.value).draw();
        });

        // 3. ELIMINAR CON DELEGACIÓN DE EVENTOS (Esto soluciona tu problema)
        // Usamos $(document).on('click', '.btn-eliminar', ...) para que funcione 
        // incluso después de buscar o cambiar de página en la tabla.
        $(document).on('click', '.btn-eliminar', function() {
            let boton = $(this);
            let id = boton.data('id');
            let nombre = boton.data('nombre');

            Swal.fire({
                title: '¿Eliminar a ' + nombre + '?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/usuarios') }}/" + id,
                        type: 'DELETE',
                        data: { 
                            "_token": "{{ csrf_token() }}" 
                        },
                        success: function(response) {
                            Swal.fire('¡Eliminado!', response.message, 'success');
                            
                            // Eliminamos la fila visualmente de la tabla de DataTables
                            table.row(boton.parents('tr')).remove().draw();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'No se pudo eliminar el usuario', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@stop