<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\marcasController;

Route::prefix('marcas')->group(function () {
    Route::get('/tabla/{estatus}',[marcasController::class, 'MostrarMarcas'])->name('marcas');
    Route::post('/insertar', [marcasController::class,'CrearMarca'])->name('marcas.crear');
    Route::post('/actualizar/{id}', [marcasController::class,'modificarMarca'])->name('marcas.actualizar');
    Route::get('/obtener', [marcasController::class, 'obtenerMarcas']);
    Route::get('/buscar/{id}',[marcasController::class, 'buscarMarca']);
    Route::post('/eliminar/{id}', [marcasController::class, 'eliminarMarca']);
    Route::post('/restaurar/{id}', [marcasController::class, 'restaurarMarca']);
});
