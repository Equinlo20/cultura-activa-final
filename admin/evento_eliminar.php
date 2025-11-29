<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

checkLogin();
if (!hasPermission('eliminar_eventos')) {
    die("Acceso denegado.");
}

$id_evento = $_GET['id'] ?? null;

if ($id_evento) {
    try {
        // (En un sistema real, primero borrarías tickets, pagos, etc., asociados)
        $stmt = $pdo->prepare("DELETE FROM eventos WHERE id_evento = ?");
        $stmt->execute([$id_evento]);
        
        header("Location: eventos.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        // Error si tiene registros hijos (tickets, patrocinadores)
        header("Location: eventos.php?status=error_fk");
        exit;
    }
} else {
    header("Location: eventos.php");
    exit;
}
?>