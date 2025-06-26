
<?php $__env->startSection('contenido'); ?>
<div class="container py-5">
  <h2 class="mb-4">Bienvenido al Sistema de Agenda Comercial</h2>
  <div class="row g-4">
    <!-- Tarjeta de Usuarios -->
    <div class="col-md-4">
      <div class="card shadow ">
        <div class="card-body">
          <h5 class="card-title">Usuarios Registrados</h5>
          <p class="card-text display-6 fw-bold" id="totalUsuarios"></p>
        </div>
      </div>
    </div>

    <!-- Tarjeta de Clientes -->
    <div class="col-md-4">
      <div class="card shadow ">
        <div class="card-body">
          <h5 class="card-title">Clientes Registrados</h5>
          <p class="card-text display-6 fw-bold" id="totalClientes"></p>
        </div>
      </div>
    </div>

    <!-- Tarjeta de Productos -->
    <div class="col-md-4">
      <div class="card shadow ">
        <div class="card-body">
          <h5 class="card-title">Productos Disponibles</h5>
          <p class="card-text display-6 fw-bold" id="totalProductos"></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/funciones/funciones_home.js']); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Aldo\AgendaComercialV2.5\resources\views/paginas/home.blade.php ENDPATH**/ ?>