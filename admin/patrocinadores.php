<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ver_patrocinadores')) {
    die("Acceso denegado.");
}

// 2. Lógica: Obtener patrocinadores, uniendo con eventos para el nombre
$stmt = $pdo->query("
    SELECT 
        s.*, e.nombre as evento_nombre
    FROM patrocinadores s
    LEFT JOIN eventos e ON s.id_evento = e.id_evento
    ORDER BY s.id_patrocinador DESC
");
$sponsors = $stmt->fetchAll();

// 3. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Patrocinadores</h1>
        <?php if (hasPermission('crear_patrocinadores')): ?>
            <a href="patrocinador_gestionar.php" class="btn btn-primary">+ Nuevo Patrocinador</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'created'): ?>
            <div class="alert alert-success">Patrocinador creado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'updated'): ?>
            <div class="alert alert-success">Patrocinador actualizado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success">Patrocinador eliminado exitosamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Evento Asociado</th>
                        <th>Nivel</th>
                        <th>Contribución</th>
                        <th>Contacto</th>
                        <th>Destacado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sponsors as $sponsor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sponsor['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($sponsor['evento_nombre'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="tag tag-level-<?php echo strtolower($sponsor['nivel']); ?>">
                                    <?php echo htmlspecialchars($sponsor['nivel']); ?>
                                </span>
                            </td>
                            <td>$<?php echo number_format($sponsor['contribucion'], 2); ?></td>
                            <td>
                                <?php echo htmlspecialchars($sponsor['contacto_nombre']); ?>
                                <br>
                                <small><?php echo htmlspecialchars($sponsor['contacto_email']); ?></small>
                            </td>
                            <td>
                                <span class="tag <?php echo $sponsor['destacado'] ? 'tag-success' : 'tag-pending'; ?>">
                                    <?php echo $sponsor['destacado'] ? 'Sí' : 'No'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (hasPermission('editar_patrocinadores')): ?>
                                    <a href="patrocinador_gestionar.php?id=<?php echo $sponsor['id_patrocinador']; ?>" class="btn btn-icon btn-edit" title="Editar">✏️</a>
                                <?php endif; ?>
                                <?php if (hasPermission('eliminar_patrocinadores')): ?>
                                    <a href="patrocinador_eliminar.php?id=<?php echo $sponsor['id_patrocinador']; ?>" class="btn btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este patrocinador?');">🗑️</a>
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
    .tag-level-platinum { background-color: #e5e4e2; color: #333; }
    .tag-level-gold { background-color: #ffd700; color: #5c4d00; }
    .tag-level-silver { background-color: #c0c0c0; color: #4d4d4d; }
    .tag-level-bronze { background-color: #cd7f32; color: white; }
</style>

<?php include '../includes/footer.php'; ?>