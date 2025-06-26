@extends('layouts.app')
@section('contenido')
<div class="container py-5">
  <h2 class="mb-4">Bienvenido al Sistema de Agenda Comercial</h2>
  <div class="row g-4">
    <!-- Tarjeta de Usuarios -->
    <div class="col-md-4">
      <div class="card shadow ">
        <div class="card-body">
          <h5 class="card-title">Usuarios Registrados</h5>
          <p class="card-text display-6 fw-bold" id="totalUsuarios"></p>
        </div>
      </div>
    </div>

    <!-- Tarjeta de Clientes -->
    <div class="col-md-4">
      <div class="card shadow ">
        <div class="card-body">
          <h5 class="card-title">Clientes Registrados</h5>
          <p class="card-text display-6 fw-bold" id="totalClientes"></p>
        </div>
      </div>
    </div>

    <!-- Tarjeta de Productos -->
    <div class="col-md-4">
      <div class="card shadow ">
        <div class="card-body">
          <h5 class="card-title">Productos Disponibles</h5>
          <p class="card-text display-6 fw-bold" id="totalProductos"></p>
        </div>
      </div>
    </div>
  </div>
</div>
@vite(['resources/js/funciones/funciones_home.js'])
@endsection
