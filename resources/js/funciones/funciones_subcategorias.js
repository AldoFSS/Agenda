$(document).ready(function () {

    cargarSelect('/categoria/obtener', '#select_categoria, #editar_categoria', 'Seleccione una Categoria', 'id_categoria', 'nombre_categoria');

    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    
    cargarTabla();
});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarSubcategoria(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarSubcategoria(id);
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
    $('#tabla_subcategoria').DataTable({
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
            url: `/subcategoria/tabla/${estatus}`,
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_subcategoria' },
            {
                data: 'imagen_subcategoria',
                render: function(data) {
                    if (data) {
                        return `<img src="${data}" width="50" height="50" alt="Imagen" style="object-fit: cover; border-radius: 5px;">`;
                    } else {
                        return `<img src="imgsubcategoria/user_default.png" width="50" height="50" alt="Sin imagen">`;
                    }
                }
            },
            { data: 'nombre_subcategoria' },
            { data: 'Categoria' },
            { data: 'descripcion_subcategoria' },
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
                            <a title="Editar" href="#" class="btn btn-primary btn-editarSubcategoria"
                                data-id="${row.id_subcategoria}" data-bs-toggle="modal" data-bs-target="#editarSubcategoriaModal">
                                <i class="fas fa-edit"></i>
                            </a>
                        </li>`;
                    let botonAccion = '';
                    if (row.estatus == 1) {
                        botonAccion = `
                            <li>
                                <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                     data-id="${row.id_subcategoria}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>`;
                    } else if (row.estatus == 0) {
                        botonAccion = `
                            <li>
                                <a title="Restaurar" class="btn btn-success btn-restaurar" data-id="${row.id_subcategoria}">
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
$(document).on('click', '.btn-editarSubcategoria', function () {
    var id = $(this).data('id');
    $.ajax({
        url: `/subcategoria/buscar/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                $('#editar_id').val(res.id_subcategoria);
                $('#editar_nombre').val(res.nombre_subcategoria);
                $('#editar_descripcion').val(res.descripcion_subcategoria);
                $('#editar_categoria').val(res.id_ctg);
                $('#editarSubcategoriaModal').modal('show');
            }
        },
        error: function () {
            alert('Error al obtener los datos de la subcategoria');
        }
    });
});
$('#formEditarSubcategoria').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'EditarSubcategoria');
    $.ajax({
        url: `/subcategoria/actualizar/${$('#editar_id').val()}`,
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
                    $('#editarSubcategoriaModal').modal('hide');
                    $('#tabla_subcategoria').DataTable().ajax.reload();
                    $('#formEditarSubcategoria')[0].reset();
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
$('#formCrearSubcategoria').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'CrearSubcategoria');
    $.ajax({
        url: '/subcategoria/insertar',
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
                    $('#crearSubcategoriaModal').modal('hide');
                    $('#tabla_subcategoria').DataTable().ajax.reload();
                    $('#formCrearSubcategoria')[0].reset();
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
function eliminarSubcategoria(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar la Subcategoria`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/subcategoria/eliminar/${id}`, {
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
          $('#tabla_subcategoria').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar la Subcategoria`, "error"));
    }
  });
}
function restaurarSubcategoria(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar la Subcategoria`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/subcategoria/restaurar/${id}`, {
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
          $('#tabla_subcategoria').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar la Subcategoria`, "error"));
    }
  });
}

