<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
// Asumimos un permiso 'eliminar_pagos'
if (!hasPermission('eliminar_pagos')) {
    die("Acceso denegado.");
}

// 2. Obtener el ID del pago de la URL
$id_pago = $_GET['id'] ?? null;

if ($id_pago) {
    try {
        // 3. Ejecutar DELETE
        $stmt = $pdo->prepare("DELETE FROM pagos WHERE id_pago = ?");
        $stmt->execute([$id_pago]);
        
        // 4. Redirigir a la lista con mensaje de éxito
        header("Location: pagos.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        // 5. Manejar error
        header("Location: pagos.php?status=error_db");
        exit;
    }
} else {
    // Si no hay ID, simplemente volver
    header("Location: pagos.php");
    exit;
}
?>