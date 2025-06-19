let calendar;
document.addEventListener('DOMContentLoaded', () => { 

    //Inicializar los modales para crear y editar citas
    const crearCitaModal = new bootstrap.Modal(document.getElementById('crearCitaModal'));
    const editarCitaModal = new bootstrap.Modal(document.getElementById('editarCitaModal'));

    // Obtiene el contenedor del calendario
    const calendarEl = document.getElementById('calendario');

    calendar = new FullCalendar.Calendar(calendarEl, {
        selectable: true, 
        editable: true,   
        droppable: true,  
        dayMaxEvents: true, 
        height: 800, 
        contentHeight: 780,
        aspectRatio: 3,
        nowIndicator: true, 
        locale: 'es', 
        initialView: 'dayGridMonth', 
        initialDate: new Date(), 
        events: '/citas/obtenerEvento', 

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },

        views: {
            dayGridMonth: { buttonText: 'Mes' },
            timeGridWeek: { buttonText: 'Semana' }
        },

        //  Evento cuando comienza arrastrarse una cita
        eventDragStart(info) {
            zonaEliminar.classList.add('bg-danger', 'text-white');
            zonaEliminar.innerHTML = '<i class=" fas fa-trash-restore-alt"></i>';
        },

        // Evento para eliminar cuando se suelta un cita dentro del area
        eventDragStop(info) {
            const evento = info.event;
            const zonaRect = zonaEliminar.getBoundingClientRect();
            const { clientX: x, clientY: y } = info.jsEvent;
            
            // Restablece la zona de eliminación a su estado original
            zonaEliminar.classList.remove('bg-danger', 'text-white');
            zonaEliminar.innerHTML = '<i class="fas fa-trash"></i>';

            // Verifica si el evento fue soltado dentro de la zona de eliminación
            const estaEnZona = (
                x >= zonaRect.left &&
                x <= zonaRect.right &&
                y >= zonaRect.top &&
                y <= zonaRect.bottom
            );

            // Si se soltó en la zona de eliminación, Inicia el sweetAlert pide confirmación y lo elimina
            if (estaEnZona) {
                Swal.fire({
                    title: '¿Estás seguro de eliminar esta cita?',
                    text: 'No podrás revertir esta acción.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) {
                        const id_cita = evento.extendedProps.id_ct;

                        // Realiza la petición para eliminar la cita
                        fetch(`/citas/eliminar/${id_cita}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({}) 
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Error al eliminar la cita');
                            return res.json();
                        })
                        .then(data => {
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

                            evento.remove(); // Elimina visualmente el evento del calendario
                        })
                        .catch(err => {
                            Swal.fire('Error', err.message, 'error');
                        });
                    }
                });
            }
        },

        // Al hacer clic en un día del calendario muestra el modal para crear una nueve cita
        dateClick: info => {
            document.getElementById('fecha_cita').value = info.dateStr;
            crearCitaModal.show();
        },

        // Al mover un evento a otra fecha u hora
        eventDrop: info => {
            Swal.fire({
                title: '¿Estás seguro de cambiar la fecha u hora?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (!result.isConfirmed) {
                    info.revert(); // Revierte el cambio si se cancela
                    return;
                }

                const evento = info.event;
                const id = evento.extendedProps.id_ct;
                const startDate = new Date(evento.start);
                const endDate = evento.end ? new Date(evento.end) : null;

                // Formatea las fechas y horas
                const fecha_cita = startDate.toISOString().split('T')[0];
                const hora_inicio = startDate.toTimeString().slice(0, 5);
                const hora_fin = endDate ? endDate.toTimeString().slice(0, 5) : null;

                // Envía la actualización al servidor
                fetch(`/citas/actualizarfecha/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ fecha_cita, hora_inicio, hora_fin })
                })
                .then(async response => {
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Error inesperado');
                    }
                    return response.json();
                })
                .then(data => {
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
                })
                .catch(error => {
                    Swal.fire('Error', `No se pudo actualizar:\n${error.message}`, 'error');
                    info.revert(); // Revierte los cambios visuales si ocurre un error
                });
            });
        },

        // Al hacer clic sobre un evento existente
        eventClick: info => {
            const event = info.event;
            const startDate = new Date(event.start);
            const endDate = event.end ? new Date(event.end) : null;

            // Formateo las fechas y horas
            const pad = num => num.toString().padStart(2, '0');
            const hora_inicio = `${pad(startDate.getHours())}:${pad(startDate.getMinutes())}`;
            const hora_fin = endDate ? `${pad(endDate.getHours())}:${pad(endDate.getMinutes())}` : '';
            const fecha_inicio = startDate.toISOString().split('T')[0];

            // Obtiene los datos de la cita y los coloca en el formulario de edición
            const idCita = event.extendedProps.id_ct;
            document.getElementById('editar_id').value = idCita;
            document.getElementById('editar_usuario').value = event.extendedProps.id_usr;
            document.getElementById('editar_cliente').value = event.extendedProps.id_cli;
            document.getElementById('editar_motivo').value = event.title;
            document.getElementById('editar_fecha_cita').value = fecha_inicio;
            document.getElementById('editar_hora_inicio').value = hora_inicio;
            document.getElementById('editar_hora_fin').value = hora_fin;
            editarCitaModal.show();
        }
    });

    // Renderiza el calendario en pantalla
    calendar.render();
    cargarSelect('/cliente/obtener', '#select_cliente, #editar_cliente', 'Seleccione un Cliente', 'id_cliente', 'nombre_cliente');
    cargarSelect('/usuarios/obtener', '#select_usuario, #editar_usuario', 'Seleccione un Usuario', 'id_usuario', 'nombre_usuario');

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

$('#formEditarCita').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: `/citas/actualizar/${$('#editar_id').val()}`,
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
                    $('#editarCitaModal').modal('hide');
                    $('#formEditarCita')[0].reset();
                    calendar.refetchEvents();
                } else {
                    Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
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
$('#formCrearCita').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('funcion', 'crearCita');

    $.ajax({
        url: '/citas/insertar',
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
                    $('#crearCitaModal').modal('hide');
                    $('#formCrearCita')[0].reset();
                    calendar.refetchEvents();
                } else {
                    Swal.fire({
                    icon: 'error',
                    title: data.title,
                    text: data.message,
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
