<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentasController;

Route::prefix('ventas')->group(function () {
    Route::get('/tabla/{estatus}',[VentasController::class, 'mostrarVentas'])->name('ventas');
    Route::post('/actualizar/{id}', [VentasController::class, 'actualizarVenta'])->name('ventas.actualizar');
    Route::post('/insertar', [VentasController::class, 'crearVenta'])->name('ventas.crear');
    Route::post('/eliminar/{id}', [VentasController::class, 'eliminarVenta']);
    Route::post('/restaurar/{id}', [VentasController::class, 'restaurarVenta']);
    Route::get('/detalles/{id}', [VentasController::class, 'obtenerDetalles']);
    Route::get('/ticket/{id}', [VentasController::class, 'obtenerVentaConDetalles'])->name('ventas.detalles');
 
});