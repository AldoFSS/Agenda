
<?php $__env->startSection('contenido'); ?>
<div class="d-flex justify-content-center">
    <form id="formulario-grafica"  class="d-flex align-items-end gap-3 flex-wrap">
        <div>
            <input type="date" name="fecha_inicio" class="form-control" required>
        </div>
        <div>
            <input type="date" name="fecha_final" class="form-control" required>
        </div>
        <div>
            <select class="form-select" name="tipo" required>
                <option value="ventas">Ventas</option>
                <option value="productos">Productos</option>
                <option value="clientes">Clientes</option>
                <option value="usuarios">Usuarios</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Mostrar</button>
        </div>
    </form>
</div>
<div class="container position-relative text-center">
    <div id="grafica-placeholder" class="text-muted" style="padding: 100px 0;">
        <i class="fas fa-chart-bar fa-3x mb-2"></i>
    </div>
    <canvas id="miGrafica"></canvas>
</div>
<?php $__env->stopSection(); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/funciones/grafico.js']); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Aldo\AgendaComercialV2.5\resources\views/paginas/grafico.blade.php ENDPATH**/ ?>