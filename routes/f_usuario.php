<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

Route::prefix('usuarios')->group(function () {
    Route::get('/tabla/{estatus}', [UsuariosController::class,'mostrarUsuarios'])->name('usuarios.tabla');
    Route::post('/login', [UsuariosController::class,'BuscarUsuario'])->name('usuarios.login');
    Route::get('/buscar/{id}',[UsuariosController::class, 'obtenerUsuario']);
    Route::post('/actualizar/{id}', [UsuariosController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::post('/insertar', [UsuariosController::class, 'crearUsuario'])->name('usuarios.crear');
    Route::post('/eliminar/{id}', [UsuariosController::class, 'eliminarUsuario']);
    Route::post('/restaurar/{id}', [UsuariosController::class, 'restaurarUsuario']);
    Route::get('/obtener', [UsuariosController::class, 'Usuarios']);
});
