
<?php $__env->startSection('contenido'); ?>
<div class="row mb-3">
  <div class="col-auto">
     <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">Nuevo Usuario</button>
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
        <table id="tabla_usuarios" class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="centered">#</th>
                    <th class="centered">Imagen</th>
                    <th class="centered">nombre</th>
                    <th class="centered">telefono</th>
                    <th class="centered">correo</th>
                    <th class="centered">cargo</th>
                    <th class="centered">Fecha_registro</th>
                    <th class="centered">Opciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<!-- MODAL CREAR -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" id="formCrearUsuario" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="id" id="usuarioId">
      <div class="modal-content modal-dialog modal-dialog-scrollable" >
        <div class="modal-header">
          <h5 class="modal-title" id="crearUsuarioLabel">Nuevo Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre_usuario" id="nombreUsuario" required>
              </div>
              <div class="col-12">
                <label class="form-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="telefonoUsuario" required>
              </div>
              <div class="col-12">
                <label class="form-label">Imagen:</label>
                <input type="file" class="form-control" name="imagen" id="imagenUsuario" accept="image/*">
              </div>
              <div class="col-12">
                <label class="form-label">Correo:</label>
                <input type="email" class="form-control" name="correo" id="correoUsuario" required>
              </div>
              <div class="col-12">
                <label class="form-label">Contraseña:</label>
                <input type="password" class="form-control" name="contraseña" id="contraseña" required>
              </div>
              <div class="col-12">
                <label class="form-label">Rol:</label>
                <select class="form-select" name="rol" id="rolUsuario" required>
                  <option value="Administrador">Administrador</option>
                  <option value="Asesor_Comercial">Asesor Comercial</option>
                  <option value="Gerente">Gerente</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar usuario</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" id="formEditarUsuario" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="editar_id" id="editar_id">
      <div class="modal-content modal-dialog modal-dialog-scrollable">
        <div class="modal-header">
          <h5 class="modal-title" id="editarUsuarioLabel">Editar Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre_usuario" id="editar_nombre" required>
              </div>
              <div class="col-12">
                <label class="form-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="editar_telefono" required>
              </div>
              <div class="col-12">
                <label class="form-label">Imagen:</label>
                <input type="file" class="form-control" name="imagen" id="editar_imagen" accept="image/*">
              </div>
              <div class="col-12">
                <label class="form-label">Correo:</label>
                <input type="email" class="form-control" name="correo" id="editar_correo" required>
              </div>
              <div class="col-12">
                <label class="form-label">Rol:</label>
                <select class="form-select" name="rol" id="editar_rol" required>
                  <option value="Administrador">Administrador</option>
                  <option value="Asesor Comercial">Asesor Comercial</option>
                  <option value="Gerente">Gerente</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary btn-ver-detalle">Actualizar usuario</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/funciones/funciones_usuario.js']); ?>
<script>
    window.mensajeSuccess = <?php echo json_encode(session('success'), 15, 512) ?>;
    window.mensajeError = <?php echo json_encode(session('error'), 15, 512) ?>;
</script>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Aldo\AgendaComercialV2.5\resources\views/paginas/usuarios.blade.php ENDPATH**/ ?>