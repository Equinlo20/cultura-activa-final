<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('crear_patrocinadores') && !hasPermission('editar_patrocinadores')) {
    die("Acceso denegado.");
}

// 2. Determinar si estamos editando o creando
$id_patrocinador = $_GET['id'] ?? null;
$is_editing = ($id_patrocinador !== null);

// Valores por defecto
$sponsor = [
    'nombre' => '',
    'id_evento' => null,
    'nivel' => 'Bronze',
    'contribucion' => '0.00',
    'contacto_nombre' => '',
    'contacto_email' => '',
    'destacado' => 0
];
$error = '';
$success = '';

// 3. Lógica POST (Guardar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $id_evento = !empty($_POST['id_evento']) ? $_POST['id_evento'] : null; // Permitir NULL
    $nivel = $_POST['nivel'];
    $contribucion = $_POST['contribucion'];
    $contacto_nombre = $_POST['contacto_nombre'];
    $contacto_email = $_POST['contacto_email'];
    $destacado = isset($_POST['destacado']) ? 1 : 0; // Checkbox

    if (empty($nombre) || empty($nivel) || empty($contribucion)) {
        $error = "Nombre, Nivel y Contribución son obligatorios.";
    } else {
        try {
            if ($is_editing) {
                // --- Actualizar Patrocinador ---
                if (!hasPermission('editar_patrocinadores')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "UPDATE patrocinadores SET nombre = ?, id_evento = ?, nivel = ?, contribucion = ?, 
                     contacto_nombre = ?, contacto_email = ?, destacado = ?
                     WHERE id_patrocinador = ?"
                );
                $stmt->execute([$nombre, $id_evento, $nivel, $contribucion, $contacto_nombre, $contacto_email, $destacado, $id_patrocinador]);
                $success = "Patrocinador actualizado correctamente.";
                header("Location: patrocinadores.php?status=updated");
                exit;

            } else {
                // --- Crear Patrocinador ---
                if (!hasPermission('crear_patrocinadores')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "INSERT INTO patrocinadores (nombre, id_evento, nivel, contribucion, contacto_nombre, contacto_email, destacado) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$nombre, $id_evento, $nivel, $contribucion, $contacto_nombre, $contacto_email, $destacado]);
                $id_patrocinador = $pdo->lastInsertId();
                
                header("Location: patrocinadores.php?status=created");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error al guardar el patrocinador: " . $e->getMessage();
        }
    }
}

// 4. Lógica GET (Cargar datos para el formulario)

// Cargar datos del patrocinador si estamos editando
if ($is_editing) {
    $stmt = $pdo->prepare("SELECT * FROM patrocinadores WHERE id_patrocinador = ?");
    $stmt->execute([$id_patrocinador]);
    $data = $stmt->fetch();
    if ($data) {
        $sponsor = $data; // Sobrescribir los valores por defecto
    }
}

// 4a. Cargar Eventos para el Dropdown
$stmt_eventos = $pdo->query("SELECT id_evento, nombre FROM eventos ORDER BY nombre");
$eventos = $stmt_eventos->fetchAll();


// 5. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1><?php echo $is_editing ? 'Editar Patrocinador' : 'Crear Nuevo Patrocinador'; ?></h1>
        <a href="patrocinadores.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form action="patrocinador_gestionar.php<?php if ($is_editing) echo '?id=' . $id_patrocinador; ?>" method="POST">
                
                <div class="form-group">
                    <label for="nombre">Nombre del Patrocinador</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($sponsor['nombre']); ?>" required>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="id_evento">Evento Asociado (Opcional)</label>
                        <select id="id_evento" name="id_evento" class="form-control">
                            <option value="">-- Ningún evento / Patrocinador general --</option>
                            <?php foreach ($eventos as $evento): ?>
                                <option value="<?php echo $evento['id_evento']; ?>" <?php if ($sponsor['id_evento'] == $evento['id_evento']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($evento['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nivel">Nivel</label>
                        <select id="nivel" name="nivel" class="form-control" required>
                            <option value="Platinum" <?php if ($sponsor['nivel'] == 'Platinum') echo 'selected'; ?>>Platinum</option>
                            <option value="Gold" <?php if ($sponsor['nivel'] == 'Gold') echo 'selected'; ?>>Gold</option>
                            <option value="Silver" <?php if ($sponsor['nivel'] == 'Silver') echo 'selected'; ?>>Silver</option>
                            <option value="Bronze" <?php if ($sponsor['nivel'] == 'Bronze') echo 'selected'; ?>>Bronze</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="contribucion">Contribución (Monto)</label>
                        <input type="number" step="0.01" id="contribucion" name="contribucion" class="form-control" value="<?php echo htmlspecialchars($sponsor['contribucion']); ?>" required>
                    </div>
                </div>

                <hr>
                <h4>Información de Contacto</h4>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="contacto_nombre">Nombre del Contacto</label>
                        <input type="text" id="contacto_nombre" name="contacto_nombre" class="form-control" value="<?php echo htmlspecialchars($sponsor['contacto_nombre']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="contacto_email">Email del Contacto</label>
                        <input type="email" id="contacto_email" name="contacto_email" class="form-control" value="<?php echo htmlspecialchars($sponsor['contacto_email']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group" style="padding-top: 10px;">
                        <input type="checkbox" id="destacado" name="destacado" value="1" <?php if ($sponsor['destacado'] == 1) echo 'checked'; ?>>
                        <label for="destacado">Marcar como Patrocinador Destacado</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar Patrocinador</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>