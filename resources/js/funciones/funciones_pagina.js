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
if(window.errorUsuario){
   Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Usuario o contraseña incorrectos',
        confirmButtonText: 'Aceptar'
    });
}