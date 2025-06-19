@extends('layouts.app')
@section('contenido')
<div class="row mb-3">
  <div class="col-auto">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearMunicipioModal">Nuevo Municipio</button>
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
    <table id="tabla_municipios" class="table table-hover table-striped">
        <thead>
            <tr>
                <th class="centered">#</th>
                <th class="centered">Municipio</th>
                <th class="centered">Estado</th>
                <th class="centered">fecha registro</th>
                <th class="centered">Opciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade" id="crearMunicipioModal" tabindex="-1" aria-labelledby="crearMunicipioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form form method="POST" id="formCrearMunicipio">
            @csrf
            <input type="hidden" name="id" id="municipioId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertarMunicipioLabel">Nuevo Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Municipio:</label>
                    <input type="text" class="form-control" name="nombre_municipio" id="nombreMunicipio"  required>
                </div>
                <div class="mb-3">
                    <label for="editar_usuario">Estado:</label>
                    <select class="form-control" name="id_estado" id="select_estado" required>
                        <option value="">Selecciona una Estado</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary ">Guardar cliente</button>
            </div>
        </div>
    </form>
  </div>
</div>
<div class="modal fade" id="editarMunicipioModal" tabindex="-1" aria-labelledby="editarMunicipioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form form method="post" id="formEditarMunicipio">
            @csrf
            <input type="hidden" name="id" id="editar_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertarMunicipioLabel">Editar Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Municipio:</label>
                    <input type="text" class="form-control" name="municipio" id="editar_municipio"  required>
                </div>
                <div class="mb-3">
                    <label for="editar_usuario">Estado:</label>
                    <select class="form-control" name="id_estado" id="editar_estado" required>
                        <option value="">Selecciona una Estado</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary ">Guardar cliente</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection
@vite(['resources/js/funciones/funciones_municipio.js'])
<script>
    window.mensajeSuccess = @json(session('success'));
    window.mensajeError = @json(session('error'));
</script>