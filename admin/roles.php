<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// Seguridad
checkLogin();
if (!hasPermission('ver_roles')) {
    die("Acceso denegado.");
}

// Lógica (Obtener todos los roles)
$stmt = $pdo->query("SELECT * FROM roles ORDER BY nombre_rol");
$roles = $stmt->fetchAll();

// Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Gestión de Roles</h1>
        <?php if (hasPermission('crear_roles')): ?>
            <a href="rol_editar.php" class="btn btn-primary">+ Agregar Rol</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $rol): ?>
                        <tr>
                            <td><?php echo $rol['id_rol']; ?></td>
                            <td><?php echo htmlspecialchars($rol['nombre_rol']); ?></td>
                            <td>
                                <?php if (hasPermission('editar_roles')): ?>
                                    <a href="rol_editar.php?id=<?php echo $rol['id_rol']; ?>" class="btn btn-icon btn-edit" title="Editar Permisos">✏️</a>
                                <?php endif; ?>
                                <?php if (hasPermission('eliminar_roles')): ?>
                                    <?php if ($rol['id_rol'] != 1): ?>
                                        <a href="rol_eliminar.php?id=<?php echo $rol['id_rol']; ?>" class="btn btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Seguro?');">🗑️</a>
                                    <?php endif; ?>
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