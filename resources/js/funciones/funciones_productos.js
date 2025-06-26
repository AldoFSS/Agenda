$(document).ready(function () {

    cargarSelect('/categoria/obtener', '#select_categoria, #editar_categoria', 'Seleccione una Categoría', 'id_categoria', 'nombre_categoria');
    cargarSelect('/marcas/obtener', '#select_marca, #editar_marca', 'Seleccione una Marca', 'id_marca', 'nombre_marca');
    cargarSelect('/cliente/obtenerProveedor', '#select_proveedor, #editar_proveedor', 'Seleccione un proveedor', 'id_proveedor', 'nombre_proveedor');

    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    $('#editar_categoria, #select_categoria').on('change', function () {
        cargarSubcategorias($(this).val());
    });
    cargarTabla();
    generarCodigoDeBarras('#codigoProducto', '#barcode');
    generarCodigoDeBarras('#editar_codigo', '#barcode_editar');
});


$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarProducto(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarProducto(id);
});
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

function cargarTabla(estatus = 'All') {
    $('#tabla_productos').DataTable({
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
            url: `/producto/tabla/${estatus}`,
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_producto' },
            {
                data: 'imagen_producto',
                render: function(data) {
                    if (data) {
                        return `<img src="${data}" width="50" height="50" alt="Imagen" style="object-fit: cover; border-radius: 5px;">`;
                    } else {
                        return `<img src="imgproducto/user_default.png" width="50" height="50" alt="Sin imagen">`;
                    }
                }
            },
            { data: 'nombre_producto' },
            { data: 'categoria' },
            { data: 'subcategoria' },
            { data: 'marca' },
            { data: 'stock' },
            { data: 'precio_unitario' },
            { data: 'precio_venta' },
            { data: 'IVA_producto' },
            { data: 'codigo' },
            { data: 'proveedor' },
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
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const botonEditar = `
                        <li>
                            <a title="Editar" href="#" class="btn btn-primary btn-editarProducto"
                                data-id="${row.id_producto}" data-bs-toggle="modal" data-bs-target="#editarProductoModal">
                                <i class="fas fa-edit"></i>
                            </a>
                        </li>`;
                    let botonAccion = '';
                    if (row.estatus == 1) {
                        botonAccion = `
                            <li>
                                <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                    data-tipo="usuario" data-id="${row.id_producto}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>`;
                    } else if (row.estatus == 0) {
                        botonAccion = `
                            <li>
                                <a title="Restaurar" class="btn btn-success btn-restaurar"
                                    data-tipo="usuario" data-id="${row.id_producto}">
                                    <i class="fas fa-trash-restore"></i>
                                </a>
                            </li>`;
                    }
                    return `<ul class="opciones">${botonEditar}${botonAccion}</ul>`;
                }
            }
        ]
    });
}

$(document).on('click', '.btn-editarProducto', function () {
    var id = $(this).data('id');
    $.ajax({
        url: `/producto/buscar/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                $('#editar_id').val(res.id_producto);
                $('#editar_nombre').val(res.nombre_producto);
                $('#editar_proveedor').val(res.id_proveedor);
                $('#editar_categoria').val(res.id_catg);
                $('#editar_marca').val(res.id_marc);
                $('#editar_stock').val(res.stock);
                $('#editar_codigo').val(res.codigo);
                $('#editar_precio_unitario').val(res.precio_unitario);
                $('#editar_precio_venta').val(res.precio_venta);
                $('#editar_IVA_producto').val(res.IVA_producto);
                cargarSubcategorias(res.id_catg, res.id_subcatg)
                // Generar código de barras
                JsBarcode("#barcode_editar", res.codigo, {
                    format: "CODE128",
                    displayValue: true,
                    fontSize: 14,
                    height: 40
                });

                $('#editarProductoModal').modal('show');
            }
        },
        error: function () {
            alert('Error al obtener los datos del producto');
        }
    });
});

// Enviar formulario de edición
$('#formEditarProducto').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'EditarProducto');

    $.ajax({
        url: `/producto/actualizar/${$('#editar_id').val()}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            try {
                let res = typeof response === 'object' ? response : JSON.parse(response);
                if (res.success) {
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
                    $('#editarProductoModal').modal('hide');
                    $('#tabla_productos').DataTable().ajax.reload();
                    $('#formEditarProducto')[0].reset();
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

$('#formCrearProducto').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'CrearProducto');

    $.ajax({
        url: '/producto/insertar',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            try {
                let res = typeof response === 'object' ? response : JSON.parse(response);
                if (res.success) {
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
                    $('#crearProductoModal').modal('hide');
                    $('#tabla_productos').DataTable().ajax.reload();
                    $('#formCrearProducto')[0].reset();
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
function eliminarProducto(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar el Producto`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/producto/eliminar/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
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
          $('#tabla_productos').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar el Producto`, "error"));
    }
  });
}
function restaurarProducto(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar el Producto`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/producto/restaurar/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
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
          $('#tabla_productos').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar el Producto`, "error"));
    }
  });
}
function cargarSubcategorias(id_categoria, id_subcategoriaSeleccionado = null) {
    const subcategoriaSelects = document.querySelectorAll('#editar_subcategoria, #select_subcategoria');

    subcategoriaSelects.forEach(select => {
        $(select).html('<option value="">Cargando...</option>');
    });

    if (id_categoria !== '') {
        $.ajax({
            url: `subcategoria/obtener/${id_categoria}`,
            type: 'GET',
            dataType: 'JSON',
            success: function (subcategorias) {
                subcategoriaSelects.forEach(select => {
                    $(select).empty().append('<option value="">Seleccione una Subcategoría</option>');
                    subcategorias.data.forEach(subcategoria => {
                        const selected = (subcategoria.id_subcategoria == id_subcategoriaSeleccionado ? 'selected' : '');
                        $(select).append(`<option value="${subcategoria.id_subcategoria}" ${selected}>${subcategoria.nombre_subcategoria}</option>`);
                    });
                });
            },
            error: function () {
                subcategoriaSelects.forEach(select => {
                    $(select).html('<option value="">Error al cargar</option>');
                });
            }
        });
    }
}
function generarCodigoDeBarras(inputSelector, barcodeSelector) {
        $(inputSelector).on('input', function () {
            const valorCodigo = $(this).val().trim();
            const barcodeElement = document.querySelector(barcodeSelector);

            if (valorCodigo.length > 0) {
                JsBarcode(barcodeElement, valorCodigo, {
                    format: "CODE128",
                    displayValue: true,
                    fontSize: 14,
                    height: 40
                });
            } else {
                barcodeElement.innerHTML = '';
            }
        });
    }