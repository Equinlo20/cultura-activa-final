// Espera a que todo el documento HTML esté cargado
document.addEventListener("DOMContentLoaded", function () {

    // --- GRÁFICO DEL DASHBOARD ---

    // Busca el <canvas> con id 'dashboardChart'
    const dashboardCanvas = document.getElementById('dashboardChart');

    // Comprueba si el canvas existe en la página actual Y
    // si las variables 'chartLabels' y 'chartData' (de PHP) existen
    if (dashboardCanvas && typeof chartLabels !== 'undefined' && typeof chartData !== 'undefined') {

        const ctx = dashboardCanvas.getContext('2d');

        new Chart(ctx, {
            type: 'line', // Tipo de gráfico (línea)
            data: {
                labels: chartLabels, // Las fechas (eje X)
                datasets: [{
                    label: 'Nuevos Registros',
                    data: chartData, // La cantidad (eje Y)
                    borderColor: 'rgb(52, 152, 219)',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ... código del gráfico del dashboard (dashboardCanvas) ...

    // --- GRÁFICOS DE LA PÁGINA DE REPORTES ---

    // Gráfico 1: Ventas por Tipo (Donut)
    const chartVentasPorTipoCanvas = document.getElementById('chartVentasPorTipo');
    if (chartVentasPorTipoCanvas) {
        new Chart(chartVentasPorTipoCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: chartVentasTipoLabels, // de PHP
                datasets: [{
                    label: 'Ventas',
                    data: chartVentasTipoData, // de PHP
                    backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Gráfico 2: Ventas (Últimos 30 días) (Línea)
    const chartVentas30DiasCanvas = document.getElementById('chartVentas30Dias');
    if (chartVentas30DiasCanvas) {
        new Chart(chartVentas30DiasCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartVentas30dLabels, // de PHP
                datasets: [{
                    label: 'Tickets Vendidos',
                    data: chartVentas30dData, // de PHP
                    borderColor: 'rgb(52, 152, 219)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // Gráfico 3: Tasa de Asistencia (Dona)
    const chartAsistenciaCanvas = document.getElementById('chartAsistencia');
    if (chartAsistenciaCanvas) {
        new Chart(chartAsistenciaCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Asistieron', 'No Asistieron'],
                datasets: [{
                    data: chartAsistenciaData, // de PHP
                    backgroundColor: ['#2ecc71', '#f0f2f2'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    }

    // Gráfico 4: Ingresos por Tipo (Barra)
    const chartIngresosPorTipoCanvas = document.getElementById('chartIngresosPorTipo');
    if (chartIngresosPorTipoCanvas) {
        new Chart(chartIngresosPorTipoCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: chartIngresosTipoLabels, // de PHP
                datasets: [{
                    label: 'Ingresos ($)',
                    data: chartIngresosTipoData, // de PHP
                    backgroundColor: '#2ecc71',
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y', // Hace el gráfico de barras horizontal
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Gráfico 5: Ingresos (Últimos 30 días) (Línea)
    const chartIngresos30DiasCanvas = document.getElementById('chartIngresos30Dias');
    if (chartIngresos30DiasCanvas) {
        new Chart(chartIngresos30DiasCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartIngresos30dLabels, // de PHP
                datasets: [{
                    label: 'Ingresos ($)',
                    data: chartIngresos30dData, // de PHP
                    borderColor: '#2ecc71',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

});