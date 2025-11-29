<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ver_pagos')) {
    die("Acceso denegado.");
}

// 2. Lógica: Obtener todos los pagos con sus datos relacionados
$stmt = $pdo->query("
    SELECT 
        p.id_pago, p.monto, p.metodo, p.estado, p.fecha_pago,
        t.numero_ticket,
        e.nombre as evento_nombre,
        u.nombre_completo as asistente_nombre
    FROM pagos p
    LEFT JOIN tickets t ON p.id_ticket = t.id_ticket
    LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
    LEFT JOIN eventos e ON t.id_evento = e.id_evento
    ORDER BY p.fecha_pago DESC
");
$pagos = $stmt->fetchAll();

// 3. Lógica para las tarjetas de resumen (como en la imagen)
$total_ingresos = $pdo->query("SELECT SUM(monto) FROM pagos WHERE estado = 'Completado'")->fetchColumn() ?? 0;
$pagos_pendientes = $pdo->query("SELECT SUM(monto) FROM pagos WHERE estado = 'Pendiente'")->fetchColumn() ?? 0;
$reembolsos = $pdo->query("SELECT SUM(monto) FROM pagos WHERE estado = 'Reembolsado'")->fetchColumn() ?? 0;
$total_pagos = $pdo->query("SELECT COUNT(*) FROM pagos")->fetchColumn();


// 4. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Gestión de Pagos</h1>
        <div>
            <?php if (hasPermission('crear_pagos')): // Asumimos un permiso 'crear_pagos' ?>
                <a href="pago_gestionar.php" class="btn btn-primary">+ Nuevo Pago</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
        <div class="stat-card">
            <h4>Total de Pagos</h4>
            <h2><?php echo $total_pagos; ?></h2>
        </div>
        <div class="stat-card">
            <h4>Ingresos Totales</h4>
            <h2>$<?php echo number_format($total_ingresos, 2); ?></h2>
        </div>
        <div class="stat-card">
            <h4>Pagos Pendientes</h4>
            <h2>$<?php echo number_format($pagos_pendientes, 2); ?></h2>
        </div>
        <div class="stat-card">
            <h4>Reembolsos</h4>
            <h2>$<?php echo number_format($reembolsos, 2); ?></h2>
        </div>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'created'): ?>
            <div class="alert alert-success">Pago creado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'updated'): ?>
            <div class="alert alert-success">Pago actualizado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success">Pago eliminado exitosamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Evento</th>
                        <th>Asistente</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td><?php echo $pago['id_pago']; ?></td>
                            <td><?php echo htmlspecialchars($pago['evento_nombre'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($pago['asistente_nombre'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($pago['metodo']); ?></td>
                            <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                            <td>
                                <span class="tag tag-<?php echo strtolower($pago['estado']); ?>">
                                    <?php echo htmlspecialchars($pago['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?></td>
                            <td>
                                <?php if (hasPermission('editar_pagos')): ?>
                                    <a href="pago_gestionar.php?id=<?php echo $pago['id_pago']; ?>" class="btn btn-icon btn-edit" title="Editar Pago/Estado">✏️</a>
                                <?php endif; ?>
                                <?php if (hasPermission('eliminar_pagos')): // Asumimos permiso 'eliminar_pagos' ?>
                                    <a href="pago_eliminar.php?id=<?php echo $pago['id_pago']; ?>" class="btn btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este pago?');">🗑️</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<style>
    .tag-completado { background-color: var(--success-bg); color: var(--success-green); }
    .tag-pendiente { background-color: var(--warning-bg); color: var(--warning-orange); }
    .tag-reembolsado { background-color: var(--pending-bg); color: var(--pending-gray); }
</style>

<?php include '../includes/footer.php'; ?>