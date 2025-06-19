@extends('layouts.app')
@section('contenido')
<!-- Modal -->
 <div class="row mb-3">
  <div class="col-auto">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearZonaModal">Nueva Zona</button>
  </div>
  <div class="col-auto">
    <select class="form-control" name="cambiar_estatus" id="select_estatus">
      <option value="All">Todos</option>
      <option value="1">Activos</option>
      <option value="0">Inactivos</option>
    </select>
  </div>
</div>
<div class="table-responsive-lg">
    <table id="tabla_zonas" class="table table-hover table-striped">
        <thead>
            <tr>
                <th class="centered">#</th>
                <th class="centered">Nombre</th>
                <th class="centered">Descripcion</th>
                <th class="centered">fecha registro</th>
                <th class="centered">Opciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<!-- MODAL CREAR-->
<div class="modal fade" id="crearZonaModal" tabindex="-1" aria-labelledby="crearZonaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form form method="POST" id="formCrearZona">
            @csrf
            <input type="hidden" name="id" id="marcaId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertarZonaLabel">Nueva Zona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre Zona:</label>
                    <input type="text" class="form-control" name="nombre_zona" id="nombreZona"  required>
                </div>
                 <div class="mb-3">
                    <label class="form-label">Descripcion:</label>
                    <input type="text" class="form-control" name="descripcion_zona" id="descripcionZona"  required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary ">Guardar Zona</button>
            </div>
        </div>
    </form>
  </div>
</div>
<!-- Modal editar Zona -->
<div class="modal fade" id="editarZonaModal" tabindex="-1" aria-labelledby="editarZonaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" id="formEditarZona">
            @csrf
            <input type="hidden" name="editar_id" id="editar_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarProductoLabel">Editar Zona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control" name="nombre_zona" id="editar_zona" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripcion:</label>
                        <input type="text" class="form-control" name="descripcion_zona" id="editar_descripcion" required>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary btn-ver-detalle">Actualizar Zona</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@vite(['resources/js/funciones/funciones_zona.js'])
<script>
    window.mensajeSuccess = @json(session('success'));
    window.mensajeError = @json(session('error'));
</script>