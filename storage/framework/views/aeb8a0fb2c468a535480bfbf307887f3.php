
<?php $__env->startSection('contenido'); ?>
<!-- Modal -->
 <div class="row mb-3">
  <div class="col-auto">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearMarcaModal">Nueva Marca</button>
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
    <table id="tabla_marca" class="table table-hover table-striped">
        <thead>
            <tr>
                <th class="centered">#</th>
                <th class="centered">Marca</th>
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
<div class="modal fade" id="crearMarcaModal" tabindex="-1" aria-labelledby="crearMarcaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formCrearMarca" >
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" id="marcaId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertarMarcaLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre Marca:</label>
                    <input type="text" class="form-control" name="nombre_marca" id="nombreMarca"  required>
                </div>
                 <div class="mb-3">
                    <label class="form-label">Descripcion:</label>
                    <input type="text" class="form-control" name="descripcion_marca" id="descripcionMarca"  required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary ">Guardar Marca</button>
            </div>
        </div>
    </form>
  </div>
</div>
<!-- MODAL ACTUALIZAR-->
<div class="modal fade" id="editarMarcaModal" tabindex="-1" aria-labelledby="editarMarcaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" id="formEditarMarca">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="editar_id" id="editar_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarProductoLabel">Editar Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control" name="nombre_marca" id="editar_marca" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripcion:</label>
                        <input type="text" class="form-control" name="descripcion_marca" id="editar_descripcion" required>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary btn-ver-detalle">Actualizar Marca</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/funciones/funciones_marca.js']); ?>
<script>
    window.mensajeSuccess = <?php echo json_encode(session('success'), 15, 512) ?>;
    window.mensajeError = <?php echo json_encode(session('error'), 15, 512) ?>;
</script>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Aldo\AgendaComercialV2.5\resources\views/paginas/marcas.blade.php ENDPATH**/ ?>