<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('eliminar_tipos_ticket')) {
    die("Acceso denegado.");
}

// 2. Lógica de Eliminación
$id_tipo_ticket = $_GET['id'] ?? null;
if ($id_tipo_ticket) {
    try {
        $stmt = $pdo->prepare("DELETE FROM tipos_ticket WHERE id_tipo_ticket = ?");
        $stmt->execute([$id_tipo_ticket]);
        
        header("Location: tipos_ticket.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        // Error (probablemente porque ya hay tickets vendidos de este tipo)
        if ($e->getCode() == 23000) {
            header("Location: tipos_ticket.php?status=error_fk");
        } else {
            header("Location: tipos_ticket.php?status=error_db");
        }
        exit;
    }
} else {
    header("Location: tipos_ticket.php");
    exit;
}
?>