
$(document).ready(function () {
    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });

    cargarTabla(); 

    $('#productosTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
    });
    $('#tableDetalles').DataTable({
        responsive: true,
        dom: 'Bfrtip',
    });

});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarZona(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarZona(id);
});

// EDITAR USUARIO
$(document).on('click', '.btn-editarZona', function () {
    const id = $(this).data('id');
    $.ajax({
        url: `/zonas/obtener/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                $('#editar_id').val(res.id_zona);
                $('#editar_zona').val(res.nombre_zona);
                $('#editar_descripcion').val(res.descripcion_zona);
            }
        },
        error: function () {
            alert('Error al obtener los datos del la zona');
        }
    });
});
$('#formEditarZona').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
        url: `/zonas/actualizar/${$('#editar_id').val()}`,
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
                    $('#editarZonaModal').modal('hide');
                    $('#tabla_zonas').DataTable().ajax.reload();
                    $('#formCrearZona')[0].reset();
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

$('#formCrearZona').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('funcion', 'crearZona');

    $.ajax({
        url: '/zonas/insertar',
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
                    $('#crearZonaModal').modal('hide');
                    $('#tabla_zonas').DataTable().ajax.reload();
                    $('#formCrearZona')[0].reset();
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

function eliminarZona(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar la Zona`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/zonas/eliminar/${id}`, {
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
          $('#tabla_zonas').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar la Zona`, "error"));
    }
  });
}
function restaurarZona(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar la Zona`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/zonas/restaurar/${id}`, {
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
          $('#tabla_zonas').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar la Zona`, "error"));
    }
  });
}
function cargarTabla(estatus = 'All') {
        $('#tabla_zonas').DataTable({
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
                url: `/zonas/tabla/${estatus}`,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id_zona' },
                { data: 'nombre_zona' },
                { data: 'descripcion_zona' },
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
                                <a title="Editar" href="#" class="btn btn-primary btn-editarZona"
                                    data-id="${row.id_zona}" data-bs-toggle="modal" data-bs-target="#editarZonaModal">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </li>`;
                        let botonAccion = '';

                        if (row.estatus == 1) {
                            botonAccion = `
                                <li>
                                    <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                        data-tipo="usuario" data-id="${row.id_zona}">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </li>`;
                        } else if (row.estatus == 0) {
                            botonAccion = `
                                <li>
                                    <a title="Restaurar" class="btn btn-success btn-restaurar"
                                        data-tipo="usuario" data-id="${row.id_zona}">
                                        <i class="fas fa-trash-restore"></i>
                                    </a>
                                </li>`;
                        }

                        return `
                            <ul class="opciones">
                                ${botonEditar}
                                ${botonAccion}
                            </ul>`;
                    }
                }
            ]
        });
    }