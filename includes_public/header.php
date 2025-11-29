<?php
// Iniciar sesión para saber si el usuario está logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CulturaActiva</title>

    <link rel="stylesheet" href="public/css/style.css">

</head>

<body class="public-page">

    <nav class="public-nav">
        <a href="index.php" class="logo-container">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-1.5h5.25m-5.25 0h3m-3 0h-3m-2.25-3h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
            </svg>
            <span>CulturaActiva</span>
        <div class="user-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="mis_eventos.php">Mis Eventos</a>

                <?php if (isset($_SESSION['permissions']['ver_dashboard'])): ?>
                    <a href="admin/dashboard.php">Ir al Dashboard</a>
                <?php endif; ?>

                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary">Iniciar Sesión</a>
                <a href="register.php" class="btn btn-primary">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="public-container">