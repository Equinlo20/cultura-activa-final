<?php
require '../includes/functions.php';
require '../includes/db_connection.php';

checkLogin();
if (!hasPermission('ver_eventos')) {
    die("Acceso denegado.");
}

$id_evento = $_GET['id'] ?? null;
if (!$id_evento) {
    header("Location: eventos.php");
    exit;
}

// Cargar datos del evento y del creador
$stmt = $pdo->prepare("
    SELECT e.*, u.nombre_completo as creador_nombre
    FROM eventos e
    LEFT JOIN usuarios u ON e.id_organizador = u.id_usuario
    WHERE e.id_evento = ?
");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch();

if (!$evento) {
    header("Location: eventos.php");
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="content">
    <div class="header-bar">
        <h1>Detalles del Evento</h1>
        <div>
            <a href="eventos.php" class="btn btn-secondary">Volver</a>
            <?php if (hasPermission('editar_eventos')): ?>
                <a href="evento_gestionar.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-primary">Editar</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="detalle-grid">
        
        <div class="card">
            <div class="card-body">
                <h3><?php echo htmlspecialchars($evento['nombre']); ?></h3>
                
                <h4>Descripción</h4>
                <p><?php echo nl2br(htmlspecialchars($evento['descripcion'])); ?></p>
                
                <div class="detalle-fechas">
                    <div>
                        <strong>Fecha de Inicio:</strong>
                        <span><?php echo date('d/m/Y H:i', strtotime($evento['fecha_evento'])); ?></span>
                    </div>
                    </div>
                
                <h4>Ubicación</h4>
                <p><?php echo htmlspecialchars($evento['lugar']); ?></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h4>Información del Evento</h4>
                
                <strong>Estado:</strong>
                <p><span class="tag <?php echo strtolower($evento['estado']); ?>"><?php echo htmlspecialchars($evento['estado']); ?></span></p>
                
                <strong>Visibilidad:</strong>
                <p><?php echo htmlspecialchars($evento['visibilidad']); ?></p>
                
                <strong>Capacidad:</strong>
                <p><?php echo htmlspecialchars($evento['capacidad']); ?> asistentes</p>
                
                <strong>Creado por:</strong>
                <p><?php echo htmlspecialchars($evento['creador_nombre']); ?></p>
                
                <strong>Fecha de Creación:</strong>
                <p><?php echo date('d/m/Y H:i', strtotime($evento['fecha_publicacion'])); ?></p>
            </div>
        </div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>