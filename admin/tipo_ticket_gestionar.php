<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('crear_tipos_ticket') && !hasPermission('editar_tipos_ticket')) {
    die("Acceso denegado.");
}

// 2. Determinar modo (Crear/Editar)
$id_tipo_ticket = $_GET['id'] ?? null;
$is_editing = ($id_tipo_ticket !== null);

// Valores por defecto
$tipo = [
    'id_evento' => '',
    'nombre' => '',
    'precio' => '0.00',
    'cantidad_disponible' => 100
];
$error = '';
$success = '';

// 3. Lógica POST (Guardar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_evento = $_POST['id_evento'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad_disponible'];

    if (empty($id_evento) || empty($nombre) || !isset($precio) || !isset($cantidad)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            if ($is_editing) {
                // --- Actualizar ---
                if (!hasPermission('editar_tipos_ticket')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "UPDATE tipos_ticket SET id_evento = ?, nombre = ?, precio = ?, cantidad_disponible = ?
                     WHERE id_tipo_ticket = ?"
                );
                $stmt->execute([$id_evento, $nombre, $precio, $cantidad, $id_tipo_ticket]);
                header("Location: tipos_ticket.php?status=updated");
                exit;

            } else {
                // --- Crear ---
                if (!hasPermission('crear_tipos_ticket')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "INSERT INTO tipos_ticket (id_evento, nombre, precio, cantidad_disponible) 
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$id_evento, $nombre, $precio, $cantidad]);
                header("Location: tipos_ticket.php?status=created");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    }
}

// 4. Lógica GET (Cargar datos)
if ($is_editing) {
    $stmt = $pdo->prepare("SELECT * FROM tipos_ticket WHERE id_tipo_ticket = ?");
    $stmt->execute([$id_tipo_ticket]);
    $tipo = $stmt->fetch();
}
// Cargar eventos para el dropdown
$stmt_eventos = $pdo->query("SELECT id_evento, nombre FROM eventos ORDER BY nombre");
$eventos = $stmt_eventos->fetchAll();

// 5. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1><?php echo $is_editing ? 'Editar Tipo de Ticket' : 'Crear Tipo de Ticket'; ?></h1>
        <a href="tipos_ticket.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            
            <form action="tipo_ticket_gestionar.php<?php if ($is_editing) echo '?id=' . $id_tipo_ticket; ?>" method="POST">
                
                <div class="form-group">
                    <label for="id_evento">Evento al que pertenece</label>
                    <select id="id_evento" name="id_evento" class="form-control" required>
                        <option value="">-- Seleccionar Evento --</option>
                        <?php foreach ($eventos as $evento): ?>
                            <option value="<?php echo $evento['id_evento']; ?>" <?php if ($tipo['id_evento'] == $evento['id_evento']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($evento['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="nombre">Nombre (Ej: "VIP", "General")</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($tipo['nombre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="precio">Precio (0.00 para gratuito)</label>
                        <input type="number" step="0.01" id="precio" name="precio" class="form-control" value="<?php echo htmlspecialchars($tipo['precio']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="cantidad_disponible">Cantidad Disponible</label>
                        <input type="number" id="cantidad_disponible" name="cantidad_disponible" class="form-control" value="<?php echo htmlspecialchars($tipo['cantidad_disponible']); ?>" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>