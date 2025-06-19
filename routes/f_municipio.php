<?php
use App\Models\municipios;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\municipiosController;
Route::prefix('municipios')->group(function () {
    Route::get('/tabla/{estatus}',[municipiosController::class, 'MostrarMunicipios'])->name('municipios');
    Route::post('/insertar', [municipiosController::class,'CrearMunicipio'])->name('municipios.crear');
    Route::post('/actualizar/{id}', [municipiosController::class,'EditarMunicipio'])->name('municipios.actualizar');
    Route::get('/buscarMunicipio/{id}', [municipiosController::class, 'BuscarMunicipio'])->name('municipios.buscar');
    Route::get('/obtener/{id}',[municipiosController::class, 'obtenerMunicipio']);
    Route::post('/eliminar/{id}', [municipiosController::class, 'eliminarMunicipio']);
    Route::post('/restaurar/{id}', [municipiosController::class, 'restaurarMunicipio']);
});