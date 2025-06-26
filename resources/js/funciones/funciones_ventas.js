let indexDetalles = 1;
let productosDisponibles = [];
let productosData = {};
let index = 0;

$(document).ready(function () {

  $('#select_estatus').on('change', function () {
    cargarTabla($(this).val());
  });
  cargarTabla();
  cargarProductos();
  cargarSelect('/cliente/obtener', '#select_cliente, #editar_cliente', 'Seleccione un Cliente', 'id_cliente', 'nombre_cliente');
  cargarSelect('/usuarios/obtener', '#select_usuario, #editar_usuario', 'Seleccione un Usuario', 'id_usuario', 'nombre_usuario');
});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarVenta(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarVenta(id);
});

document.addEventListener("DOMContentLoaded", function () {
  document.querySelector('#productosTable tbody').addEventListener('change', function(event){
    if(event.target && event.target.matches('select[name^="productos"]')){
      actualizarProducto(event);
    }
  });
  document.querySelector('#tablaDetalles tbody').addEventListener('change', function(event){
    if(event.target && event.target.matches('select[name^="productos"]')){
      actualizarProducto(event);
    }
  });
  //Carga los productos y agrega una nueva fila por defecto
  document.querySelectorAll('.btn-crear').forEach(boton => {
    boton.addEventListener('click', async function() {
      try {
       await cargarProductos();
        agregarFila();
      } catch (error) {
        console.error("Error cargando productos:", error);
      }
    });
  });
  // Botón para agregar una nueva fila en detalles de venta
  const botonDetalles = document.querySelector('[data-agregar-filaDetalles]');
  if (botonDetalles) {
    botonDetalles.addEventListener('click', agregarFilaDetalles);
  }

  // Botón para agregar una nueva fila en una venta nueva
  const botonAgregarFila = document.querySelector('[data-agregar-fila]');
  if (botonAgregarFila) {
    botonAgregarFila.addEventListener('click', agregarFila);
  }
  document.addEventListener('click', async function (e) {
  if (e.target.closest('.btn-detallesVenta')) {
    const boton = e.target.closest('.btn-detallesVenta');
    const idVenta = boton.dataset.id;

    try {
      const response = await fetch(`/ventas/ticket/${idVenta}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        }
      });
      if (!response.ok) throw new Error('Error en la solicitud');
      const res = await response.json();
      if (res) {
        console.log(res.venta);
        console.log(res.detalles);
        cargarTicketVenta(res);
      } else {
        console.log("error, respuesta vacía");
      }
    } catch (error) {
      console.error('Error al obtener los detalles de la venta:', error);
    }
  }
});

});

// Genera las opciones HTML para los selects de productos según los productosDisponibles de la BD
function generarOpcionesProductos(idSeleccionado) {
  return productosDisponibles.map(p =>
    `<option value="${p.id_producto}" ${p.id_producto === idSeleccionado ? 'selected' : ''}>${p.nombre_producto}</option>`
  ).join('');
}

// Función que se ejecuta cuando cambia el producto del select actualiza el precio automáticamente
function actualizarProducto(event) {
  const selectElement = event.target; 
  const id_producto = selectElement.value; 
  const fila = selectElement.closest('tr'); 
  const inputPrecio = fila.querySelector('input[name$="[precio_venta]"]'); 

  // Si no hay producto seleccionado o no existe en datos, limpia el precio y subtotal
  if (!id_producto || !productosData[id_producto]) {
    inputPrecio.value = "";
    actualizarSubtotalFila(fila); 
    return;
  }

  // Obtiene el precio del producto y actualiza el input precio
  const precio = parseFloat(productosData[id_producto].precio_venta);
  inputPrecio.value = precio.toFixed(2);

  actualizarSubtotalFila(fila);
}

// Calcula el total sumando subtotales de todas las filas de ambas tablas
function calcularTotal() {
  let total = 0;
  
  document.querySelectorAll('#tablaDetalles tbody tr, #productosTable tbody tr').forEach(fila => {
    const totalProductoInput =  fila.querySelector('input[name$="[total]"]');

    total += parseFloat(totalProductoInput?.value) || 0;
  });

  // Actualiza los inputs del total 
  const totalInputDetalles = document.getElementById('editar_total');
  const totalInput = document.getElementById('total');
  if (totalInputDetalles) totalInputDetalles.value = total.toFixed(2);
  if (totalInput) totalInput.value = total.toFixed(2);
}

function cargarTicketVenta(data) {
  const venta = data.venta;
  const detalles = data.detalles; 

  document.getElementById('ticket_cliente').textContent = venta.Cliente;
  document.getElementById('ticket_usuario').textContent = venta.Usuario;
  document.getElementById('ticket_fecha').textContent = venta.fecha_venta;
  document.getElementById('ticket_total').textContent = `$${parseFloat(venta.total).toFixed(2)}`;

  let html = '';
  detalles.forEach(detalle => {
    html += `<tr>
      <td>${detalle.nombre_producto}</td>
      <td class="text-end">${detalle.cantidad}</td>
      <td class="text-end">$${parseFloat(detalle.precio_venta).toFixed(2)}</td>
      <td class="text-end">$${parseFloat(detalle.total).toFixed(2)}</td>
    </tr>`;
  });

  document.getElementById('ticket_productos').innerHTML = html;
}
function cargarTabla(estatus = 'All'){
  $('#tabla_ventas').DataTable({
      responsive: true,
      dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', text: 'Copiar' },
            { extend: 'csv', text: 'CSV' },
            { extend: 'excel', text: 'Excel' },
            { extend: 'pdf', text: 'PDF' },
            { extend: 'print', text: 'Imprimir' }
        ],
        aLengthMenu: [[5, 10, 25, -1], [5, 10, 25, "Todos"]],
        iDisplayLength: 5,
        destroy: true,
        ajax: {
            url: `/ventas/tabla/${estatus}`,
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_venta' },
            { data: 'Cliente' },
            { data: 'Usuario' },
            {
              data: 'fecha_venta',
              render: function(data) {
                if (!data) return '';
                const fecha = new Date(data);
                const opciones = { day: 'numeric', month: 'long', year: 'numeric' };
                let fechaFormateada = fecha.toLocaleDateString('es-MX', opciones);
                fechaFormateada = fechaFormateada.replace(' de ', ' ');
                return fechaFormateada;
              }
            },
            {
              data: 'created_at',
              render: function(data) {
                if (!data) return '';
                const fecha = new Date(data);
                const opciones = { day: 'numeric', month: 'long', year: 'numeric' };
                let fechaFormateada = fecha.toLocaleDateString('es-MX', opciones);
                fechaFormateada = fechaFormateada.replace(' de ', ' ');
                return fechaFormateada;
              }
            },
            { data: 'total' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const botonEditar = `
                        <li>
                            <a title="Editar" href="#" class="btn btn-primary btn-editarVenta"
                                data-id="${row.id_venta}" data-bs-toggle="modal" data-bs-target="#editarVentaModal">
                                <i class="fas fa-edit"></i>
                            </a>
                        </li>`;
                    const botonDetalles = `
                        <li>
                            <a title="Detalles" href="#" class="btn btn-primary btn-detallesVenta"
                                data-id="${row.id_venta}" data-bs-toggle="modal" data-bs-target="#detallesVentaModal">
                                <i class="fas fa-eye"></i>
                            </a>
                        </li>`;
                    let botonAccion = '';
                    if (row.estatus == 1) {
                        botonAccion = `
                            <li>
                                <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                    data-tipo="usuario" data-id="${row.id_venta}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>`;
                    } else if (row.estatus == 0) {
                        botonAccion = `
                            <li>
                                <a title="Restaurar" class="btn btn-success btn-restaurar"
                                    data-tipo="usuario" data-id="${row.id_venta}">
                                    <i class="fas fa-trash-restore"></i>
                                </a>
                            </li>`;
                    }
                    return `<ul class="opciones">${botonEditar}${botonDetalles}${botonAccion}</ul>`;
                }
            }
        ]
    });
}
function cargarSelect(url, selector, placeholder, key, value) {
    const selects = document.querySelectorAll(selector);
    $.ajax({
        url,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            selects.forEach(select => {
                $(select).empty().append(`<option value="">${placeholder}</option>`);
                response.data.forEach(item => {
                    $(select).append(`<option value="${item[key]}">${item[value]}</option>`);
                });
            });
        },
        error: function () {
            alert(`Error al cargar los datos de ${placeholder}`);
        }
    });
}
async function cargarProductos() {
  try {
    const res = await fetch('/producto/lista');
    productosDisponibles = await res.json();
    productosData = {};
    productosDisponibles.forEach(p => {
      productosData[p.id_producto] = p;
    });
  } catch (error) {
    console.error("Error al cargar productos:", error);
  }
}
// Función para agregar una nueva fila en la tabla principal para Crear una Venta
function agregarFila() { 
  const table = document.querySelector('#productosTable tbody');
  const fila = table.insertRow();

  // Llena la fila con inputs y select para producto, cantidad, precio, subtotal, IVA y total
  fila.innerHTML = `
    <td>
      <select class="form form-control" name="productos[${index}][id_producto]" required>
        <option value="">Producto</option>
        ${generarOpcionesProductos()}
      </select>
    </td>
    <td><input type="number" class="form form-control" name="productos[${index}][cantidad]" required></td>
    <td><input type="number" class="form form-control" step="0.01" name="productos[${index}][precio_venta]" readonly></td>
    <td><input type="number" class="form form-control" step="0.01" name="productos[${index}][subtotal]" readonly></td>
    <td><input type="number" class="form form-control" step="0.01" name="productos[${index}][IVA]" readonly></td>
    <td><input type="number" class="form form-control" step="0.01" name="productos[${index}][total]" readonly></td>
    <td><button type="button" class="btn btn-primary btn-eliminar-fila">X</button></td>
  `;

  // Evento para eliminar una fila con confirmación y actualizar el total tras eliminar
  fila.querySelector('.btn-eliminar-fila').addEventListener('click', function () {
    confirmarEliminarFila(this, () => {
      fila.remove();
      calcularTotal();
    });
  });

  // Evento para actualizar subtotal cuando cambie la cantidad
  const inputCantidad = fila.querySelector('input[name$="[cantidad]"]');
  inputCantidad.addEventListener('input', () => {
    actualizarSubtotalFila(fila);
  });

  index++; 
}
// Muestra un sweetAlert de confirmación para eliminar una fila
function confirmarEliminarFila(btn, callback) {
  Swal.fire({
    title: '¿Estás seguro de eliminar el producto?',
    text: "No podrás revertir esto",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      callback();
      Swal.fire('Eliminado', 'El producto ha sido eliminado.', 'success');
    }
  });
}
// Actualiza el subtotal, IVA y total en una fila cuando cambian cantidad o precio
function actualizarSubtotalFila(fila) {
  const inputCantidad = fila.querySelector('input[name$="[cantidad]"]');
  const inputPrecio = fila.querySelector('input[name$="[precio_venta]"]');
  const inputSubtotal = fila.querySelector('input[name$="[subtotal]"]');
  const inputIVA = fila.querySelector('input[name$="[IVA]"]');
  const inputTotal = fila.querySelector('input[name$="[total]"]');
  const selectProducto = fila.querySelector('select[name^="productos"]');

  const cantidad = parseFloat(inputCantidad?.value) || 0;
  const precio = parseFloat(inputPrecio?.value) || 0;
  const subtotal = cantidad * precio;
  const IVA = subtotal * 0.16; 
  const totalDetalles = subtotal + IVA;

  const idProducto = parseInt(selectProducto?.value);
  
  const producto = productosDisponibles.find(p => p.id_producto === idProducto);

  if (producto && cantidad > producto.stock) {
    Swal.fire({
      icon: 'warning',
      title: 'Stock insuficiente',
      text: `El producto "${producto.nombre_producto}" tiene solo ${producto.stock} en stock.`,
    });

    inputCantidad.value = producto.stock; 
    return; 
  }
  
  inputSubtotal.value = subtotal.toFixed(2);
  inputIVA.value = IVA.toFixed(2);
  inputTotal.value = totalDetalles.toFixed(2);

  // Recalcula el total general de todas las filas
  calcularTotal();
}

function agregarFilaDetalles(detalle = null) {
  const table = document.querySelector('#tablaDetalles tbody');
  const fila = table.insertRow();
  const index = table.querySelectorAll('tr').length;

  const id_producto = detalle ? detalle.id_producto : '';
  const cantidad = detalle ? detalle.cantidad : '';
  const precio_venta = detalle ? detalle.precio_venta : '';
  const subtotal = detalle ? detalle.subtotal : '';
  const IVA = detalle ? detalle.IVA : '';
  const total = detalle ? detalle.total : '';

  fila.innerHTML = `
    <td>
      <select class="form form-control" name="productos[${index}][id_producto]" required>
        <option value="">Producto</option>
        ${generarOpcionesProductos(id_producto)}
      </select>
    </td>
    <td><input type="number" class="form form-control" value="${cantidad}" name="productos[${index}][cantidad]" required></td>
    <td><input type="number" class="form form-control" step="0.01" value="${precio_venta}" name="productos[${index}][precio_venta]" readonly></td>
    <td><input type="number" class="form form-control" step="0.01" value="${subtotal}" name="productos[${index}][subtotal]" readonly></td>
    <td><input type="number" class="form form-control" step="0.01" value="${IVA}" name="productos[${index}][IVA]" readonly></td>
    <td><input type="number" class="form form-control" step="0.01" value="${total}" name="productos[${index}][total]" readonly></td>
    <td><button type="button" class="btn btn-primary btn-eliminar-filaDetalles">X</button></td>
  `;

  fila.querySelector('.btn-eliminar-filaDetalles').addEventListener('click', function () {
    confirmarEliminarFila(this, () => {
      fila.remove();
      calcularTotal();
    });
  });

  fila.querySelector(`input[name="productos[${index}][cantidad]"]`).addEventListener('input', () => {
    actualizarSubtotalFila(fila);
  });
}
function eliminarVenta(id) {
  Swal.fire({
    title: "¿Estás seguro de anular esta venta?",
    text: "Los productos se regresarán al inventario. Podrás restaurarla más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, anular",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/ventas/eliminar/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          cargarProductos();
          Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.title,
                text: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
          $('#tabla_ventas').DataTable().ajax.reload();
        } else {
          Swal.fire("Error", data.message || "No se pudo anular la venta", "error");
        }
      })
      .catch(() => Swal.fire("Error", "Error en la solicitud al anular la venta", "error"));
    }
  });
}

function restaurarVenta(id) {
  Swal.fire({
    title: "¿Estás seguro de restaurar esta venta?",
    text: "Los productos serán descontados del inventario nuevamente.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/ventas/restaurar/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          cargarProductos();
          Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.title,
                text: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
          $('#tabla_ventas').DataTable().ajax.reload();
        } else {
          Swal.fire("Error", data.message || "No se pudo restaurar la venta", "error");
        }
      })
      .catch(() => Swal.fire("Error", "Error en la solicitud al restaurar la venta", "error"));
    }
  });
}

$('#formEditarVenta').on('submit', function (e){
  e.preventDefault();
  var formData = new FormData(this);
  formData.append('funcion', 'EditarVenta');

  $.ajax({
    url: `/ventas/actualizar/${$('#editar_id').val()}`,
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    success: function(response){
      try {
        let res = typeof response === 'object' ? response : JSON.parse(response);
        if (res.success) {
          cargarProductos();
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: res.title,
              text: res.message,
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
            });
            $('#editarVentaModal').modal('hide');
            $('#tabla_ventas').DataTable().ajax.reload();
            $('#formEditarVenta')[0].reset();
          } else {
            Swal.fire({
              icon: 'error',
              title: res.title,
              text: res.message,
            });
          }
        } catch (e) {
          console.error("Error al parsear JSON:", e, response);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Respuesta inválida del servidor. Revisa consola.'
        });
      }
    },
    error: function (err) {
      console.error("Error en petición:", err);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error en la solicitud. Revisa consola.'
      });
    }
  });
});

$(document).on('click', '.btn-editarVenta', function () {
  var id = $(this).data('id');

  $.ajax({
    url: `/ventas/ticket/${id}`,
    method: 'GET',
    dataType: 'json',
    headers: {
      'Accept': 'application/json',
    },
    success: function (res) {
      const venta = res.venta;
      const detalles = res.detalles;

      if (!venta || !Array.isArray(detalles)) {
        console.error("La respuesta no tiene la estructura esperada:", res);
        return;
      }

      // Rellenar campos generales
      $('#editar_id').val(venta.id_venta);
      $('#editar_cliente').val(venta.id_cli);
      $('#editar_usuario').val(venta.id_usr);
      $('#editar_fecha_venta').val(venta.fecha_venta);
      $('#editar_total').val(venta.total);

      // Mostrar el modal
      $('#editarVentaModal').modal('show');

      // Limpiar tabla y agregar filas con datos
      const tablaBody = document.querySelector('#tablaDetalles tbody');
      tablaBody.innerHTML = '';

      detalles.forEach(detalle => {
        agregarFilaDetalles(detalle);  // ← aquí se usa la nueva función
      });

      // Actualizar el total general
      calcularTotal(); // O actualizarTotalGeneral() si la usas aparte
    },
    error: function () {
      alert('Error al obtener los datos de la venta');
    }
  });
});
$('#formCrearVenta').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'Crearventa');

    $.ajax({
        url: '/ventas/insertar',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            try {
                let res = typeof response === 'object' ? response : JSON.parse(response);
                if (res.success) {
                  cargarProductos();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.title,
                        text: res.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    $('#crearVentaModal').modal('hide');
                    $('#tabla_ventas').DataTable().ajax.reload();
                    $('#formCrearVenta')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.title,
                        text: res.message,
                    });
                }
            } catch (e) {
                console.error("Error al parsear JSON:", e, response);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Respuesta inválida del servidor. Revisa consola.'
                });
            }
        },
        error: function (err) {
            console.error("Error en petición:", err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la solicitud. Revisa consola.'
            });
        }
    });
});