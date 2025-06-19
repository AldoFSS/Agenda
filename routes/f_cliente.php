<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientesController;

Route::prefix('cliente')->group(function () {
    Route::get('/tabla/{estatus}',[ClientesController::class,'mostrarClientes'])->name('clientes.tabla');
    Route::post('/actualizar/{id}', [ClientesController::class, 'actualizarCliente'])->name('cliente.actualizar');
    Route::post('/insertar', [ClientesController::class, 'crearCliente'])->name('cliente.crear');
    Route::get('/buscar/{id}', [ClientesController::class, 'obtenerCliente']);
    Route::get('/obtener', [ClientesController::class, 'buscarCliente']);
    Route::get('/obtenerProveedor', [ClientesController::class, 'obtenerProveedor']);
    Route::post('/eliminar/{id}', [ClientesController::class, 'eliminarCliente']);
    Route::post('/restaurar/{id}', [ClientesController::class, 'restaurarCliente']);
});
