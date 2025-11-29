<?php
// Inicia la sesión si no está iniciada.
// session_start() debe ir ANTES de cualquier salida HTML.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario ha iniciado sesión.
 * Si no, lo redirige a la página de login.
 */
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        // La ruta puede necesitar ajuste dependiendo de dónde estés.
        header("Location: ../login.php");
        exit;
    }
}

/**
 * Verifica si el usuario tiene un permiso específico.
 * Los permisos se cargan en la sesión al iniciar sesión.
 *
 * @param string $permiso_nombre El nombre del permiso (ej: 'ver_usuarios')
 * @return bool
 */
function hasPermission($permiso_nombre) {
    // Si no hay permisos en la sesión, no tiene permiso.
    if (!isset($_SESSION['permissions'])) {
        return false;
    }
    
    // Devuelve true si el permiso existe y está seteado a true.
    return isset($_SESSION['permissions'][$permiso_nombre]) && $_SESSION['permissions'][$permiso_nombre] === true;
}
?>