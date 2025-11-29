<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('editar_usuarios')) {
    die("Acceso denegado.");
}

// 2. Obtener el ID del usuario de la URL
$id_usuario = $_GET['id'] ?? null;
if (!$id_usuario) {
    header("Location: usuarios.php");
    exit;
}

$error = '';
$success = '';

// 3. Lógica POST: Procesar la actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Contraseña (opcional)
    $id_rol = $_POST['id_rol'];
    $estado = $_POST['estado'];

    try {
        // Lógica para actualizar contraseña SOLO si se proporciona una nueva
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "UPDATE usuarios SET nombre_completo = ?, email = ?, password_hash = ?, id_rol = ?, estado = ?
                 WHERE id_usuario = ?"
            );
            $stmt->execute([$nombre, $email, $password_hash, $id_rol, $estado, $id_usuario]);
        } else {
            // Actualizar sin cambiar la contraseña
            $stmt = $pdo->prepare(
                "UPDATE usuarios SET nombre_completo = ?, email = ?, id_rol = ?, estado = ?
                 WHERE id_usuario = ?"
            );
            $stmt->execute([$nombre, $email, $id_rol, $estado, $id_usuario]);
        }
        
        $success = "Usuario actualizado correctamente.";
        // Opcional: redirigir
        // header("Location: usuarios.php?status=updated");
        // exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "El correo electrónico ya está registrado por otro usuario.";
        } else {
            $error = "Error al actualizar el usuario: " . $e->getMessage();
        }
    }
}

// 4. Lógica GET: Obtener datos del usuario para rellenar el formulario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    // Si el usuario no existe, volver a la lista
    header("Location: usuarios.php");
    exit;
}

// Obtener roles para el <select>
$stmt_roles = $pdo->query("SELECT * FROM roles ORDER BY nombre_rol");
$roles = $stmt_roles->fetchAll();

// 5. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Editar Usuario: <?php echo htmlspecialchars($usuario['nombre_completo']); ?></h1>
        <a href="usuarios.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form action="usuario_editar.php?id=<?php echo $usuario['id_usuario']; ?>" method="POST">
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="id_rol">Rol</label>
                    <select id="id_rol" name="id_rol" class="form-control" required>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['id_rol']; ?>" <?php if ($usuario['id_rol'] == $rol['id_rol']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($rol['nombre_rol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="Activo" <?php if ($usuario['estado'] == 'Activo') echo 'selected'; ?>>Activo</option>
                        <option value="Inactivo" <?php if ($usuario['estado'] == 'Inactivo') echo 'selected'; ?>>Inactivo</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>