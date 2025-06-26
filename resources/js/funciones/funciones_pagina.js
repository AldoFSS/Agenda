$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
document.addEventListener("DOMContentLoaded", function () {
  // Botones para expandir/colapsar el sidebar
  const toggleButtons = document.querySelectorAll(".toggle-btn");
  toggleButtons.forEach(button => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      document.getElementById("sidebar")?.classList.toggle("expand");
    });
  });
});
$(document).ready(function () {
  obtenerNombreUsuario();
});
$(document).ready(function () {
  $('#btnLogout').on('click', function (e) {
    e.preventDefault();

    $.ajax({
      url: '/usuarios/logout',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: response.title,
            text: response.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.href = '/';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cerrar la sesión.'
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo contactar al servidor.'
        });
      }
    });
  });
});


function obtenerNombreUsuario() {
  $.ajax({
    url: '/usuarios/usuario/actual',
    type: 'GET',
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        document.getElementById('nombreUsuario').textContent = response.usuario;
      } else {
        document.getElementById('nombreUsuario').textContent = 'Invitado';
      }
    },
    error: function () {
      document.getElementById('nombreUsuario').textContent = 'Error';
    }
  });
}