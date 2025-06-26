$(document).ready(function () {
  $.ajax({
    url: '/contadores',
    type: 'GET',
    dataType: 'json',
    success: function (data) {
      $('#totalUsuarios').text(data.total_usuarios);
      $('#totalClientes').text(data.total_clientes);
      $('#totalProductos').text(data.total_productos);
    },
    error: function () {
      console.error("Error al obtener los datos");
    }
  });
});
