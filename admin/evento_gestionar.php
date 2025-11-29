<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

checkLogin();
if (!hasPermission('editar_eventos') && !hasPermission('crear_eventos')) {
    die("Acceso denegado.");
}

// 1. Determinar si estamos editando o creando
$id_evento = $_GET['id'] ?? null;
$is_editing = ($id_evento !== null);

$evento = [
    'nombre' => '',
    'descripcion' => '',
    'fecha_evento' => '',
    'lugar' => '',
    'id_organizador' => $_SESSION['user_id'], // Por defecto, el organizador es el usuario logueado
    'estado' => 'Borrador',
    'capacidad' => 500,
    'visibilidad' => 'Público'
];
$error = '';
$success = '';

// 2. Lógica POST (Guardar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_evento = $_POST['fecha_evento'];
    $lugar = $_POST['lugar'];
    $estado = $_POST['estado'];
    $capacidad = $_POST['capacidad'];
    $visibilidad = $_POST['visibilidad'];
    // El id_organizador se mantiene (el del usuario logueado o el que ya tenía)

    if (empty($nombre) || empty($fecha_evento) || empty($lugar)) {
        $error = "Nombre, Fecha y Lugar son obligatorios.";
    } else {
        try {
            if ($is_editing) {
                // --- Actualizar Evento ---
                if (!hasPermission('editar_eventos')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "UPDATE eventos SET nombre = ?, descripcion = ?, fecha_evento = ?, lugar = ?, estado = ?, capacidad = ?, visibilidad = ?
                     WHERE id_evento = ?"
                );
                $stmt->execute([$nombre, $descripcion, $fecha_evento, $lugar, $estado, $capacidad, $visibilidad, $id_evento]);
                $success = "Evento actualizado.";

            } else {
                // --- Crear Evento ---
                if (!hasPermission('crear_eventos')) die("Acceso denegado.");
                $id_organizador = $_SESSION['user_id'];
                $stmt = $pdo->prepare(
                    "INSERT INTO eventos (nombre, descripcion, fecha_evento, lugar, estado, capacidad, visibilidad, id_organizador) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$nombre, $descripcion, $fecha_evento, $lugar, $estado, $capacidad, $visibilidad, $id_organizador]);
                $id_evento = $pdo->lastInsertId();
                header("Location: evento_gestionar.php?id=$id_evento&status=created");
                exit;
            }
        } catch (Exception $e) {
            $error = "Error al guardar el evento: " . $e->getMessage();
        }
    }
}

// 3. Lógica GET (Cargar datos si estamos editando)
if ($is_editing) {
    $stmt = $pdo->prepare("SELECT * FROM eventos WHERE id_evento = ?");
    $stmt->execute([$id_evento]);
    $data = $stmt->fetch();
    if ($data) {
        $evento = $data;
    } else {
        header("Location: eventos.php"); // Si no existe el ID, volver
        exit;
    }
}

// 4. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1><?php echo $is_editing ? 'Editar Evento' : 'Crear Nuevo Evento'; ?></h1>
        <a href="eventos.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form action="evento_gestionar.php<?php if ($is_editing) echo '?id=' . $id_evento; ?>" method="POST">
                
                <div class="form-group">
                    <label for="nombre">Nombre del Evento</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($evento['nombre']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="5"><?php echo htmlspecialchars($evento['descripcion']); ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="fecha_evento">Fecha y Hora</label>
                        <input type="datetime-local" id="fecha_evento" name="fecha_evento" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($evento['fecha_evento'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lugar">Lugar</label>
                        <input type="text" id="lugar" name="lugar" class="form-control" value="<?php echo htmlspecialchars($evento['lugar']); ?>" required>
                    </div>
                </div>

                <hr>
                
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control">
                            <option value="Borrador" <?php if ($evento['estado'] == 'Borrador') echo 'selected'; ?>>Borrador</option>
                            <option value="Publicado" <?php if ($evento['estado'] == 'Publicado') echo 'selected'; ?>>Publicado</option>
                            <option value="Cancelado" <?php if ($evento['estado'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="capacidad">Capacidad</label>
                        <input type="number" id="capacidad" name="capacidad" class="form-control" value="<?php echo htmlspecialchars($evento['capacidad']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="visibilidad">Visibilidad</label>
                        <select id="visibilidad" name="visibilidad" class="form-control">
                            <option value="Público" <?php if ($evento['visibilidad'] == 'Público') echo 'selected'; ?>>Público</option>
                            <option value="Privado" <?php if ($evento['visibilidad'] == 'Privado') echo 'selected'; ?>>Privado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Evento</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>