<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ver_estadisticas')) {
    die("Acceso denegado.");
}

// 2. Lógica de Filtro
// Comprobar si se está filtrando por un evento específico
$id_evento_filtrado = $_GET['id_evento'] ?? null;
$filtro_sql_evento = '';
$params = [];

if ($id_evento_filtrado && $id_evento_filtrado != 'todos') {
    // Si filtramos, añadimos un WHERE a todas las consultas
    $filtro_sql_evento = 'WHERE e.id_evento = ?';
    $params = [$id_evento_filtrado];
} else {
    // Si no, la cláusula WHERE está vacía
    $filtro_sql_evento = '';
}

// 3. Cargar datos para el filtro (lista de todos los eventos)
$stmt_eventos = $pdo->query("SELECT id_evento, nombre FROM eventos ORDER BY nombre");
$todos_los_eventos = $stmt_eventos->fetchAll();


// 4. Lógica de Consultas SQL (respetando el filtro)

// --- SECCIÓN VENTAS DE TICKETS ---

// 4a. Ventas por Tipo de Ticket (Donut Chart)
$sql_ventas_tipo = "
    SELECT tt.nombre, COUNT(t.id_ticket) as cantidad
    FROM tickets t
    JOIN tipos_ticket tt ON t.id_tipo_ticket = tt.id_tipo_ticket
    " . (empty($filtro_sql_evento) ? '' : 'JOIN eventos e ON t.id_evento = e.id_evento') . "
    $filtro_sql_evento
    GROUP BY tt.nombre
";
$stmt_ventas_tipo = $pdo->prepare($sql_ventas_tipo);
$stmt_ventas_tipo->execute($params);
$ventas_por_tipo = $stmt_ventas_tipo->fetchAll();

$chart_ventas_tipo_labels = json_encode(array_column($ventas_por_tipo, 'nombre'));
$chart_ventas_tipo_data = json_encode(array_column($ventas_por_tipo, 'cantidad'));

// 4b. Ventas de Tickets (Últimos 30 días) (Line Chart)
$sql_ventas_30d = "
    SELECT DATE(t.fecha_compra) as dia, COUNT(t.id_ticket) as cantidad
    FROM tickets t
    " . (empty($filtro_sql_evento) ? '' : 'JOIN eventos e ON t.id_evento = e.id_evento') . "
    $filtro_sql_evento
    " . (empty($filtro_sql_evento) ? 'WHERE' : 'AND') . " t.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY dia
    ORDER BY dia ASC
";
$stmt_ventas_30d = $pdo->prepare($sql_ventas_30d);
$stmt_ventas_30d->execute($params);
$ventas_30d = $stmt_ventas_30d->fetchAll();

$chart_ventas_30d_labels = json_encode(array_column($ventas_30d, 'dia'));
$chart_ventas_30d_data = json_encode(array_column($ventas_30d, 'cantidad'));


// --- SECCIÓN INGRESOS ---

// 4c. Ingresos Totales (Tarjeta)
$sql_ingresos_total = "
    SELECT SUM(p.monto) as total
    FROM pagos p
    JOIN tickets t ON p.id_ticket = t.id_ticket
    " . (empty($filtro_sql_evento) ? '' : 'JOIN eventos e ON t.id_evento = e.id_evento') . "
    $filtro_sql_evento
    " . (empty($filtro_sql_evento) ? 'WHERE' : 'AND') . " p.estado = 'Completado'
";
$stmt_ingresos_total = $pdo->prepare($sql_ingresos_total);
$stmt_ingresos_total->execute($params);
$ingresos_totales = $stmt_ingresos_total->fetchColumn() ?? 0;

// 4d. Ingresos por Tipo de Ticket (Bar Chart)
$sql_ingresos_tipo = "
    SELECT tt.nombre, SUM(p.monto) as total
    FROM pagos p
    JOIN tickets t ON p.id_ticket = t.id_ticket
    JOIN tipos_ticket tt ON t.id_tipo_ticket = tt.id_tipo_ticket
    " . (empty($filtro_sql_evento) ? '' : 'JOIN eventos e ON t.id_evento = e.id_evento') . "
    $filtro_sql_evento
    " . (empty($filtro_sql_evento) ? 'WHERE' : 'AND') . " p.estado = 'Completado'
    GROUP BY tt.nombre
";
$stmt_ingresos_tipo = $pdo->prepare($sql_ingresos_tipo);
$stmt_ingresos_tipo->execute($params);
$ingresos_por_tipo = $stmt_ingresos_tipo->fetchAll();

$chart_ingresos_tipo_labels = json_encode(array_column($ingresos_por_tipo, 'nombre'));
$chart_ingresos_tipo_data = json_encode(array_column($ingresos_por_tipo, 'total'));

// 4e. Ingresos por Día (Últimos 30 días) (Line Chart)
$sql_ingresos_30d = "
    SELECT DATE(p.fecha_pago) as dia, SUM(p.monto) as total
    FROM pagos p
    JOIN tickets t ON p.id_ticket = t.id_ticket
    " . (empty($filtro_sql_evento) ? '' : 'JOIN eventos e ON t.id_evento = e.id_evento') . "
    $filtro_sql_evento
    " . (empty($filtro_sql_evento) ? 'WHERE' : 'AND') . " p.estado = 'Completado'
    AND p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY dia
    ORDER BY dia ASC
";
$stmt_ingresos_30d = $pdo->prepare($sql_ingresos_30d);
$stmt_ingresos_30d->execute($params);
$ingresos_30d = $stmt_ingresos_30d->fetchAll();

$chart_ingresos_30d_labels = json_encode(array_column($ingresos_30d, 'dia'));
$chart_ingresos_30d_data = json_encode(array_column($ingresos_30d, 'total'));

// --- SECCIÓN ASISTENCIA Y REGISTROS ---

// 4f. Tasa de Asistencia (Donut Chart)
// (Tickets escaneados vs. No escaneados para el evento)
$sql_asistencia = "
    SELECT 
        SUM(CASE WHEN t.escaneado = 1 THEN 1 ELSE 0 END) as asistieron,
        SUM(CASE WHEN t.escaneado = 0 THEN 1 ELSE 0 END) as no_asistieron
    FROM tickets t
    " . (empty($filtro_sql_evento) ? '' : 'JOIN eventos e ON t.id_evento = e.id_evento') . "
    $filtro_sql_evento
";
$stmt_asistencia = $pdo->prepare($sql_asistencia);
$stmt_asistencia->execute($params);
$asistencia = $stmt_asistencia->fetch();

$chart_asistencia_data = json_encode([
    $asistencia['asistieron'] ?? 0,
    $asistencia['no_asistieron'] ?? 0
]);

// 5. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Análisis y Reportes</h1>
        
        <form action="reportes.php" method="GET" class="report-filter">
            <label for="id_evento">Filtrar por Evento:</label>
            <select name="id_evento" id="id_evento" onchange="this.form.submit()">
                <option value="todos">Todos los Eventos</option>
                <?php foreach ($todos_los_eventos as $evento): ?>
                    <option value="<?php echo $evento['id_evento']; ?>" <?php if ($id_evento_filtrado == $evento['id_evento']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($evento['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="report-row">
        <div class="report-col-large">
            <div class="card">
                <div class="card-body">
                    <h3>Ventas de Tickets</h3>
                    <div class="charts-grid" style="grid-template-columns: 1fr 2fr; align-items: center;">
                        <div>
                            <h4>Ventas por Tipo</h4>
                            <canvas id="chartVentasPorTipo" height="200"></canvas>
                        </div>
                        <div>
                            <h4>Ventas (Últimos 30 días)</h4>
                            <canvas id="chartVentas30Dias" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="report-col-small">
            <div class="card">
                <div class="card-body">
                    <h3>Asistencia</h3>
                    <p style="text-align: center; margin:0;">
                        <?php 
                        $total_asistencia = ($asistencia['asistieron'] ?? 0) + ($asistencia['no_asistieron'] ?? 0);
                        $tasa = ($total_asistencia > 0) ? ($asistencia['asistieron'] / $total_asistencia) * 100 : 0;
                        echo '<strong>' . round($tasa, 2) . '%</strong><br>';
                        echo '<small>(' . ($asistencia['asistieron'] ?? 0) . ' de ' . $total_asistencia . ')</small>';
                        ?>
                    </p>
                    <canvas id="chartAsistencia" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-row">
        <div class="report-col-full">
            <div class="card">
                <div class="card-body">
                    <h3>Ingresos (Total: $<?php echo number_format($ingresos_totales, 2); ?>)</h3>
                    <div class="charts-grid" style="grid-template-columns: 1fr 2fr; align-items: center;">
                        <div>
                            <h4>Ingresos por Tipo</h4>
                            <canvas id="chartIngresosPorTipo" height="250"></canvas>
                        </div>
                        <div>
                            <h4>Ingresos (Últimos 30 días)</h4>
                            <canvas id="chartIngresos30Dias" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // --- DATOS DE VENTAS ---
    const chartVentasTipoLabels = <?php echo $chart_ventas_tipo_labels; ?>;
    const chartVentasTipoData = <?php echo $chart_ventas_tipo_data; ?>;
    
    const chartVentas30dLabels = <?php echo $chart_ventas_30d_labels; ?>;
    const chartVentas30dData = <?php echo $chart_ventas_30d_data; ?>;

    // --- DATOS DE ASISTENCIA ---
    const chartAsistenciaData = <?php echo $chart_asistencia_data; ?>;

    // --- DATOS DE INGRESOS ---
    const chartIngresosTipoLabels = <?php echo $chart_ingresos_tipo_labels; ?>;
    const chartIngresosTipoData = <?php echo $chart_ingresos_tipo_data; ?>;

    const chartIngresos30dLabels = <?php echo $chart_ingresos_30d_labels; ?>;
    const chartIngresos30dData = <?php echo $chart_ingresos_30d_data; ?>;
</script>

<style>
    .report-filter {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .report-filter select {
        padding: 8px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }
    .report-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    .report-col-large { flex: 2.5; }
    .report-col-small { flex: 1; }
    .report-col-full { flex: 1; }
</style>

<?php include '../includes/footer.php'; ?>