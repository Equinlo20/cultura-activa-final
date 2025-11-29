<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'includes/db_connection.php'; // Conexión a BD

// 1. Verificar que los datos se enviaron por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 2. Buscar al usuario por email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 3. Verificar si el usuario existe y la contraseña es correcta
    // Usamos password_verify() para comparar con el hash de la BD
    if ($user && password_verify($password, $user['password_hash'])) {

        // 4. Iniciar sesión: Guardar datos en la variable $_SESSION
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre_completo'];
        $_SESSION['user_rol'] = $user['id_rol'];

        // 5. Cargar permisos del usuario en la sesión
        $stmt_perms = $pdo->prepare("
            SELECT p.nombre_permiso 
            FROM rol_permisos rp
            JOIN permisos p ON rp.id_permiso = p.id_permiso
            WHERE rp.id_rol = ?
        ");
        $stmt_perms->execute([$user['id_rol']]);
        $permisos_db = $stmt_perms->fetchAll();

        // Convertir el array de permisos a un formato más rápido de consultar
        $permisos_sesion = [];
        foreach ($permisos_db as $perm) {
            $permisos_sesion[$perm['nombre_permiso']] = true;
        }
        $_SESSION['permissions'] = $permisos_sesion;

        // 6. Redirigir según el rol
        if (isset($_SESSION['permissions']['ver_dashboard'])) {
            // Si tiene permiso para ver el dashboard (Admin, Organizador)
            header("Location: admin/dashboard.php");
        } else {
            // Si no (Participante), enviarlo a su página de "Mis Eventos"
            header("Location: index.php");
        }
        exit;

    } else {
        // Error: Credenciales incorrectas
        $_SESSION['error_message'] = "Correo o contraseña incorrectos.";
        header("Location: login.php");
        exit;
    }
} else {
    // Si alguien accede directamente a este archivo, redirigir
    header("Location: login.php");
    exit;
}
