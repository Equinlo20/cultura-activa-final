<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ver_tipos_ticket')) { // Permiso nuevo
    die("Acceso denegado.");
}

// 2. Lógica: Obtener tipos de ticket, uniendo con eventos
$stmt = $pdo->query("
    SELECT tt.*, e.nombre as evento_nombre
    FROM tipos_ticket tt
    JOIN eventos e ON tt.id_evento = e.id_evento
    ORDER BY e.nombre, tt.precio ASC
");
$tipos = $stmt->fetchAll();

// 3. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Gestión de Tipos de Ticket</h1>
        <?php if (hasPermission('crear_tipos_ticket')): // Permiso nuevo ?>
            <a href="tipo_ticket_gestionar.php" class="btn btn-primary">+ Nuevo Tipo de Ticket</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'created'): ?>
            <div class="alert alert-success">Tipo de ticket creado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'updated'): ?>
            <div class="alert alert-success">Tipo de ticket actualizado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success">Tipo de ticket eliminado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'error_fk'): ?>
            <div class="alert alert-danger">Error: No se puede eliminar. Hay tickets vendidos de este tipo.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre (Ej: "VIP")</th>
                        <th>Evento Asociado</th>
                        <th>Precio</th>
                        <th>Cantidad Disponible</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tipos as $tipo): ?>
                        <tr>
                            <td><?php echo $tipo['id_tipo_ticket']; ?></td>
                            <td><?php echo htmlspecialchars($tipo['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($tipo['evento_nombre']); ?></td>
                            <td>$<?php echo number_format($tipo['precio'], 2); ?></td>
                            <td><?php echo htmlspecialchars($tipo['cantidad_disponible']); ?></td>
                            <td>
                                <?php if (hasPermission('editar_tipos_ticket')): // Permiso nuevo ?>
                                    <a href="tipo_ticket_gestionar.php?id=<?php echo $tipo['id_tipo_ticket']; ?>" class="btn btn-icon btn-edit" title="Editar">✏️</a>
                                <?php endif; ?>
                                <?php if (hasPermission('eliminar_tipos_ticket')): // Permiso nuevo ?>
                                    <a href="tipo_ticket_eliminar.php?id=<?php echo $tipo['id_tipo_ticket']; ?>" class="btn btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Seguro?');">🗑️</a>
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