$(document).ready(function () {
    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    // Cargar estados al cargar la página
    const estadoSelects = document.querySelectorAll('#Select_estado, #editar_estado');
    $.ajax({
        url: '/estados/buscar',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            estadoSelects.forEach(select => {
                $(select).empty().append('<option value="">Seleccione un Estado</option>');
                response.data.forEach(estado => {
                    $(select).append(`<option value="${estado.id_estado}">${estado.nombre_estado}</option>`);
                });
            });
        },
        error: function () {
            alert('Error al cargar los estados');
        }
    });

    $('#editar_estado, #Select_estado').on('change', function () {
        cargarMunicipiosPorEstado($(this).val());
    });

    cargarTabla(); // Cargar tabla al inicio
});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarCliente(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarCliente(id);
});

function cargarTabla(estatus = 'All') {
        $('#tabla_cliente').DataTable({
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
                url: `/cliente/tabla/${estatus}`,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id_cliente' },
                {
                    data: 'imagen',
                    render: function(data) {
                        if (data) {
                            return `<img src="${data}" width="50" height="50" alt="Imagen" style="object-fit: cover; border-radius: 5px;">`;
                        } else {
                            return `<img src="imgcliente/user_default.png" width="50" height="50" alt="Sin imagen">`; // o deja un texto si no hay imagen
                        }
                    }
                },
                { data: 'nombre_cliente' },
                { data: 'nombre_comercial' },
                { data: 'rol' },
                { data: 'telefono' },
                { data: 'correo' },
                { data: 'codigo_postal' },
                { data: 'colonia' },
                { data: 'calle' },
                { data: 'Estado' },
                { data: 'Municipio' },
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
                <a title="Editar" href="#" class="btn btn-primary btn-editarCliente"
                    data-id="${row.id_cliente}" data-bs-toggle="modal" data-bs-target="#editarClienteModal">
                    <i class="fas fa-edit"></i>
                </a>
            </li>`;

        let botonAccion = '';

        if (row.estatus == 1) {
            botonAccion = `
                <li>
                    <a title="Eliminar" class="btn btn-danger btn-eliminar"
                        data-tipo="cliente" data-id="${row.id_cliente}">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </li>`;
        } else if (row.estatus == 0) {
            botonAccion = `
                <li>
                    <a title="Restaurar" class="btn btn-success btn-restaurar"
                        data-tipo="cliente" data-id="${row.id_cliente}">
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

function cargarMunicipiosPorEstado(id_estado, id_municipioSeleccionado = null) {
    const municipioSelects = document.querySelectorAll('#editar_municipio, #Select_municipio');

    municipioSelects.forEach(select => {
        $(select).html('<option value="">Cargando...</option>');
    });

    if (id_estado !== '') {
        $.ajax({
            url: `/municipios/buscarMunicipio/${id_estado}`,
            type: 'GET',
            dataType: 'json',
            success: function (municipios) {
                municipioSelects.forEach(select => {
                    $(select).empty().append('<option value="">Seleccione un Municipio</option>');
                    municipios.forEach(municipio => {
                        const selected = municipio.id_municipio == id_municipioSeleccionado ? 'selected' : '';
                        $(select).append(`<option value="${municipio.id_municipio}" ${selected}>${municipio.nombre_municipio}</option>`);
                    });
                });
            },
            error: function () {
                alert('Error al cargar los municipios');
                municipioSelects.forEach(select => {
                    $(select).html('<option value="">Error al cargar</option>');
                });
            }
        });
    } else {
        municipioSelects.forEach(select => {
            $(select).empty().append('<option value="">Seleccione un Municipio</option>');
        });
    }
}

function eliminarCliente(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar la Cliente`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/cliente/eliminar/${id}`, {
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
          $('#tabla_cliente').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar el Cliente`, "error"));
    }
  });
}
function restaurarCliente(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar la Cliente`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/cliente/restaurar/${id}`, {
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
          $('#tabla_cliente').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar la Cliente`, "error"));
    }
  });
}
// EDITAR CLIENTE
$(document).on('click', '.btn-editarCliente', function () {
    const id = $(this).data('id');
    $.ajax({
        url: `/cliente/buscar/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                $('#editar_id').val(res.id_cliente);
                $('#editar_nombre').val(res.nombre_cliente);
                $('#editar_nombre_comercial').val(res.nombre_comercial);
                $('#editar_telefono').val(res.telefono);
                $('#editar_correo').val(res.correo);
                $('#editar_codigo_postal').val(res.codigo_postal);
                $('#editar_colonia').val(res.colonia);
                $('#editar_calle').val(res.calle);
                $('#editar_estado').val(res.id_estado);
                $('#editar_rol').val(res.rol);
                cargarMunicipiosPorEstado(res.id_estado, res.id_municipio);
                $('#editarClienteModal').modal('show');
            }
        },
        error: function () {
            alert('Error al obtener los datos del cliente');
        }
    });
});

// FORM EDITAR
$('#formEditarCliente').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this)

    $.ajax({
        url: `/cliente/actualizar/${$('#editar_id').val()}`,
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
                    $('#editarClienteModal').modal('hide');
                    $('#tabla_cliente').DataTable().ajax.reload();
                    $('#formEditarCliente')[0].reset();
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

// FORM CREAR
$('#formCrearCliente').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('funcion', 'CrearCliente');

    $.ajax({
        url: '/cliente/insertar',
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
                    $('#crearClienteModal').modal('hide');
                    $('#tabla_cliente').DataTable().ajax.reload();
                    $('#formCrearCliente')[0].reset();
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