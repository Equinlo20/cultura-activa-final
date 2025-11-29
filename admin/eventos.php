<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

checkLogin();
if (!hasPermission('ver_eventos')) {
    die("Acceso denegado.");
}

// Lógica: Obtener eventos. 
// Hacemos JOIN con usuarios para obtener el nombre del organizador
$stmt = $pdo->query("
    SELECT e.*, u.nombre_completo as organizador_nombre
    FROM eventos e
    LEFT JOIN usuarios u ON e.id_organizador = u.id_usuario
    ORDER BY e.fecha_evento DESC
");
$eventos = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Gestión de Eventos</h1>
        <?php if (hasPermission('crear_eventos')): ?>
            <a href="evento_gestionar.php" class="btn btn-primary">+ Crear Evento</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Evento</th>
                        <th>Organizador</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $evento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($evento['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($evento['organizador_nombre']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($evento['fecha_evento'])); ?></td>
                            <td><?php echo htmlspecialchars($evento['lugar']); ?></td>
                            <td>
                                <span class="tag <?php echo strtolower($evento['estado']); ?>"><?php echo htmlspecialchars($evento['estado']); ?></span>
                            </td>
                            <td>
                                <a href="evento_detalle.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-icon btn-view" title="Ver Detalles">👁️</a>
                                
                                <?php if (hasPermission('editar_eventos')): ?>
                                    <a href="evento_gestionar.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-icon btn-edit" title="Editar">✏️</a>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('eliminar_eventos')): ?>
                                    <a href="evento_eliminar.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Seguro?');">🗑️</a>
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