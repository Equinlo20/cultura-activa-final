<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('ejecutar_control_asistencia')) { // Permiso nuevo
    die("Acceso denegado.");
}

// 2. Obtener datos de la URL
$id_ticket = $_GET['id_ticket'] ?? null;
$id_evento = $_GET['id_evento'] ?? null;
$action = $_GET['action'] ?? 'marcar'; // 'marcar' o 'desmarcar'

if (!$id_ticket || !$id_evento) {
    header("Location: control_asistencia.php");
    exit;
}

// 3. Lógica de Actualización
try {
    if ($action == 'marcar') {
        // Marcar como presente (escaneado = 1) y estado = 'Usado'
        $stmt = $pdo->prepare("UPDATE tickets SET escaneado = 1, estado = 'Usado' WHERE id_ticket = ?");
        $stmt->execute([$id_ticket]);
        $status = 'marked';
    } else {
        // Desmarcar (escaneado = 0) y estado = 'Activo'
        $stmt = $pdo->prepare("UPDATE tickets SET escaneado = 0, estado = 'Activo' WHERE id_ticket = ?");
        $stmt->execute([$id_ticket]);
        $status = 'unmarked';
    }
    
    // 4. Redirigir de vuelta a la lista
    header("Location: control_asistencia.php?id_evento=" . $id_evento . "&status=" . $status);
    exit;

} catch (PDOException $e) {
    header("Location: control_asistencia.php?id_evento=" . $id_evento . "&status=error");
    exit;
}
?>