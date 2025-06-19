$(document).ready(function () {
    // Cargar tabla con filtro de estatus
    $('#select_estatus').on('change', function () {
        cargarTabla($(this).val());
    });
    cargarTabla();
});
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    eliminarEstado(id);
});
$(document).on('click', '.btn-restaurar', function () {
    const id = $(this).data('id');
    restaurarEstado(id);
});
function cargarTabla(estatus = 'All') {
    $('#tabla_estados').DataTable({
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
            url: `/estados/tabla/${estatus}`,
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_estado' },
            { data: 'nombre_estado' },
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
                    let botonAccion = '';
                    if (row.estatus == 1) {
                        botonAccion = `
                            <li>
                                <a title="Eliminar" class="btn btn-danger btn-eliminar"
                                    data-tipo="estado" data-id="${row.id_estado}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>`;
                    } else if (row.estatus == 0) {
                        botonAccion = `
                            <li>
                                <a title="Restaurar" class="btn btn-success btn-restaurar"
                                    data-tipo="estado" data-id="${row.id_estado}">
                                    <i class="fas fa-trash-restore"></i>
                                </a>
                            </li>`;
                    }
                    return `<ul class="opciones">${botonAccion}</ul>`;
                }
            }
        ]
    });
}
function eliminarEstado(id) {
  Swal.fire({
    title: `¿Estás seguro de desactivar el Estado`,
    text: "Podrás restaurarlo más tarde.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/estados/eliminar/${id}`, {
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
          $('#tabla_estados').DataTable().ajax.reload();
        } else {
          Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al desactivar el Estado`, "error"));
    }
  });
}
function restaurarEstado(id) {
  Swal.fire({
    title: `¿Estás seguro de restaurar el Estado`,
    text: "Volverá a estar disponible.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, restaurar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/estados/restaurar/${id}`, {
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
          $('#tabla_estados').DataTable().ajax.reload();
        } else {
          Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
            });
        }
      })
      .catch(() => Swal.fire("Error", `Error en la solicitud al restaurar el Estado`, "error"));
    }
  });
}