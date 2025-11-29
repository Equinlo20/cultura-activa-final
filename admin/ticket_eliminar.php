<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('eliminar_tickets')) {
    die("Acceso denegado.");
}

// 2. Obtener el ID del ticket de la URL
$id_ticket = $_GET['id'] ?? null;

if ($id_ticket) {
    try {
        // 3. Ejecutar DELETE
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id_ticket = ?");
        $stmt->execute([$id_ticket]);
        
        // 4. Redirigir a la lista con mensaje de éxito
        header("Location: tickets.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        // 5. Manejar error de llave foránea (FK)
        // Esto pasa si el ticket tiene 'pagos' o 'certificados' asociados
        if ($e->getCode() == 23000) {
            header("Location: tickets.php?status=error_fk");
        } else {
            // Otro error
            header("Location: tickets.php?status=error_db");
        }
        exit;
    }
} else {
    // Si no hay ID, simplemente volver
    header("Location: tickets.php");
    exit;
}
?>