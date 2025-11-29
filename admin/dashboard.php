<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ver_dashboard')) {
    die("Acceso denegado.");
}

// 2. Lógica de Tarjetas de Resumen
// Total Eventos
$total_eventos = $pdo->query("SELECT COUNT(*) FROM eventos")->fetchColumn();

// Total Registros (Asistentes) - Asumimos que "Asistente" es un rol
$stmt_rol_asistente = $pdo->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'Asistente' OR nombre_rol = 'Participante' LIMIT 1");
$stmt_rol_asistente->execute();
$id_rol_asistente = $stmt_rol_asistente->fetchColumn();
$total_asistentes = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_rol = ?");
$total_asistentes->execute([$id_rol_asistente]);
$total_registros = $total_asistentes->fetchColumn();

// Tickets Vendidos
$total_tickets = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();

// Ingresos Totales (de pagos completados)
$total_ingresos = $pdo->query("SELECT SUM(monto) FROM pagos WHERE estado = 'Completado'")->fetchColumn();
$total_ingresos = $total_ingresos ?? 0; // Si es null, poner 0

// 3. Lógica para Gráficos (Datos de los últimos 30 días)
// Esta consulta agrupa registros por día
$stmt_registros = $pdo->query("
    SELECT DATE(fecha_creacion) as dia, COUNT(*) as cantidad
    FROM usuarios
    WHERE fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY dia
    ORDER BY dia ASC
");
$registros_chart_data = $stmt_registros->fetchAll();

// Preparar datos para JavaScript
$chart_labels = [];
$chart_data = [];
foreach ($registros_chart_data as $data) {
    $chart_labels[] = $data['dia'];
    $chart_data[] = $data['cantidad'];
}
// Convertir a JSON para que JavaScript pueda leerlo
$json_chart_labels = json_encode($chart_labels);
$json_chart_data = json_encode($chart_data);

// 4. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Panel de Control</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Eventos</h4>
            <h2><?php echo $total_eventos; ?></h2>
            <span class="icon">📅</span>
        </div>
        <div class="stat-card">
            <h4>Registros</h4>
            <h2><?php echo $total_registros; ?></h2>
            <span class="icon">👥</span>
        </div>
        <div class="stat-card">
            <h4>Tickets Vendidos</h4>
            <h2><?php echo $total_tickets; ?></h2>
            <span class="icon">🎟️</span>
        </div>
        <div class="stat-card">
            <h4>Ingresos</h4>
            <h2>$<?php echo number_format($total_ingresos, 2); ?></h2>
            <span class="icon">💰</span>
        </div>
    </div>

    <div class="charts-grid">
        <div class="card">
            <div class="card-body">
                <h3>Estadísticas de Registros (Últimos 30 días)</h3>

                <div class="chart-container">
                    <canvas id="dashboardChart"></canvas>
                </div>

            </div>
        </div>

    </div>
</main>

<script>
    // Pasamos las variables de PHP a variables de JavaScript
    const chartLabels = <?php echo $json_chart_labels; ?>;
    const chartData = <?php echo $json_chart_data; ?>;
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>