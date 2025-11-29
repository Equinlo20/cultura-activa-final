<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('crear_tickets') && !hasPermission('editar_tickets')) {
    die("Acceso denegado.");
}

// 2. Determinar si estamos editando o creando
$id_ticket = $_GET['id'] ?? null;
$is_editing = ($id_ticket !== null);

// Valores por defecto
$ticket = [
    'id_evento' => '',
    'id_usuario' => '',
    'id_tipo_ticket' => '',
    'numero_ticket' => 'MAN-' . strtoupper(uniqid()), // Generar un ticket manual por defecto
    'estado' => 'Activo',
    'escaneado' => 0
];
$error = '';
$success = '';

// 3. Lógica POST (Guardar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_evento = $_POST['id_evento'];
    $id_usuario = $_POST['id_usuario'];
    $id_tipo_ticket = $_POST['id_tipo_ticket'];
    $numero_ticket = $_POST['numero_ticket'];
    $estado = $_POST['estado'];
    $escaneado = isset($_POST['escaneado']) ? 1 : 0; // Checkbox

    if (empty($id_evento) || empty($id_usuario) || empty($id_tipo_ticket) || empty($numero_ticket)) {
        $error = "Evento, Asistente, Tipo de Ticket y Número de Ticket son obligatorios.";
    } else {
        try {
            if ($is_editing) {
                // --- Actualizar Ticket ---
                if (!hasPermission('editar_tickets')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "UPDATE tickets SET id_evento = ?, id_usuario = ?, id_tipo_ticket = ?, numero_ticket = ?, estado = ?, escaneado = ?
                     WHERE id_ticket = ?"
                );
                $stmt->execute([$id_evento, $id_usuario, $id_tipo_ticket, $numero_ticket, $estado, $escaneado, $id_ticket]);
                $success = "Ticket actualizado correctamente.";
                header("Location: tickets.php?status=updated");
                exit;

            } else {
                // --- Crear Ticket ---
                if (!hasPermission('crear_tickets')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "INSERT INTO tickets (id_evento, id_usuario, id_tipo_ticket, numero_ticket, estado, escaneado) 
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$id_evento, $id_usuario, $id_tipo_ticket, $numero_ticket, $estado, $escaneado]);
                $id_ticket = $pdo->lastInsertId();
                
                // Redirigir a la lista
                header("Location: tickets.php?status=created");
                exit;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Error de llave duplicada
                $error = "Error: El número de ticket '$numero_ticket' ya existe.";
            } else {
                $error = "Error al guardar el ticket: " . $e->getMessage();
            }
        }
    }
}

// 4. Lógica GET (Cargar datos para el formulario)

// Cargar datos del ticket si estamos editando
if ($is_editing) {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ?");
    $stmt->execute([$id_ticket]);
    $data = $stmt->fetch();
    if ($data) {
        $ticket = $data; // Sobrescribir los valores por defecto
    }
}

// Cargar datos para los Dropdowns
// 4a. Cargar Eventos
$stmt_eventos = $pdo->query("SELECT id_evento, nombre FROM eventos ORDER BY nombre");
$eventos = $stmt_eventos->fetchAll();

// 4b. Cargar Asistentes (Usuarios con rol "Asistente" o "Participante")
$stmt_asistentes = $pdo->query("
    SELECT u.id_usuario, u.nombre_completo 
    FROM usuarios u
    JOIN roles r ON u.id_rol = r.id_rol
    WHERE r.nombre_rol = 'Asistente' OR r.nombre_rol = 'Participante'
    ORDER BY u.nombre_completo
");
$asistentes = $stmt_asistentes->fetchAll();

// 4c. Cargar Tipos de Ticket (Agrupados por evento para mejor UX)
$stmt_tipos = $pdo->query("
    SELECT tt.id_tipo_ticket, tt.nombre, e.nombre as evento_nombre
    FROM tipos_ticket tt
    JOIN eventos e ON tt.id_evento = e.id_evento
    ORDER BY e.nombre, tt.nombre
");
$tipos_ticket = $stmt_tipos->fetchAll();


// 5. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1><?php echo $is_editing ? 'Editar Ticket' : 'Crear Ticket Manual'; ?></h1>
        <a href="tickets.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form action="ticket_gestionar.php<?php if ($is_editing) echo '?id=' . $id_ticket; ?>" method="POST">
                
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="id_evento">Evento</label>
                        <select id="id_evento" name="id_evento" class="form-control" required>
                            <option value="">-- Seleccionar Evento --</option>
                            <?php foreach ($eventos as $evento): ?>
                                <option value="<?php echo $evento['id_evento']; ?>" <?php if ($ticket['id_evento'] == $evento['id_evento']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($evento['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_usuario">Asistente</label>
                        <select id="id_usuario" name="id_usuario" class="form-control" required>
                            <option value="">-- Seleccionar Asistente --</option>
                            <?php foreach ($asistentes as $asistente): ?>
                                <option value="<?php echo $asistente['id_usuario']; ?>" <?php if ($ticket['id_usuario'] == $asistente['id_usuario']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($asistente['nombre_completo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_tipo_ticket">Tipo de Ticket</label>
                        <select id="id_tipo_ticket" name="id_tipo_ticket" class="form-control" required>
                            <option value="">-- Seleccionar Tipo --</option>
                            <?php 
                            $current_evento = '';
                            foreach ($tipos_ticket as $tipo): 
                                if ($tipo['evento_nombre'] != $current_evento) {
                                    if ($current_evento != '') echo '</optgroup>';
                                    $current_evento = $tipo['evento_nombre'];
                                    echo '<optgroup label="' . htmlspecialchars($current_evento) . '">';
                                }
                            ?>
                                <option value="<?php echo $tipo['id_tipo_ticket']; ?>" <?php if ($ticket['id_tipo_ticket'] == $tipo['id_tipo_ticket']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php if ($current_evento != '') echo '</optgroup>'; ?>
                        </select>
                    </div>
                </div>

                <hr>
                
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="numero_ticket">Número de Ticket</label>
                        <input type="text" id="numero_ticket" name="numero_ticket" class="form-control" value="<?php echo htmlspecialchars($ticket['numero_ticket']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control">
                            <option value="Activo" <?php if ($ticket['estado'] == 'Activo') echo 'selected'; ?>>Activo</option>
                            <option value="Usado" <?php if ($ticket['estado'] == 'Usado') echo 'selected'; ?>>Usado</option>
                            <option value="Cancelado" <?php if ($ticket['estado'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Opciones</label>
                        <div class="checkbox-group" style="padding-top: 10px;">
                            <input type="checkbox" id="escaneado" name="escaneado" value="1" <?php if ($ticket['escaneado'] == 1) echo 'checked'; ?>>
                            <label for="escaneado">Marcar como Escaneado (Asistencia confirmada)</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar Ticket</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>