<?php
require 'includes/db_connection.php';

// Lógica: Obtener todos los eventos publicados
$stmt = $pdo->query("
    SELECT * FROM eventos
    WHERE estado = 'Publicado'
    ORDER BY fecha_evento ASC
");
$eventos = $stmt->fetchAll();

// Renderizado
include 'includes_public/header.php'; // Incluye el header público
?>

<h1>Próximos Eventos</h1>
<p>Descubre las actividades culturales que tenemos para ti.</p>

<div class="event-grid">
    <?php if (count($eventos) > 0): ?>
        <?php foreach ($eventos as $evento): ?>
            <div class="event-card">
                <div class="event-card-body">
                    <h3><?php echo htmlspecialchars($evento['nombre']); ?></h3>
                    <p class="date"><?php echo date('d/m/Y H:i', strtotime($evento['fecha_evento'])); ?></p>
                    <p><?php echo htmlspecialchars(substr($evento['descripcion'], 0, 100)) . '...'; ?></p>
                    <a href="evento_publico.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-primary">Ver Detalles</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay eventos publicados por el momento.</p>
    <?php endif; ?>
</div>

<?php include 'includes_public/footer.php'; // Incluye el footer público ?>