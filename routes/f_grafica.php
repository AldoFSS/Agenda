<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\graficaController;

Route::prefix('grafico')->group(function () {
    Route::get('/reporte{tipoGrafica},{fecha_inicio},{fecha_final},{id}', [graficaController::class, 'reporteGrafica'])->name('reporte.mostrar');
    Route::get('/mostrar/{tipoGrafica},{fecha_inicio},{fecha_final}', [graficaController::class, 'mostrarGrafica'])->name('grafico.mostrar');
    Route::get('/opciones/{tipo}',[graficaController::class,'mostrarOpciones'])->name('reporte.tipo');
});
