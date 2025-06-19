<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriasController;

Route::prefix('categoria')->group(function () {
    Route::get('/tabla/{estatus}',[CategoriasController::class,'MostrarCatalogo'])->name('categorias');
    Route::post('/insertar', [CategoriasController::class,'CrearCategoria'])->name('categorias.crear');
    Route::post('/actualizar/{id}', [CategoriasController::class,'ModificarCategoria'])->name('categorias.actualizar');
    Route::get('/obtener', [CategoriasController::class, 'obtenerCategorias']);
    Route::get('/buscar/{id}', [CategoriasController::class, 'buscarCategoria']);
    Route::post('/eliminar/{id}', [CategoriasController::class, 'eliminarCategoria']);
    Route::post('/restaurar/{id}', [CategoriasController::class, 'restaurarCategoria']);
});
