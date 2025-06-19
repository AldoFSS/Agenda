<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\estadosController;

Route::prefix('estados')->group(function () {
    Route::get('/tabla/{estatus}',[estadosController::class, 'MostrarEstados'])->name('estados.tabla');
    Route::get('/buscar',[estadosController::class, 'BuscarEstados']);
    Route::post('/eliminar/{id}', [estadosController::class, 'eliminarEstado']);
    Route::post('/restaurar/{id}', [estadosController::class, 'restaurarEstado']);
});
