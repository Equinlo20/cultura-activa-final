<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('crear_usuarios')) {
    die("Acceso denegado.");
}

$error = '';
$success = '';

// 2. Lógica POST: Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $id_rol = $_POST['id_rol'];
    $estado = $_POST['estado'];

    // Validación simple
    if (empty($nombre) || empty($email) || empty($password) || empty($id_rol)) {
        $error = "Todos los campos (excepto estado) son obligatorios.";
    } else {
        // Hashear la contraseña por seguridad
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // Insertar en la BD
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre_completo, email, password_hash, id_rol, estado) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nombre, $email, $password_hash, $id_rol, $estado]);
            
            // Redirigir a la lista de usuarios
            header("Location: usuarios.php?status=created");
            exit;

        } catch (PDOException $e) {
            // Manejar error de email duplicado
            if ($e->getCode() == 23000) {
                $error = "El correo electrónico ya está registrado.";
            } else {
                $error = "Error al crear el usuario: " . $e->getMessage();
            }
        }
    }
}

// 3. Lógica GET: Obtener roles para el <select>
$stmt_roles = $pdo->query("SELECT * FROM roles ORDER BY nombre_rol");
$roles = $stmt_roles->fetchAll();


// 4. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Crear Nuevo Usuario</h1>
        <a href="usuarios.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form action="usuario_crear.php" method="POST">
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="id_rol">Rol</label>
                    <select id="id_rol" name="id_rol" class="form-control" required>
                        <option value="">-- Seleccionar Rol --</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['id_rol']; ?>"><?php echo htmlspecialchars($rol['nombre_rol']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar Usuario</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>