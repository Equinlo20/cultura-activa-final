<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

checkLogin();
if (!hasPermission('ver_asistentes')) {
    die("Acceso denegado.");
}

// 1. Obtener el ID del rol "Asistente" o "Participante"
$stmt_rol = $pdo->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'Asistente' OR nombre_rol = 'Participante' LIMIT 1");
$stmt_rol->execute();
$id_rol_asistente = $stmt_rol->fetchColumn();

// 2. Lógica: Obtener usuarios que tengan ese ID de rol
$stmt = $pdo->prepare("
    SELECT id_usuario, nombre_completo, email, telefono, estado, fecha_creacion
    FROM usuarios
    WHERE id_rol = ?
    ORDER BY id_usuario DESC
");
$stmt->execute([$id_rol_asistente]);
$asistentes = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Gestión de Asistentes</h1>
        <?php if (hasPermission('crear_usuarios')): ?>
            <a href="usuario_crear.php?rol=<?php echo $id_rol_asistente; ?>" class="btn btn-primary">+ Agregar Asistente</a>
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
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asistentes as $asistente): ?>
                        <tr>
                            <td><?php echo $asistente['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($asistente['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($asistente['email']); ?></td>
                            <td><?php echo htmlspecialchars($asistente['telefono']); ?></td>
                            <td>
                                <span class="tag <?php echo ($asistente['estado'] == 'Activo') ? 'tag-success' : 'tag-danger'; ?>">
                                    <?php echo htmlspecialchars($asistente['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($asistente['fecha_creacion'])); ?></td>
                            <td>
                                <?php if (hasPermission('editar_usuarios')): ?>
                                    <a href="usuario_editar.php?id=<?php echo $asistente['id_usuario']; ?>" class="btn btn-icon btn-edit" title="Editar">✏️</a>
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