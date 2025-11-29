<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

checkLogin();
if (!hasPermission('eliminar_roles')) {
    die("Acceso denegado.");
}

$id_rol = $_GET['id'] ?? null;

// No permitir eliminar los roles 1 (Admin) o el rol del propio usuario
if ($id_rol == 1 || $id_rol == $_SESSION['user_rol']) {
    header("Location: roles.php?status=error_delete_core");
    exit;
}

if ($id_rol) {
    try {
        $stmt = $pdo->prepare("DELETE FROM roles WHERE id_rol = ?");
        $stmt->execute([$id_rol]);
        
        header("Location: roles.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        // Error si hay usuarios con este rol
        header("Location: roles.php?status=error_fk");
        exit;
    }
} else {
    header("Location: roles.php");
    exit;
}
?>