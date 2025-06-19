@extends('layouts.app')
@section('contenido')
<div class="d-flex justify-content-center">
    <form id="formulario-grafica"  class="d-flex align-items-end gap-3 flex-wrap">
        <div>
            <input type="date" name="fecha_inicio" class="form-control" required>
        </div>
        <div>
            <input type="date" name="fecha_final" class="form-control" required>
        </div>
        <div>
            <select class="form-select" name="tipo" required>
                <option value="ventas">Ventas</option>
                <option value="productos">Productos</option>
                <option value="clientes">Clientes</option>
                <option value="usuarios">Usuarios</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Mostrar</button>
        </div>
    </form>
</div>
<div class="container position-relative text-center">
    <div id="grafica-placeholder" class="text-muted" style="padding: 100px 0;">
        <i class="fas fa-chart-bar fa-3x mb-2"></i>
    </div>
    <canvas id="miGrafica"></canvas>
</div>
@endsection
@vite(['resources/js/funciones/grafico.js'])