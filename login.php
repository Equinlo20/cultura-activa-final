<?php
// Iniciar sesión para manejar mensajes de error
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
    // Redirigir según el rol
    if (isset($_SESSION['permissions']['ver_dashboard'])) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: mis_eventos.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - CulturaActiva</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        /* Estructura principal de dos columnas */
        .login-container {
            display: flex;
            min-height: 100vh;
            background-color: var(--card-bg, #fff); /* Fondo blanco por si acaso */
        }

        /* Columna Izquierda (Branding/Imagen) */
        .login-branding {
            flex-basis: 50%; /* Ocupa la mitad del ancho */
            background-color: var(--primary-light-blue, #EFF6FF); /* Fondo azul claro */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centrar verticalmente */
            align-items: center; /* Centrar horizontalmente */
            padding: 40px;
            text-align: center;
            /* *** NUEVA IMAGEN DE FONDO *** */
            /* Imagen de ejemplo con Sombrero Vueltiao */
             background-image: url('public/img/login.jpg');
            background-size: cover;
            background-position: center;
            position: relative; /* Para el overlay */
        }
        /* Overlay oscuro para mejorar legibilidad del texto sobre la imagen */
        .login-branding::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.5); /* Overlay oscuro (ajustar opacidad si es necesario) */
        }
        .login-branding h1, .login-branding p {
            color: white; /* Texto blanco sobre el overlay */
            position: relative; /* Asegurar que esté sobre el overlay */
            z-index: 1;
        }
         .login-branding h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .login-branding p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85); /* Blanco semi-transparente */
        }
        .login-branding .logo-image-login { /* Estilo para un logo si lo pones */
             width: 150px; /* Ajusta según tu logo */
             margin-bottom: 20px;
             position: relative;
             z-index: 1;
        }


        /* Columna Derecha (Formulario) */
        .login-form-container {
            flex-basis: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background-color: var(--background-body, #F9FAFB); /* Fondo gris claro */
        }
        .login-form {
            width: 100%;
            max-width: 400px; /* Ancho máximo del formulario */
        }
        .login-form h2 {
            font-size: 1.8rem;
            color: var(--text-title, #111827);
            margin-bottom: 25px;
            text-align: center;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        /* Usar .form-control del style.css principal */
        .btn-login { /* Reutilizar .btn .btn-primary */
            width: 100%;
            margin-top: 10px;
        }
        .login-links {
            margin-top: 25px;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-body, #4B5563);
        }
        .login-links a {
            color: var(--primary-blue, #3B82F6); /* Usamos el azul del tema para enlaces */
            font-weight: 600;
            text-decoration: none;
        }
        .login-links a:hover {
            text-decoration: underline;
        }
        .error {
            background: var(--danger-bg, #FEE2E2);
            color: var(--danger-red, #EF4444);
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        /* Estilo para el enlace 'Volver al inicio' */
        .home-link-login {
            color: var(--text-muted, #9CA3AF) !important; /* Color gris claro */
            font-weight: 400;
            font-size: 0.85em;
            display: inline-block;
            margin-top: 15px;
        }

        /* Ocultar elementos de layout global en login */
        body.login-page { padding: 0 !important; }
        .top-navbar, .sidebar, footer { display: none !important; }
    </style>
</head>
<body class="login-page"> <div class="login-container">

        <div class="login-branding">
            <h1>Bienvenido a CulturaActiva</h1>
            <p>Gestiona y descubre eventos culturales fácilmente.</p>
        </div>

        <div class="login-form-container">
            <div class="login-form">
                <h2>Iniciar Sesión</h2>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error"><?php echo $_SESSION['error_message']; ?></div>
                    <?php unset($_SESSION['error_message']); // Limpiar el error ?>
                <?php endif; ?>

                <form action="login_proceso.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-login">Acceder</button>
                </form>

                <div class="login-links">
                    ¿No tienes cuenta? <a href="register.php">Regístrate</a>
                    <br>
                    <a href="index.php" class="home-link-login">Volver al inicio</a>
                </div>
            </div>
        </div>

    </div>

    </body>
</html>