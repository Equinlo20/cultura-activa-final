<?php
require 'includes/db_connection.php';
require 'includes/functions.php';

// 1. Seguridad
checkLogin();
$id_usuario = $_SESSION['user_id'];

// 2. Lógica: Obtener todos los tickets del usuario
$stmt = $pdo->prepare("
    SELECT 
        t.id_ticket, t.numero_ticket,
        e.nombre as evento_nombre, e.fecha_evento,
        p.estado as pago_estado,
        tt.nombre as tipo_ticket_nombre
    FROM tickets t
    JOIN eventos e ON t.id_evento = e.id_evento
    LEFT JOIN pagos p ON t.id_ticket = p.id_ticket
    JOIN tipos_ticket tt ON t.id_tipo_ticket = tt.id_tipo_ticket
    WHERE t.id_usuario = ?
    ORDER BY e.fecha_evento DESC
");
$stmt->execute([$id_usuario]);
$mis_tickets = $stmt->fetchAll();

// 3. Renderizado
include 'includes_public/header.php';
?>

<h1>Mis Eventos y Tickets</h1>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'success'): ?>
        <div class="alert alert-success">¡Inscripción completada exitosamente!</div>
    <?php elseif ($_GET['status'] == 'already_registered'): ?>
        <div class="alert alert-danger">Ya estabas inscrito en ese evento.</div>
    <?php endif; ?>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (count($mis_tickets) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Tipo de Ticket</th>
                        <th>Número de Ticket</th>
                        <th>Fecha del Evento</th>
                        <th>Estado del Pago</th>
                        <th>Certificado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mis_tickets as $ticket): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket['evento_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['tipo_ticket_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['numero_ticket']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($ticket['fecha_evento'])); ?></td>
                            <td>
                                <span class="tag tag-<?php echo strtolower($ticket['pago_estado'] ?? 'pendiente'); ?>">
                                    <?php echo htmlspecialchars($ticket['pago_estado'] ?? 'Pendiente'); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                // Lógica para el botón de certificado
                                $evento_paso = (strtotime($ticket['fecha_evento']) < time());
                                $pago_completo = ($ticket['pago_estado'] == 'Completado');
                                
                                if ($pago_completo && $evento_paso) {
                                    echo '<a href="descargar_certificado.php?id_ticket=' . $ticket['id_ticket'] . '" class="btn btn-secondary" style="padding: 5px 10px;">Descargar</a>';
                                } elseif (!$pago_completo) {
                                    echo '<small>Pago Pendiente</small>';
                                } else {
                                    echo '<small>Evento Futuro</small>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aún no te has inscrito a ningún evento.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes_public/footer.php'; ?>