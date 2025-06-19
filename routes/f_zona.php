<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\zonasController;

Route::prefix('zonas')->group(function () {
    Route::get('/tabla/{estatus}',[zonasController::class, 'MostrarZonas'])->name('zonas');
    Route::post('/insertar', [zonasController::class,'CrearZona'])->name('zonas.crear');
    Route::post('/actualizar/{id}', [zonasController::class,'modificarZona'])->name('zonas.actualizar');
    Route::get('/obtener/{id}', [zonasController::class,'obtenerZona']);
    Route::post('/eliminar/{id}', [zonasController::class, 'eliminarZona']);
    Route::post('/restaurar/{id}', [zonasController::class, 'restaurarZona']);
});
