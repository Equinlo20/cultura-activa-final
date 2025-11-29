<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad: Verificar login y permisos
checkLogin();
if (!hasPermission('ver_usuarios')) {
    // Si no tiene permiso, lo sacamos
    die("Acceso denegado. No tienes permiso para ver esta página.");
}

// 2. Lógica: Obtener datos de la BD
$stmt = $pdo->query("
    SELECT u.id_usuario, u.nombre_completo, u.email, u.estado, u.fecha_creacion, r.nombre_rol 
    FROM usuarios u
    LEFT JOIN roles r ON u.id_rol = r.id_rol
    ORDER BY u.id_usuario DESC
");
$usuarios = $stmt->fetchAll();

// 3. Renderizado: Incluir plantillas HTML
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    
    <div class="header-bar">
        <h1>Listado de Usuarios</h1>
        <?php if (hasPermission('crear_usuarios')): ?>
            <a href="usuario_crear.php" class="btn btn-primary">+ Agregar Usuario</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
                            <td>
                                <?php if ($usuario['estado'] == 'Activo'): ?>
                                    <span class="tag tag-success">Activo</span>
                                <?php else: ?>
                                    <span class="tag tag-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($usuario['fecha_creacion'])); ?></td>
                            <td>
                                <?php if (hasPermission('editar_usuarios')): ?>
                                    <a href="usuario_editar.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-icon btn-edit" title="Editar">✏️</a>
                                <?php endif; ?>
                                <?php if (hasPermission('eliminar_usuarios')): ?>
                                    <a href="usuario_eliminar.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">🗑️</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
// 6. Incluir el footer
include '../includes/footer.php';
?>