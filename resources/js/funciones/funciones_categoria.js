
$(document).ready(function () {
    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    cargarTabla();
});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarCategoria(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarCategoria(id);
});
$(document).on('click', '.btn-editarCategoria', function () {
    const id = $(this).data('id');

    $.ajax({
        url: `/categoria/buscar/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                $('#editar_id').val(res.id_categoria);
                $('#editar_nombre').val(res.nombre_categoria);
                $('#editar_descripcion').val(res.descripcion);
            }
        },
        error: function () {
            alert('Error al obtener los datos de la categoría');
        }
    });
});

$('#formEditarCategoria').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: `/categoria/actualizar/${$('#editar_id').val()}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            try {
                const res = typeof response === 'object' ? response : JSON.parse(response);
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
                    $('#editarCategoriaModal').modal('hide');
                    $('#tabla_categoria').DataTable().ajax.reload();
                    $('#formCrearCategoria')[0].reset();
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

// -------------------------
// CREAR CATEGORÍA
// -------------------------

$('#formCrearCategoria').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('funcion', 'crearCategoria');

    $.ajax({
        url: '/categoria/insertar',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            try {
                const res = typeof response === 'object' ? response : JSON.parse(response);
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
                    $('#crearCategoriaModal').modal('hide');
                    $('#tabla_categoria').DataTable().ajax.reload();
                    $('#formCrearCategoria')[0].reset();
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
function eliminarCategoria(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar la Categoria`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/categoria/eliminar/${id}`, {
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
          $('#tabla_categoria').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar la Categoria`, "error"));
    }
  });
}
function restaurarCategoria(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar la Categoria`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/categoria/restaurar/${id}`, {
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
          $('#tabla_categoria').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar la Categoria`, "error"));
    }
  });
}
    // Función para cargar la tabla con DataTables
    function cargarTabla(estatus = 'All') {
        $('#tabla_categoria').DataTable({
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
                url: `/categoria/tabla/${estatus}`,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id_categoria' },
                {
                    data: 'imagen_categoria',
                    render: function(data) {
                        if (data) {
                            return `<img src="${data}" width="50" height="50" alt="Imagen" style="object-fit: cover; border-radius: 5px;">`;
                        } else {
                            return `<img src="imgcategoria/user_default.png" width="50" height="50" alt="Sin imagen">`; // o deja un texto si no hay imagen
                        }
                    }
                },
                { data: 'nombre_categoria' },
                { data: 'descripcion' },
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
                                <a title="Editar" href="#" class="btn btn-primary btn-editarCategoria"
                                    data-id="${row.id_categoria}" data-bs-toggle="modal" data-bs-target="#editarCategoriaModal">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </li>`;

                        let botonAccion = '';

                        if (row.estatus == 1) {
                            botonAccion = `
                                <li>
                                    <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                        data-tipo="usuario" data-id="${row.id_categoria}">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </li>`;
                        } else if (row.estatus == 0) {
                            botonAccion = `
                                <li>
                                    <a title="Restaurar" class="btn btn-success btn-restaurar"
                                        data-tipo="usuario" data-id="${row.id_categoria}">
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