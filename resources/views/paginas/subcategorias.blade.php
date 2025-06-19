@extends('layouts.app')
@section('contenido')
 <div class="row mb-3">
  <div class="col-auto">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearSubcategoriaModal">Nuevo SubCategoria</button>
  </div>
  <div class="col-auto">
    <select class="form-control" name="cambiar_estatus" id="select_estatus">
      <option value="All">Todos</option>
      <option value="1">Activos</option>
      <option value="0">Inactivos</option>
    </select>
  </div>
</div>
<div>
    <div class="table-responsive-lg">
        <table id="tabla_subcategoria" class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="centered">#</th>
                    <th class="centered">Imagen</th>
                    <th class="centered">Subcategoria</th>
                    <th class="centered">Categoria</th>
                    <th class="centered">Descripcion</th>
                    <th class="centered">Fecha Registro</th>
                    <th class="centered">Opciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<!-- MODAL CREAR -->
<div class="modal fade" id="crearSubcategoriaModal" tabindex="-1" aria-labelledby="crearSubcategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formCrearSubcategoria" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_subcategoria" id="subCategoriaId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearsubCategoriaLabel">Nuevo Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="nombre_subcategoria" id="nombresubCategoria"  required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripcion:</label>
                    <input type="text" class="form-control" name="descripcion_subcategoria" id="descripcionsubCategoria"  required>
                </div>
                <div class="mb-3">
                    <label for="editar_subCategoria">Categoria</label>
                    <select class="form-control" name="id_categoria" id="select_categoria" required>
                        <option value="">Seleccione una Categoria</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen:</label>
                    <input type="file" class="form-control" name="imagen_subcategoria" id="imagensubCategoria" accept="image/*"  required >
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary ">Guardar usuario</button>
            </div>
        </div>
    </form>
  </div>
</div>
<!-- MODAL EDITAR-->
<div class="modal fade" id="editarSubcategoriaModal" tabindex="-1" aria-labelledby="editarSubcategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formEditarSubcategoria" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="editar_id" id="editar_id" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarSubcategoriaLabel">Editar subCategoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="nombre_subcategoria" id="editar_nombre"  required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripcion:</label>
                    <input type="text" class="form-control" name="descripcion_subcategoria" id="editar_descripcion" required>
                </div>
                <div class="mb-3">
                    <label for="editar_categoria">Categoria</label>
                    <select class="form-control" name="id_categoria" id="editar_categoria" required>
                        <option value="">Seleccione una Categoria</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen:</label>
                    <input type="file" class="form-control" name="imagen_subcategoria" id="editar_imagenCategoria" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary btn-ver-detalle">Actualizar Categoria</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection
@vite(['resources/js/funciones/funciones_subcategorias.js'])
<script>
    window.mensajeSuccess = @json(session('success'));
    window.mensajeError = @json(session('error'));
</script>