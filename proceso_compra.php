<?php
require 'includes/db_connection.php';
require 'includes/functions.php';

// 1. Seguridad: Asegurarse de que el usuario esté logueado
checkLogin(); 

// 2. Verificar que se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_evento = $_POST['id_evento'];
    $id_tipo_ticket = $_POST['id_tipo_ticket'];
    $id_usuario = $_SESSION['user_id'];
    
    if (empty($id_evento) || empty($id_tipo_ticket)) {
        header("Location: index.php?status=error");
        exit;
    }

    // Iniciar transacción
    $pdo->beginTransaction();
    
    try {
        // 3. Validación: Verificar que el usuario no esté ya inscrito
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE id_evento = ? AND id_usuario = ?");
        $stmt_check->execute([$id_evento, $id_usuario]);
        if ($stmt_check->fetchColumn() > 0) {
            // Ya está inscrito, redirigir a "Mis Eventos"
            header("Location: mis_eventos.php?status=already_registered");
            exit;
        }

        // 4. Obtener el precio del tipo de ticket
        $stmt_price = $pdo->prepare("SELECT precio FROM tipos_ticket WHERE id_tipo_ticket = ? AND id_evento = ?");
        $stmt_price->execute([$id_tipo_ticket, $id_evento]);
        $precio = $stmt_price->fetchColumn();

        if ($precio === false) {
            // Tipo de ticket no coincide con el evento
            throw new Exception("Tipo de ticket no válido.");
        }
        
        // 5. Crear el Ticket
        $numero_ticket = 'TIC-' . strtoupper(uniqid());
        $stmt_ticket = $pdo->prepare(
            "INSERT INTO tickets (id_evento, id_usuario, id_tipo_ticket, numero_ticket, estado, fecha_compra) 
             VALUES (?, ?, ?, ?, 'Activo', CURRENT_TIMESTAMP)"
        );
        $stmt_ticket->execute([$id_evento, $id_usuario, $id_tipo_ticket, $numero_ticket]);
        $id_ticket = $pdo->lastInsertId();

        // 6. Crear el Pago
        // Si el precio es 0 (evento gratuito), marcar como "Completado"
        $estado_pago = ($precio == 0) ? 'Completado' : 'Pendiente';
        $metodo_pago = ($precio == 0) ? 'Gratuito' : 'Pendiente (Online)';

        $stmt_pago = $pdo->prepare(
            "INSERT INTO pagos (id_ticket, monto, metodo, estado, fecha_pago) 
             VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)"
        );
        $stmt_pago->execute([$id_ticket, $precio, $metodo_pago, $estado_pago]);
        
        // 7. Confirmar transacción
        $pdo->commit();
        
        // 8. Redirigir a "Mis Eventos" con mensaje de éxito
        header("Location: mis_eventos.php?status=success");
        exit;

    } catch (Exception $e) {
        // Si algo falla, deshacer todo
        $pdo->rollBack();
        // Redirigir con error (podrías loggear $e->getMessage())
        header("Location: evento_publico.php?id=$id_evento&status=error");
        exit;
    }

} else {
    // Si no es POST, volver al inicio
    header("Location: index.php");
    exit;
}
?>