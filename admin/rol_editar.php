<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

checkLogin();
if (!hasPermission('editar_roles') && !hasPermission('crear_roles')) {
    die("Acceso denegado.");
}

// 1. Determinar si estamos editando o creando
$id_rol = $_GET['id'] ?? null;
$is_editing = ($id_rol !== null);

$rol_nombre = '';
$error = '';
$success = '';

// 2. Lógica POST (Guardar cambios)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rol_nombre = $_POST['nombre_rol'];
    $permisos_seleccionados = $_POST['permisos'] ?? []; // Array de IDs de permisos

    if (empty($rol_nombre)) {
        $error = "El nombre del rol es obligatorio.";
    } else {
        // Iniciar transacción
        $pdo->beginTransaction();
        try {
            if ($is_editing) {
                // --- Actualizar Rol ---
                if (!hasPermission('editar_roles')) die("Acceso denegado.");
                $stmt = $pdo->prepare("UPDATE roles SET nombre_rol = ? WHERE id_rol = ?");
                $stmt->execute([$rol_nombre, $id_rol]);
            } else {
                // --- Crear Rol ---
                if (!hasPermission('crear_roles')) die("Acceso denegado.");
                $stmt = $pdo->prepare("INSERT INTO roles (nombre_rol) VALUES (?)");
                $stmt->execute([$rol_nombre]);
                $id_rol = $pdo->lastInsertId(); // Obtener el ID del nuevo rol
            }
            
            // --- Actualizar Permisos ---
            // 1. Borrar todos los permisos actuales de este rol
            $stmt_delete = $pdo->prepare("DELETE FROM rol_permisos WHERE id_rol = ?");
            $stmt_delete->execute([$id_rol]);

            // 2. Insertar los nuevos permisos seleccionados
            $stmt_insert = $pdo->prepare("INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)");
            foreach ($permisos_seleccionados as $id_permiso) {
                $stmt_insert->execute([$id_rol, $id_permiso]);
            }
            
            // Confirmar transacción
            $pdo->commit();
            $success = "Rol guardado correctamente.";
            
            // Si era creación, redirigir a la página de edición
            if (!$is_editing) {
                header("Location: rol_editar.php?id=$id_rol&status=created");
                exit;
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error al guardar el rol: " . $e->getMessage();
        }
    }
}

// 3. Lógica GET (Cargar datos para el formulario)
$permisos_actuales = [];
if ($is_editing) {
    // Cargar datos del rol
    $stmt_rol = $pdo->prepare("SELECT * FROM roles WHERE id_rol = ?");
    $stmt_rol->execute([$id_rol]);
    $rol = $stmt_rol->fetch();
    if ($rol) {
        $rol_nombre = $rol['nombre_rol'];
    }

    // Cargar permisos que este rol YA TIENE
    $stmt_perms = $pdo->prepare("SELECT id_permiso FROM rol_permisos WHERE id_rol = ?");
    $stmt_perms->execute([$id_rol]);
    $permisos_actuales_db = $stmt_perms->fetchAll(PDO::FETCH_COLUMN);
    // Convertir a un array asociativo para búsqueda rápida (ej: [1 => true, 5 => true])
    $permisos_actuales = array_flip($permisos_actuales_db);
}

// 4. Cargar TODOS los permisos disponibles en el sistema
$stmt_all_perms = $pdo->query("SELECT * FROM permisos ORDER BY nombre_permiso");
$todos_los_permisos = $stmt_all_perms->fetchAll();


// 5. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1><?php echo $is_editing ? 'Editar Rol' : 'Crear Nuevo Rol'; ?></h1>
        <a href="roles.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form action="rol_editar.php<?php if ($is_editing) echo '?id=' . $id_rol; ?>" method="POST">
                
                <div class="form-group">
                    <label for="nombre_rol">Nombre del Rol</label>
                    <input type="text" id="nombre_rol" name="nombre_rol" class="form-control" value="<?php echo htmlspecialchars($rol_nombre); ?>" required>
                </div>
                
                <hr>
                
                <h3>Permisos</h3>
                <div class="permissions-grid">
                    <?php
                    $grupos = [];
                    foreach ($todos_los_permisos as $perm) {
                        // Ej: "ver_usuarios" -> $grupo = "usuarios"
                        list($accion, $modulo) = explode('_', $perm['nombre_permiso'], 2);
                        $grupos[$modulo][] = ['accion' => $accion, 'perm' => $perm];
                    }
                    ?>
                    
                    <?php foreach ($grupos as $nombre_modulo => $permisos_grupo): ?>
                        <fieldset class="permission-group">
                            <legend><?php echo ucfirst($nombre_modulo); ?></legend>
                            <?php foreach ($permisos_grupo as $item): 
                                $permiso = $item['perm'];
                                // Verificar si este permiso está en la lista de permisos actuales del rol
                                $is_checked = isset($permisos_actuales[$permiso['id_permiso']]);
                            ?>
                                <div class="checkbox-group">
                                    <input type="checkbox" 
                                           id="perm_<?php echo $permiso['id_permiso']; ?>" 
                                           name="permisos[]" 
                                           value="<?php echo $permiso['id_permiso']; ?>"
                                           <?php if ($is_checked) echo 'checked'; ?>>
                                    <label for="perm_<?php echo $permiso['id_permiso']; ?>"><?php echo ucfirst($item['accion']); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Rol</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>