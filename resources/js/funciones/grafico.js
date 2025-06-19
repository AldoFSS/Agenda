document.addEventListener('DOMContentLoaded', () => {

    const formulario = document.getElementById('formulario-grafica');

    // Agrega un listener para cuando se envíe el formulario
    formulario.addEventListener('submit', function (e) {
        // Evita que el formulario se recargue la página
        e.preventDefault();

        // Obtiene los valores de los campos del formulario
        const fecha_inicio = formulario.querySelector('[name="fecha_inicio"]').value;
        const fecha_final = formulario.querySelector('[name="fecha_final"]').value;
        const tipo = formulario.querySelector('[name="tipo"]').value;

        // Verifica si ambas fechas fueron seleccionadas; si no, muestra una alerta
        if (!fecha_inicio || !fecha_final) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Error',
                text: 'Por favor, selecciona ambas fechas',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            return;
        }else{
             document.getElementById('grafica-placeholder').style.display = 'none';
        }

        // Realiza una solicitud al servidor para obtener los datos de la gráfica
        $.ajax({
            method: "GET",
            url: `/grafico/mostrar/${tipo},${fecha_inicio},${fecha_final}`, 
            dataType: "json", 

            // Si la petición es exitosa, se ejecuta esta función
            success: function (response) {
                let labels = [];        
                let valores = [];       
                let datasetLabel = '';  

                if(!response.data || response.data.length === 0){
                    window.miGrafica.destroy();
                    document.getElementById('grafica-placeholder').style.display = 'block';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Sin resultados',
                        text: 'No se encontraron datos para el rango de fechas seleccionado.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });  
                    return; 
                }

                // Dependiendo del tipo de grafico seleccionado, se organizan los datos correspondientes
                switch (tipo) {
                    case 'ventas':
                        response.data.forEach(item => {
                            labels.push(item.fecha);              
                            valores.push(item.total_vendidos);    
                        });
                        datasetLabel = 'Ventas Totales';
                        break;
                    case 'productos':
                        response.data.forEach(item => {
                            labels.push(item.nombre_producto);    
                            valores.push(item.cantidad_total);    
                        });
                        datasetLabel = 'Cantidad por Producto';
                        break;
                    case 'usuarios':
                        response.data.forEach(item => {
                            labels.push(item.nombre_usuario);     
                            valores.push(item.Total);            
                        });
                        datasetLabel = 'Total por Usuario';
                        break;
                    case 'clientes':
                        response.data.forEach(item => {
                            labels.push(item.nombre_cliente);     
                            valores.push(item.Total);            
                        });
                        datasetLabel = 'Total por Cliente';
                        break;
                }

                const ctx = document.getElementById('miGrafica').getContext('2d');

                // Si ya existe una gráfica anterior, la destruye 
                if (window.miGrafica && typeof window.miGrafica.destroy === 'function') {
                    window.miGrafica.destroy();
                }

                // Crea una nueva gráfica con Chart.js usando los datos obtenidos
                window.miGrafica = new Chart(ctx, {
                    type: 'bar', 
                    data: {
                        labels: labels,
                        datasets: [{
                            label: datasetLabel,
                            data: valores,
                            backgroundColor: 'rgba(235, 166, 54, 0.6)', 
                            borderColor: 'rgb(235, 99, 54)',       
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true, 
                        scales: {
                            y: {
                                beginAtZero: true 
                            }
                        }
                    }
                });
            },

            // Si ocurre un error durante la petición AJAX, muestra un alert
            error: function (xhr, status, error) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error',
                    text: "Error en la petición AJAX:",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        });
    });
});
