$(document).ready(function () {
  login();
});

function login() {
  $('#formLogin').on('submit', function (e) {
    e.preventDefault();

    const username = $("#usuario").val();
    const password = $("#pass").val();

    $.ajax({
      url: '/usuarios/login', // Laravel
      type: 'POST',
      data: {
        nombre_usuario: username,
        contraseña: password
      },
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
            window.location.href = '/home'; // o como lo manejes
          });

        } else {
          Swal.fire({
            icon: 'error',
            title: response.title || 'Error',
            text: response.message || 'Credenciales incorrectas.'
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo conectar al servidor.'
        });
      }
    });
  });
}
