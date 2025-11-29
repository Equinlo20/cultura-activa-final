<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('eliminar_patrocinadores')) {
    die("Acceso denegado.");
}

// 2. Obtener el ID del patrocinador de la URL
$id_patrocinador = $_GET['id'] ?? null;

if ($id_patrocinador) {
    try {
        // 3. Ejecutar DELETE
        $stmt = $pdo->prepare("DELETE FROM patrocinadores WHERE id_patrocinador = ?");
        $stmt->execute([$id_patrocinador]);
        
        // 4. Redirigir a la lista con mensaje de éxito
        header("Location: patrocinadores.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        // 5. Manejar error
        header("Location: patrocinadores.php?status=error_db");
        exit;
    }
} else {
    // Si no hay ID, simplemente volver
    header("Location: patrocinadores.php");
    exit;
}
?>