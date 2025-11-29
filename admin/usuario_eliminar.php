<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

// 1. Seguridad
checkLogin();
if (!hasPermission('eliminar_usuarios')) {
    die("Acceso denegado.");
}

// 2. Obtener el ID del usuario de la URL
$id_usuario = $_GET['id'] ?? null;

if ($id_usuario) {
    try {
        // Evitar que un usuario se elimine a sí mismo
        if ($id_usuario == $_SESSION['user_id']) {
            header("Location: usuarios.php?status=error_self_delete");
            exit;
        }

        // 3. Ejecutar DELETE
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        
        // 4. Redirigir
        header("Location: usuarios.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        // Manejar error (ej. si el usuario tiene eventos u otros registros asociados)
        header("Location: usuarios.php?status=error_delete");
        exit;
    }
} else {
    // Si no hay ID, simplemente volver
    header("Location: usuarios.php");
    exit;
}
?>