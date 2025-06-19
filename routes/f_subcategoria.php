<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubcategoriasController;

Route::prefix('subcategoria')->group(function () {
    Route::get('/tabla/{estatus}',[SubCategoriasController::class,'MostrarSubCategorias'])->name('subcategorias');
    Route::post('/insertar', [SubCategoriasController::class,'CrearSubCategoria'])->name('subcategorias.crear');
    Route::post('/actualizar/{id}', [SubCategoriasController::class,'ModificarSubCategoria'])->name('subcategorias.actualizar');
    Route::get('/obtener/{id}', [SubcategoriasController::class, 'obtenerSubcategorias']);
    Route::get('/buscar/{id}', [SubcategoriasController::class, 'buscarSubcategoria']);
    Route::post('/eliminar/{id}', [SubcategoriasController::class, 'eliminarSubcategoria']);
    Route::post('/restaurar/{id}', [SubcategoriasController::class, 'restaurarSubcategoria']);
});
