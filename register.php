<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'includes/db_connection.php';

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

$error = '';
$success = '';

// Obtener el ID del rol "Asistente" o "Participante"
$stmt_rol = $pdo->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'Asistente' OR nombre_rol = 'Participante' LIMIT 1");
$stmt_rol->execute();
$id_rol_asistente = $stmt_rol->fetchColumn();

if (!$id_rol_asistente) {
    die("Error de configuración: No se encontró el rol 'Asistente' o 'Participante'.");
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telefono = $_POST['telefono'] ?? null; // Teléfono es opcional

    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Nombre, email y contraseña son obligatorios.";
    } elseif (strlen($password) < 6) { // Añadir validación simple de contraseña
         $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre_completo, email, password_hash, id_rol, estado, telefono)
                 VALUES (?, ?, ?, ?, 'Activo', ?)"
            );
            $stmt->execute([$nombre, $email, $password_hash, $id_rol_asistente, $telefono]);
            $success = "¡Registro exitoso! Ahora puedes <a href='login.php'>iniciar sesión</a>.";

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "El correo electrónico ya está registrado.";
            } else {
                $error = "Error al registrar: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - CulturaActiva</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        /* Estructura principal de dos columnas */
        .login-container {
            display: flex;
            min-height: 100vh;
            background-color: var(--card-bg, #fff);
        }

        /* Columna Izquierda (Branding/Imagen) */
        .login-branding {
            flex-basis: 50%;
            background-color: var(--primary-light-blue, #EFF6FF);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            background-image: url('public/img/registro.jpg'); /* Imagen diferente para registro */
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .login-branding::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.45); /* Overlay ligeramente más oscuro */
        }
        .login-branding h1, .login-branding p {
            color: white;
            position: relative;
            z-index: 1;
        }
         .login-branding h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .login-branding p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85);
        }
         .login-branding .logo-image-login {
             width: 150px;
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
            background-color: var(--background-body, #F9FAFB);
        }
        .login-form {
            width: 100%;
            max-width: 400px;
        }
        .login-form h2 {
            font-size: 1.8rem;
            color: var(--text-title, #111827);
            margin-bottom: 25px;
            text-align: center;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .btn-register { /* Botón de registro */
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
            color: var(--primary-blue, #3B82F6);
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
        .success { /* Estilo para mensaje de éxito */
            background: var(--success-bg, #D1FAE5);
            color: var(--success-green, #10B981);
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
         .success a { /* Enlace dentro del mensaje de éxito */
             color: var(--success-green, #10B981);
             font-weight: bold;
             text-decoration: underline;
         }

        /* Ocultar elementos de layout global en registro */
        body.register-page { padding: 0 !important; }
        .top-navbar, .sidebar, footer { display: none !important; }
    </style>
</head>
<body class="register-page"> <div class="login-container">

        <div class="login-branding">
            <h1>Únete a la Comunidad</h1>
            <p>Regístrate para descubrir y participar en los mejores eventos culturales.</p>
        </div>

        <div class="login-form-container">
            <div class="login-form">
                <h2>Crear Cuenta</h2>

                <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
                <?php if ($success): ?><div class="success"><?php echo $success; // Usamos echo normal porque ya tiene HTML ?></div><?php endif; ?>

                <?php if (!$success): ?>
                    <form action="register.php" method="POST">
                        <div class="form-group">
                            <label for="nombre_completo">Nombre Completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono (Opcional)</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña (mínimo 6 caracteres)</label>
                            <input type="password" id="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary btn-register">Registrarse</button>
                    </form>
                <?php endif; ?>

                <div class="login-links">
                    <?php if (!$success): ?>
                        ¿Ya tienes cuenta? <a href="login.php">Inicia Sesión</a>
                    <?php endif; ?>
                    <br>
                    <a href="index.php" style="color: var(--text-muted); font-weight: 400; font-size: 0.85em; display: inline-block; margin-top: 15px;">Volver al inicio</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>