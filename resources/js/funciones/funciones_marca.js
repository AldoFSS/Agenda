$(document).ready(function () {
    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    cargarTabla();
});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarMarca(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarMarca(id);
});
function cargarTabla(estatus = 'All') {
    $('#tabla_marca').DataTable({
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
            url: `/marcas/tabla/${estatus}`,
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_marca' },
            { data: 'nombre_marca' },
            { data: 'descripcion_marca' },
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
                            <a title="Editar" href="#" class="btn btn-primary btn-editarMarca"
                                data-id="${row.id_marca}" data-bs-toggle="modal" data-bs-target="#editarMarcaModal">
                                <i class="fas fa-edit"></i>
                            </a>
                        </li>`;
                    let botonAccion = '';
                    if (row.estatus == 1) {
                        botonAccion = `
                            <li>
                                <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                    data-tipo="usuario" data-id="${row.id_marca}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>`;
                    } else if (row.estatus == 0) {
                        botonAccion = `
                            <li>
                                <a title="Restaurar" class="btn btn-success btn-restaurar"
                                    data-tipo="usuario" data-id="${row.id_marca}">
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
$(document).on('click', '.btn-editarMarca', function () {
    var id = $(this).data('id');
    $.ajax({
        url: `/marcas/buscar/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            console.log("Ola");
            if (res) {
                $('#editar_id').val(res.id_marca);
                $('#editar_marca').val(res.nombre_marca);
                $('#editar_descripcion').val(res.descripcion_marca);
                $('#editarMarcaModal').modal('show');
            }
        },
        error: function () {
            alert('Error al obtener los datos de la subcategoria');
        }
    });
});
$('#formEditarMarca').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'EditarMarca');
    $.ajax({
        url: `/marcas/actualizar/${$('#editar_id').val()}`,
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
                    $('#editarMarcaModal').modal('hide');
                    $('#tabla_marca').DataTable().ajax.reload();
                    $('#formEditarMarca')[0].reset();
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
$('#formCrearMarca').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'CrearMarca');
    $.ajax({
        url: '/marcas/insertar',
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
                    $('#crearMarcaModal').modal('hide');
                    $('#tabla_marca').DataTable().ajax.reload();
                    $('#formCrearMarca')[0].reset();
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
function eliminarMarca(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar la Marca`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/marcas/eliminar/${id}`, {
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
          $('#tabla_marca').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar la Marca`, "error"));
    }
  });
}
function restaurarMarca(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar la Marca`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/marcas/restaurar/${id}`, {
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
          $('#tabla_marca').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar la Marca`, "error"));
    }
  });
}