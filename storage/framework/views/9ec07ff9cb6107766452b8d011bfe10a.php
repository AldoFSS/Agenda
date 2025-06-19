
<?php $__env->startSection('contenido'); ?>
<div class="row mb-3">
  <div class="col-auto">
    <button type="button" class="btn btn-primary btn-crear" data-bs-toggle="modal" data-bs-target="#crearVentaModal">
      Nueva Venta
    </button>
  </div>
  <div class="col-auto">
    <select class="form-control" name="cambiar_estatus" id="select_estatus">
      <option value="all">Todos</option>
      <option value="1">Activos</option>
      <option value="0">Inactivos</option>
    </select>
  </div>
</div>

<div class="table-responsive-lg">
    <table id="tabla_ventas" class="table table-hover table-striped">
        <thead>
            <tr>
                <th class="centered">#</th>
                <th class="centered">Cliente</th>
                <th class="centered">Usuario</th>
                <th class="centered">fecha venta</th>
                <th class="centered">fecha registro</th>
                <th class="centered">total</th>
                <th class="centered">Opciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Modal Crear Venta-->
<div class="modal fade" id="crearVentaModal" tabindex="-1" aria-labelledby="crearVentaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <form method="POST" id="formCrearVenta">
      <?php echo csrf_field(); ?>
      <div class="modal-content modal-dialog modal-dialog-scrollable" >
        <div class="modal-header">
          <h5 class="modal-title" id="crearCitaModalLabel">Nueva Venta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="cliente">Cliente:</label>
            <select class="form-control" name="id_cliente" id="select_cliente" required>
              <option value="">Selecciona un cliente</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="usuario">Usuario:</label>
            <select class="form-control" name="id_usuario" id="select_usuario" required>
              <option value="">Selecciona un usuario</option>
            </select>
          </div>
          <div class="table-responsive-lg">
             <table id="productosTable" class="table table-hover table-striped">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Precio</th>
                  <th>Subtotal</th>
                  <th>IVA</th>
                  <th>Total</th>
                  <th>Quitar</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        <button type="button" class="btn btn-primary" data-agregar-fila >Agregar producto</button>
          <div class="mb-3">
            <label for="fecha_venta">Fecha Venta:</label>
            <input type="date" class="form-control" name="fecha_venta" required>
          </div>
          <div class="mb-3">
            <label>Total</label>
            <input type="number" class="form-control" name="total" step="0.01" id="total" readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar venta</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!--Modal editarVenta-->
<div class="modal fade" id="editarVentaModal" tabindex="-1" aria-labelledby="editarVentaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
  <form method="POST" id="formEditarVenta">
  <?php echo csrf_field(); ?>
  <input type="hidden" name="editar_id" id="editar_id">
  <div class="modal-content modal-dialog modal-dialog-scrollable">
    <div class="modal-header">
      <h5 class="modal-title" id="editarVentaModalLabel">Editar Venta</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
    </div>
    <div class="modal-body">
      <div class="mb-3">
        <label for="editar_cliente">Cliente:</label>
        <select class="form-control" name="id_cliente" id="editar_cliente" required>
          <option value="">Selecciona un cliente</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="editar_usuario">Usuario:</label>
        <select class="form-control" name="id_usuario" id="editar_usuario" required>
          <option value="">Selecciona un usuario</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="editar_fecha_venta">Fecha_venta:</label>
        <input type="date" class="form-control" name="fecha_venta" id="editar_fecha_venta" " >
      </div>
      <div class="table-responsive-lg">
        <table class="table table-hover table-striped" id="tablaDetalles">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Subtotal</th>
              <th>IVA</th>
              <th>Total</th>
              <th>Quitar</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
        <button type="button" class="btn btn-primary" data-agregar-filaDetalles >Agregar producto</button>
      <div class="mb-3">
        <label>Total</label>
        <input type="number" class="form-control" name="total" step="0.01" id="editar_total" readonly>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <button type="submit" class="btn btn-primary">Actualizar Venta</button>
    </div>
  </div>
</form>
  </div>
</div>
<div class="modal fade" id="detallesVentaModal" tabindex="-1" aria-labelledby="detallesVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3 modal-scroll" style="font-family: 'Courier New', Courier, monospace;">
      <div class="modal-header text-center">
        <h5 class="modal-title w-100" id="detallesVentaLabel">Ticket de Venta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p><strong>Cliente:</strong> <span id="ticket_cliente"></span></p>
        <p><strong>Usuario:</strong> <span id="ticket_usuario"></span></p>
        <p><strong>Fecha:</strong> <span id="ticket_fecha"></span></p>
        <hr>
        <div class="table-responsive">
          <table class="table table-sm table-borderless">
            <thead class="border-bottom">
              <tr>
                <th>Producto</th>
                <th class="text-end">Cant</th>
                <th class="text-end">P.Unit</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody id="ticket_productos"></tbody>
          </table>
        </div>
        <hr>
        <h5 class="text-end">Total: <span id="ticket_total"></span></h5>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-info" onclick="imprimirVenta()">Imprimir</button>
        <button type="button" class="btn btn-success" onclick="generarPDFVenta()">Descargar PDF</button>
      </div>
    </div>
  </div>
</div>



<?php $__env->stopSection(); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/funciones/funciones_ventas.js']); ?>
<script>
    window.mensajeSuccess = <?php echo json_encode(session('success'), 15, 512) ?>;
    window.mensajeError = <?php echo json_encode(session('error'), 15, 512) ?>;
</script>
<script>
  function imprimirVenta() {
    const contenido = document.querySelector('#detallesVentaModal .modal-body').innerHTML;
    const ventana = window.open('', '', 'width=800,height=600');
    ventana.document.write(`
      <html>
        <head>
          <title>Ticket de Venta</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body style="font-family: 'Courier New', Courier, monospace;">
          ${contenido}
        </body>
      </html>
    `);
    ventana.document.close();
    ventana.focus();
    ventana.print();
    ventana.close();
  }
</script>
<script>
  function generarPDFVenta() {
    const contenido = document.querySelector('#detallesVentaModal .modal-body');

    const opciones = {
      margin:       10,
      filename:     'ticket_venta.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opciones).from(contenido).save();
  }
</script>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Aldo\AgendaComercialV2.5\resources\views/paginas/ventas.blade.php ENDPATH**/ ?>