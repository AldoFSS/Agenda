<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitasController;
Route::get('/google-calendar/redirect', [CitasController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google-calendar/callback', [CitasController::class, 'handleGoogleCallback'])->name('google.callback');

Route::prefix('citas')->group(function () {
    Route::get('/',[CitasController::class, 'mostrarCitas'])->name('citas');
    Route::put('/actualizar/{id}', [CitasController::class, 'actualizarCita'])->name('citas.actualizar');
    Route::put('/actualizarfecha/{id}',[CitasController::class, 'actualizarFecha'])->name('citas.actfecha');
    Route::post('/eliminar/{id}', [CitasController::class, 'eliminarCita'])->name('citas.eliminar');
    Route::post('/insertar', [CitasController::class, 'crearCita'])->name('citas.crear');
    Route::get('/obtenerEvento',[CitasController::class,'obtenerEventos'])->name('citas.obtenerEventos');
});
