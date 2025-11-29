<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ver_control_asistencia')) {
    die("Acceso denegado.");
}

// 2. Lógica de Filtro
$id_evento_filtrado = $_GET['id_evento'] ?? null;
$tickets = [];
$evento_nombre = '';

// Cargar eventos para el dropdown
$stmt_eventos = $pdo->query("SELECT id_evento, nombre FROM eventos WHERE estado = 'Publicado' ORDER BY fecha_evento DESC");
$todos_los_eventos = $stmt_eventos->fetchAll();

// Si se seleccionó un evento, cargar los tickets
if ($id_evento_filtrado) {
    // Cargar nombre del evento
    $stmt_evt = $pdo->prepare("SELECT nombre FROM eventos WHERE id_evento = ?");
    $stmt_evt->execute([$id_evento_filtrado]);
    $evento_nombre = $stmt_evt->fetchColumn();

    // Cargar lista de asistentes/tickets
    $stmt_tickets = $pdo->prepare("
        SELECT 
            t.id_ticket, t.numero_ticket, t.estado, t.escaneado,
            u.nombre_completo as asistente_nombre
        FROM tickets t
        JOIN usuarios u ON t.id_usuario = u.id_usuario
        WHERE t.id_evento = ?
        ORDER BY u.nombre_completo ASC
    ");
    $stmt_tickets->execute([$id_evento_filtrado]);
    $tickets = $stmt_tickets->fetchAll();
}

// 3. Renderizado
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Control de Asistencia (Check-in)</h1>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'marked'): ?>
            <div class="alert alert-success">Asistencia marcada correctamente.</div>
        <?php elseif ($_GET['status'] == 'unmarked'): ?>
            <div class="alert alert-success">Asistencia desmarcada.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="control_asistencia.php" method="GET">
                <div class="form-group">
                    <label for="id_evento">Selecciona un evento para ver los asistentes:</label>
                    <select name="id_evento" id="id_evento" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Cargar Evento --</option>
                        <?php foreach ($todos_los_eventos as $evento): ?>
                            <option value="<?php echo $evento['id_evento']; ?>" <?php if ($id_evento_filtrado == $evento['id_evento']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($evento['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if ($id_evento_filtrado): ?>
    <div class="card">
        <div class="card-body">
            <h3>Lista de Asistentes para: <?php echo htmlspecialchars($evento_nombre); ?></h3>
            <p>(Total: <?php echo count($tickets); ?> inscritos)</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Asistente</th>
                        <th>Número de Ticket</th>
                        <th>Estado (Escaneado)</th>
                        <th>Acción (Check-in)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket['asistente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['numero_ticket']); ?></td>
                            <td>
                                <span class="tag <?php echo $ticket['escaneado'] ? 'tag-success' : 'tag-pending'; ?>">
                                    <?php echo $ticket['escaneado'] ? 'Presente' : 'Ausente'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (hasPermission('ejecutar_control_asistencia')): ?>
                                    <?php if ($ticket['escaneado']): ?>
                                        <a href="marcar_asistencia.php?id_ticket=<?php echo $ticket['id_ticket']; ?>&id_evento=<?php echo $id_evento_filtrado; ?>&action=desmarcar" class="btn btn-secondary" style="padding: 5px 10px;">
                                            Desmarcar
                                        </a>
                                    <?php else: ?>
                                        <a href="marcar_asistencia.php?id_ticket=<?php echo $ticket['id_ticket']; ?>&id_evento=<?php echo $id_evento_filtrado; ?>&action=marcar" class="btn btn-primary" style="padding: 5px 10px;">
                                            Marcar Presente
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>