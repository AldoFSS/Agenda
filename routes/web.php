<?php
use App\Http\Controllers\EliminacionController;
use Illuminate\Support\Facades\Route;
require __DIR__.'/f_categoria.php';
require __DIR__.'/f_cita.php';
require __DIR__.'/f_cliente.php';
require __DIR__.'/f_estado.php';
require __DIR__.'/f_grafica.php';
require __DIR__.'/f_marca.php';
require __DIR__.'/f_municipio.php';
require __DIR__.'/f_producto.php';
require __DIR__.'/f_subcategoria.php';
require __DIR__.'/f_usuario.php';
require __DIR__.'/f_venta.php';
require __DIR__.'/f_zona.php';

    Route::get('/', function () {
        return view('paginas.login');
    })->name('login');
    Route::get('/home', function () {
        return view('paginas.home');
    })->name('home');
    Route::get('/grafico', function () {
        return view('paginas.grafico');
    })->name('grafico');
    Route::get('/reporte', function () {
        return view('paginas.reportes');
    })->name('reportes');

    Route::get('/cliente', function (){
        return view('paginas.clientes');
    })->name('cliente');

    Route::get('/usuarios', function (){
        return view('paginas.usuarios');
    })->name('usuarios');

    Route::get('/producto', function (){
        return view('paginas.productos');
    })->name('productos');

    Route::get('/estados', function (){
        return view('paginas.estados');
    })->name('estados');

    Route::get('/municipios', function (){
        return view('paginas.municipios');
    })->name('municipios');
    Route::get('/zonas', function (){
        return view('paginas.zonas');
    })->name('zonas');
    Route::get('/categoria', function (){
        return view('paginas.categorias');
    })->name('categorias');
    Route::get('/subcategoria', function (){
        return view('paginas.subcategorias');
    })->name('subcategorias');
    Route::get('/marcas', function (){
        return view('paginas.marcas');
    })->name('marcas');
    Route::get('/ventas', function (){
        return view('paginas.ventas');
    })->name('ventas');