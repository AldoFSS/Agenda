// funciones_usuario.js
$(document).ready(function () {
    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    cargarTabla(); // Cargar al inicio
});
function cargarTabla(estatus = 'All') {        
    $('#tabla_usuarios').DataTable({
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
            url: `/usuarios/tabla/${estatus}`,
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_usuario' },
            {
                data: 'imagen',
                render: function(data) {
                    if (data) {
                        return `<img src="${data}" width="50" height="50" alt="Imagen" style="object-fit: cover; border-radius: 5px;">`;
                    } else {
                        return `<img src="imgusuario/user_default.png" width="50" height="50" alt="Sin imagen">`; // o deja un texto si no hay imagen
                    }
                }
            },
            { data: 'nombre_usuario' },
            { data: 'telefono' },
            { data: 'correo' },
            { data: 'rol' },
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
                render: function (data, type, row) 
                {
                    const botonEditar = `
                        <li>
                            <a title="Editar" href="#" class="btn btn-primary btn-editarUsuario"
                                data-id="${row.id_usuario}" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal">
                                <i class="fas fa-edit"></i>
                            </a>
                        </li>`;
                    let botonAccion = '';
                    if (row.estatus == 1) {
                        botonAccion = `
                            <li>
                                <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                    data-tipo="usuario" data-id="${row.id_usuario}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>`;
                    } else if (row.estatus == 0) {
                        botonAccion = `
                            <li>
                                <a title="Restaurar" class="btn btn-success btn-restaurar"
                                    data-tipo="usuario" data-id="${row.id_usuario}">
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
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarUsuario(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarUsuario(id);
});
// EDITAR USUARIO
$(document).on('click', '.btn-editarUsuario', function () {
    const id = $(this).data('id');
    $.ajax({
        url: `/usuarios/buscar/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                $('#editar_id').val(res.id_usuario);
                $('#editar_nombre').val(res.nombre_usuario);
                $('#editar_telefono').val(res.telefono);
                $('#editar_correo').val(res.correo);
                $('#editar_rol').val(res.rol);
                $('#editarUsuarioModal').modal('show');
            }
        },
        error: function () {
            alert('Error al obtener los datos del usuario');
        }
    });
});
$('#formEditarUsuario').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: `/usuarios/actualizar/${$('#editar_id').val()}`,
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
                    $('#editarUsuarioModal').modal('hide');
                    $('#tabla_usuarios').DataTable().ajax.reload();
                    $('#formEditarUsuario')[0].reset();
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

$('#formCrearUsuario').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('funcion', 'crearUsuario');

    $.ajax({
        url: '/usuarios/insertar',
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
                    $('#crearUsuarioModal').modal('hide');
                    $('#tabla_usuarios').DataTable().ajax.reload();
                    $('#formCrearUsuario')[0].reset();
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
function eliminarUsuario(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar el usuario`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/usuarios/eliminar/${id}`, {
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
          $('#tabla_usuarios').DataTable().ajax.reload();
        } else {
          Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar el usuario`, "error"));
    }
  });
}
function restaurarUsuario(id, tipo = 'registro') {
  Swal.fire({
    title: `¿Estás seguro de restaurar el registro`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/usuarios/restaurar/${id}`, {
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
           $('#tabla_usuarios').DataTable().ajax.reload();
        } else {
          Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar el registro`, "error"));
    }
  });
}
