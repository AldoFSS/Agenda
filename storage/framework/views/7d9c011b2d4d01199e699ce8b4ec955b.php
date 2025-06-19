
<?php $__env->startSection('contenido'); ?>
<div class="row mb-3">
  <div class="col-auto">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearClienteModal">Nuevo Cliente</button>
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
    <table id="tabla_cliente" class="table  table-hover table-striped">
        <thead>
            <tr>
                <th class="centered">#</th>
                <th class="centered">Imagen</th>
                <th class="centered">Nombre</th>
                <th class="centered">Nombre_comercial</th>
                <th class="centered">rol</th>
                <th class="centered">telefono</th>
                <th class="centered">correo</th>
                <th class="centered">codigo postal</th>
                <th class="centered">Colonia</th>
                <th class="centered">Calle</th>
                <th class="centered">Estado</th>
                <th class="centered">Municipio</th>
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
<div class="modal fade" id="crearClienteModal" tabindex="-1" aria-labelledby="crearClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" id="formCrearCliente"  enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="id" id="clienteId">
      <div class="modal-content modal-dialog modal-dialog-scrollable">
        <div class="modal-header">
          <h5 class="modal-title" id="insertarUsuarioLabel">Nuevo Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre_cliente" id="nombreCliente" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Nombre Comercial:</label>
                <input type="text" class="form-control" name="nombre_comercial" id="nombreComercialCliente" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="telefonoCliente" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Correo:</label>
                <input type="email" class="form-control" name="correo" id="correoCliente" required>
              </div>
              <div class="col-md-12">
                <label class="form-label">Imagen:</label>
                <input type="file" class="form-control" name="imagen" id="imagenCliente" accept="image/*" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Código Postal:</label>
                <input type="number" class="form-control" name="codigo_postal" id="codigoPostalCliente" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Colonia:</label>
                <input type="text" class="form-control" name="colonia" id="coloniaCliente" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Calle:</label>
                <input type="text" class="form-control" name="calle" id="calleCliente" required>
              </div>
              <div class="col-md-6">
                <label for="Select_estado" class="form-label">Estado</label>
                <select class="form-select" name="id_estado" id="Select_estado" required>
                  <option value="">Seleccione un Estado</option>

                </select>
              </div>
              <div class="col-md-6">
                <label for="Select_municipio" class="form-label">Municipio</label>
                <select class="form-select" name="id_municipio" id="Select_municipio" required>
                  <option value="">Seleccione un Municipio</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Rol:</label>
                <select class="form-select" name="rol" id="rolCliente" required>
                  <option value="Cliente">Cliente</option>
                  <option value="Prospecto">Prospecto</option>
                  <option value="Proveedor">Proveedor</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
          <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary w-100 w-sm-auto">Guardar cliente</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDITAR-->
<div class="modal fade" id="editarClienteModal" tabindex="-1" aria-labelledby="editarClienteLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg">
    <form method="post" id="formEditarCliente" enctype="multipart/form-data">
      <input type="hidden" name="id" id="editar_id">
      <div class="modal-content modal-dialog modal-dialog-scrollable">
        <div class="modal-header">
          <h5 class="modal-title" id="editarProductoLabel">Editar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre_cliente" id="editar_nombre"  required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Nombre Comercial:</label>
                <input type="text" class="form-control" name="nombre_comercial" id="editar_nombre_comercial" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="editar_telefono" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Imagen:</label>
                <input type="file" class="form-control" name="imagen" id="editar_imagen" accept="image/*">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Correo:</label>
                <input type="email" class="form-control" name="correo" id="editar_correo" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Código Postal:</label>
                <input type="number" class="form-control" name="codigo_postal" id="editar_codigo_postal" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Colonia:</label>
                <input type="text" class="form-control" name="colonia" id="editar_colonia" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Calle:</label>
                <input type="text" class="form-control" name="calle" id="editar_calle" required>
              </div>
              <div class="col-12 col-md-6">
                <label for="editar_estado" class="form-label">Estado</label>
                <select class="form-select" name="id_estado" id="editar_estado" required>
                  <option value="">Seleccione un Estado</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label for="editar_municipio" class="form-label">Municipio</label>
                <select class="form-select" name="id_municipio" id="editar_municipio" required>
                  <option value="">Seleccione un Municipio</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Rol:</label>
                <select class="form-select" name="rol" id="editar_rol" required>
                  <option value="Cliente">Cliente</option>
                  <option value="Prospecto">Prospecto</option>
                  <option value="Proveedor">Proveedor</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary btn-ver-detalle">Actualizar cliente</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo app('Illuminate\Foundation\Vite')(['resources/js/funciones/funciones_clientes.js']); ?>
<script>
    window.mensajeSuccess = <?php echo json_encode(session('success'), 15, 512) ?>;
    window.mensajeError = <?php echo json_encode(session('error'), 15, 512) ?>;
</script>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Aldo\AgendaComercialV2.5\resources\views/paginas/clientes.blade.php ENDPATH**/ ?>