<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('crear_pagos') && !hasPermission('editar_pagos')) {
    die("Acceso denegado.");
}

// 2. Determinar si estamos editando o creando
$id_pago = $_GET['id'] ?? null;
$is_editing = ($id_pago !== null);

// Valores por defecto
$pago = [
    'id_ticket' => '',
    'monto' => '0.00',
    'metodo' => 'Bank transfer',
    'estado' => 'Pendiente',
    'id_transaccion' => ''
];
$error = '';
$success = '';

// 3. Lógica POST (Guardar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ticket = $_POST['id_ticket'];
    $monto = $_POST['monto'];
    $metodo = $_POST['metodo'];
    $estado = $_POST['estado'];
    $id_transaccion = $_POST['id_transaccion'];

    if (empty($id_ticket) || empty($monto) || empty($metodo) || empty($estado)) {
        $error = "Ticket, Monto, Método y Estado son obligatorios.";
    } else {
        try {
            if ($is_editing) {
                // --- Actualizar Pago ---
                if (!hasPermission('editar_pagos')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "UPDATE pagos SET id_ticket = ?, monto = ?, metodo = ?, estado = ?, id_transaccion = ?
                     WHERE id_pago = ?"
                );
                $stmt->execute([$id_ticket, $monto, $metodo, $estado, $id_transaccion, $id_pago]);
                $success = "Pago actualizado correctamente.";
                header("Location: pagos.php?status=updated");
                exit;

            } else {
                // --- Crear Pago ---
                if (!hasPermission('crear_pagos')) die("Acceso denegado.");
                $stmt = $pdo->prepare(
                    "INSERT INTO pagos (id_ticket, monto, metodo, estado, id_transaccion) 
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$id_ticket, $monto, $metodo, $estado, $id_transaccion]);
                $id_pago = $pdo->lastInsertId();
                
                header("Location: pagos.php?status=created");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error al guardar el pago: " . $e->getMessage();
        }
    }
}

// 4. Lógica GET (Cargar datos para el formulario)

// Cargar datos del pago si estamos editando
if ($is_editing) {
    $stmt = $pdo->prepare("SELECT * FROM pagos WHERE id_pago = ?");
    $stmt->execute([$id_pago]);
    $data = $stmt->fetch();
    if ($data) {
        $pago = $data; // Sobrescribir los valores por defecto
    }
}

// 4a. Cargar Tickets para el Dropdown
// (Incluye nombre de usuario y evento para mejor UX)
$stmt_tickets = $pdo->query("
    SELECT 
        t.id_ticket, t.numero_ticket,
        u.nombre_completo as asistente_nombre,
        e.nombre as evento_nombre
    FROM tickets t
    JOIN usuarios u ON t.id_usuario = u.id_usuario
    JOIN eventos e ON t.id_evento = e.id_evento
    ORDER BY t.id_ticket DESC
");
$tickets = $stmt_tickets->fetchAll();


// 5. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1><?php echo $is_editing ? 'Editar Pago' : 'Crear Nuevo Pago'; ?></h1>
        <a href="pagos.php" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form action="pago_gestionar.php<?php if ($is_editing) echo '?id=' . $id_pago; ?>" method="POST">
                
                <div class="form-group">
                    <label for="id_ticket">Ticket Asociado</label>
                    <select id="id_ticket" name="id_ticket" class="form-control" required>
                        <option value="">-- Seleccionar Ticket --</option>
                        <?php foreach ($tickets as $ticket): ?>
                            <option value="<?php echo $ticket['id_ticket']; ?>" <?php if ($pago['id_ticket'] == $ticket['id_ticket']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($ticket['numero_ticket'] . ' - ' . $ticket['asistente_nombre'] . ' (' . $ticket['evento_nombre'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="monto">Monto</label>
                        <input type="number" step="0.01" id="monto" name="monto" class="form-control" value="<?php echo htmlspecialchars($pago['monto']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="metodo">Método de Pago</label>
                        <select id="metodo" name="metodo" class="form-control" required>
                            <option value="Bank transfer" <?php if ($pago['metodo'] == 'Bank transfer') echo 'selected'; ?>>Bank transfer</option>
                            <option value="Credit card" <?php if ($pago['metodo'] == 'Credit card') echo 'selected'; ?>>Credit card</option>
                            <option value="Efectivo" <?php if ($pago['metodo'] == 'Efectivo') echo 'selected'; ?>>Efectivo</option>
                            <option value="Otro" <?php if ($pago['metodo'] == 'Otro') echo 'selected'; ?>>Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado del Pago</label>
                        <select id="estado" name="estado" class="form-control" required>
                            <option value="Pendiente" <?php if ($pago['estado'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                            <option value="Completado" <?php if ($pago['estado'] == 'Completado') echo 'selected'; ?>>Completado</option>
                            <option value="Reembolsado" <?php if ($pago['estado'] == 'Reembolsado') echo 'selected'; ?>>Reembolsado</Foption>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_transaccion">ID de Transacción (Opcional)</label>
                    <input type="text" id="id_transaccion" name="id_transaccion" class="form-control" value="<?php echo htmlspecialchars($pago['id_transaccion']); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar Pago</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>