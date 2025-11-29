<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ver_tickets')) {
    die("Acceso denegado.");
}

// 2. Lógica: Obtener todos los tickets con sus datos relacionados
$stmt = $pdo->query("
    SELECT 
        t.id_ticket, t.numero_ticket, t.estado, t.escaneado,
        e.nombre as evento_nombre,
        u.nombre_completo as asistente_nombre,
        tt.nombre as tipo_ticket_nombre
    FROM tickets t
    LEFT JOIN eventos e ON t.id_evento = e.id_evento
    LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
    LEFT JOIN tipos_ticket tt ON t.id_tipo_ticket = tt.id_tipo_ticket
    ORDER BY t.id_ticket DESC
");
$tickets = $stmt->fetchAll();

// 3. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Listado de Tickets</h1>
        <?php if (hasPermission('crear_tickets')): ?>
            <a href="ticket_gestionar.php" class="btn btn-primary">+ Vender Ticket Manual</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'created'): ?>
            <div class="alert alert-success">Ticket creado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'updated'): ?>
            <div class="alert alert-success">Ticket actualizado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success">Ticket eliminado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'error_fk'): ?>
            <div class="alert alert-danger">Error: No se puede eliminar el ticket porque tiene pagos o certificados asociados.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número de Ticket</th>
                        <th>Evento</th>
                        <th>Asistente</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Escaneado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo $ticket['id_ticket']; ?></td>
                            <td><?php echo htmlspecialchars($ticket['numero_ticket']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['evento_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['asistente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['tipo_ticket_nombre']); ?></td>
                            <td>
                                <span class="tag tag-<?php echo strtolower($ticket['estado']); ?>">
                                    <?php echo htmlspecialchars($ticket['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="tag <?php echo $ticket['escaneado'] ? 'tag-success' : 'tag-pending'; ?>">
                                    <?php echo $ticket['escaneado'] ? 'Sí' : 'No'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (hasPermission('editar_tickets')): ?>
                                    <a href="ticket_gestionar.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-icon btn-edit" title="Editar">✏️</a>
                                <?php endif; ?>
                                <?php if (hasPermission('eliminar_tickets')): ?>
                                    <a href="ticket_eliminar.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este ticket? Esta acción no se puede deshacer.');">🗑️</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>