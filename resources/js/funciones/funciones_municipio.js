$(document).ready(function () {

    cargarSelect('/estados/buscar', '#select_estado, #editar_estado', 'Seleccione un Estado', 'id_estado', 'nombre_estado');

    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    cargarTabla();
});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarMunicipio(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarMunicipio(id);
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
    $('#tabla_municipios').DataTable({
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
            url: `/municipios/tabla/${estatus}`,
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_municipio' },
            { data: 'municipio' },
            { data: 'Estado' },
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
                            <a title="Editar" href="#" class="btn btn-primary btn-editarMunicipio"
                                data-id="${row.id_municipio}" data-bs-toggle="modal" data-bs-target="#editarMunicipioModal">
                                <i class="fas fa-edit"></i>
                            </a>
                        </li>`;
                    let botonAccion = '';
                    if (row.estatus == 1) {
                        botonAccion = `
                            <li>
                                <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                    data-tipo="usuario" data-id="${row.id_municipio}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>`;
                    } else if (row.estatus == 0) {
                        botonAccion = `
                            <li>
                                <a title="Restaurar" class="btn btn-success btn-restaurar"
                                    data-tipo="usuario" data-id="${row.id_municipio}">
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
$(document).on('click', '.btn-editarMunicipio', function () {
    var id = $(this).data('id');
    $.ajax({
        url: `/municipios/obtener/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                $('#editar_id').val(res.id_municipio);
                $('#editar_municipio').val(res.municipio);
                $('#editar_estado').val(res.id_estd);
                $('#editarMunicipioModal').modal('show');
            }
        },
        error: function () {
            alert('Error al obtener los datos del municipio');
        }
    });
});
$('#formEditarMunicipio').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'EditarMunicipio');
    $.ajax({
        url: `/municipios/actualizar/${$('#editar_id').val()}`,
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
                    $('#editarMunicipioModal').modal('hide');
                    $('#tabla_municipios').DataTable().ajax.reload();
                    $('#formEditarMunicipio')[0].reset();
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
$('#formCrearMunicipio').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('funcion', 'CrearCita');
    $.ajax({
        url: '/municipios/insertar',
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
                    $('#crearMunicipioModal').modal('hide');
                    $('#tabla_municipios').DataTable().ajax.reload();
                    $('#formCrearMunicipio')[0].reset();
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
function eliminarMunicipio(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar el Municipio`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/municipios/eliminar/${id}`, {
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
          $('#tabla_municipios').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar el Municipio`, "error"));
    }
  });
}
function restaurarMunicipio(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar el Municipio`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/municipios/restaurar/${id}`, {
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
          $('#tabla_municipios').DataTable().ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar el Municipio`, "error"));
    }
  });
}