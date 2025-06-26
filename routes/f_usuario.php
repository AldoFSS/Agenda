<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

Route::prefix('usuarios')->group(function () {
    Route::get('/tabla/{estatus}', [UsuariosController::class,'mostrarUsuarios'])->name('usuarios.tabla');
    Route::post('/login', [UsuariosController::class,'login'])->name('usuarios.login');
    Route::get('/buscar/{id}',[UsuariosController::class, 'obtenerUsuario']);
    Route::post('/actualizar/{id}', [UsuariosController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::post('/insertar', [UsuariosController::class, 'crearUsuario'])->name('usuarios.crear');
    Route::post('/eliminar/{id}', [UsuariosController::class, 'eliminarUsuario']);
    Route::post('/restaurar/{id}', [UsuariosController::class, 'restaurarUsuario']);
    Route::get('/obtener', [UsuariosController::class, 'Usuarios']);
    Route::post('/logout', [UsuariosController::class, 'logout'])->name('usuarios.logout');
    Route::get('/usuario/actual', function () {
    if (Auth::check()) {
        return response()->json([
            'success' => true,
            'usuario' => Auth::user()->nombre_usuario
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'No hay usuario autenticado.'
    ], 401);
});
});
