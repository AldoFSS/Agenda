<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductosController;

Route::prefix('producto')->group(function () {
    Route::get('/tabla/{estatus}',[ProductosController::class,'MostrarProductos'])->name('productos.tabla');
    Route::post('/actualizar/{id}', [ProductosController::class,'actualizarProducto'])->name('productos.actualizar');
    Route::post('/insertar', [ProductosController::class,'crearProducto'])->name('productos.crear');
    Route::get('/buscar/{id}', [ProductosController::class,'buscarProducto']);
    Route::get('/lista', [ProductosController::class,'obtenerProductos'])->name('productos.lista');
    Route::post('/eliminar/{id}', [ProductosController::class, 'eliminarProducto']);
    Route::post('/restaurar/{id}', [ProductosController::class, 'restaurarProducto']);
});
