@extends('layouts.app')
@section('contenido')
<div class="row mb-3">
  <div class="col-auto">
   <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearProductoModal">Nuevo Producto</button>
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
    <table id="tabla_productos" class="table table-hover table-striped">
        <thead>
            <tr>
                <th class="centered">#</th>
                <th class="centered">Imagen</th>
                <th class="centered">Producto</th>
                <th class="centered">Categoria</th>
                <th class="centered">SubCategoria</th>
                <th class="centered">Marca</th>
                <th class="centered">Cantidad</th>
                <th class="centered">precio_unitario</th>
                <th class="centered">precio_venta</th>
                <th class="centered">IVA</th>
                <th class="centered">Codigo</th>
                <th class="centered">Proveedor</th>
                <th class="centered">Fecha_registro</th>
                <th class="centered">Opciones</th>
            </tr>
        </thead>
        <tbody>
  
        </tbody>
    </table>
  </div>
</div>
<!-- MODAL CREAR-->
<div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" id="formCrearProducto" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" id="productoId">
      <div class="modal-content modal-dialog modal-dialog-scrollable">
        <div class="modal-header">
          <h5 class="modal-title" id="insertarProductoLabel">Nuevo Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Nombre:</label>
              <input type="text" class="form-control" name="nombre_producto" id="nombreProducto" required>
            </div>

            <div class="col-12 col-md-6">
              <label for="editar_usuario">Proveedor:</label>
              <select class="form-control" name="id_proveedor" id="select_proveedor" required>
                <option value="">Selecciona un proveedor</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label for="editar_usuario">Categoria:</label>
              <select class="form-control" name="id_categoria" id="select_categoria" required>
                <option value="">Selecciona una Categoria</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label for="editar_usuario">Subcategoria:</label>
              <select class="form-control" name="id_subcategoria" id="select_subcategoria" required>
                <option value="">Selecciona una subcategoria</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label for="editar_usuario">Marca:</label>
              <select class="form-control" name="id_marca" id="select_marca"required>
                <option value="">Selecciona una Marca</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Imagen:</label>
              <input type="file" class="form-control" name="imagen_producto" id="imagenProducto" accept="image/*" required>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Stock:</label>
              <input type="number" class="form-control" name="stock" id="stockProducto" required>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Código de barras:</label>
              <input type="text" class="form-control" name="codigo" id="codigoProducto" required>
            </div>

            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center"  
            style="background-color: #f8f9fa; border: 1px solid #ddd; height: 60px; min-height: 80px; position: relative;">
              <svg id="barcode"></svg>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Precio:</label>
              <input type="number" class="form-control" name="precio_unitario" id="precioUnitarioProducto" step="0.01" required>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Precio Venta:</label>
              <input type="number" class="form-control" name="precio_venta" id="precioVentaProducto" step="0.01" required>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">IVA:</label>
              <input type="number" class="form-control" name="IVA_producto" id="IVAProducto" step="0.01" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar producto</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDITAR-->
<div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoLabel" aria-hidden="true"> 
  <div class="modal-dialog modal-dialog-centered modal-lg"> 
    <form method="post" id="formEditarProducto" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" id="editar_id">
      <div class="modal-content modal-dialog modal-dialog-scrollable">
        <div class="modal-header">
          <h5 class="modal-title" id="editarProductoLabel">Editar Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row g-3"> 
              <div class="col-12 col-md-6">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre_producto" id="editar_nombre" required>
              </div>
              <div class="col-12 col-md-6">
                <label>Proveedor:</label>
                <select class="form-select" name="id_proveedor" id="editar_proveedor" required>
                  <option value="">Selecciona un proveedor</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label>Categoria:</label>
                <select class="form-select" name="id_categoria" id="editar_categoria" required>
                  <option value="">Selecciona una Categoria</option>

                </select>
              </div>
              <div class="col-12 col-md-4">
                <label>SubCategoria:</label>
                <select class="form-select" name="id_subcategoria" id="editar_subcategoria" required>
                  <option value="">Selecciona una subcategoria</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label>Marca:</label>
                <select class="form-select" name="id_marca" id="editar_marca" required>
                  <option value="">Selecciona una Marca</option>
  
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Imagen:</label>
                <input type="file" class="form-control" name="imagen_producto" id="editar_imagenProducto" accept="image/*">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Stock:</label>
                <input type="number" class="form-control" name="stock" id="editar_stock" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Codigo de barras:</label>
                <input type="text" class="form-control" name="codigo" id="editar_codigo" required>
              </div>
              <div class="col-12 col-md-6 d-flex justify-content-center align-items-center"
               style="background-color: #f8f9fa; border: 1px solid #ddd; min-height: 80px; position: relative;">
                <svg id="barcode_editar"></svg>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Precio:</label>
                <input type="number" class="form-control" name="precio_unitario" id="editar_precio_unitario" step="0.01" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Precio Venta:</label>
                <input type="number" class="form-control" name="precio_venta" id="editar_precio_venta" step="0.01" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">IVA:</label>
                <input type="number" class="form-control" name="IVA_producto" id="editar_IVA_producto" step="0.01" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar producto</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
@vite(['resources/js/funciones/funciones_productos.js'])
<script>
    window.mensajeSuccess = @json(session('success'));
    window.mensajeError = @json(session('error'));
</script>