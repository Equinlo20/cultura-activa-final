<?php
require 'includes/db_connection.php';
require 'includes/functions.php'; // Para checkLogin()

$id_evento = $_GET['id'] ?? null;
if (!$id_evento) {
    header("Location: index.php");
    exit;
}

// 1. Lógica: Obtener datos del evento
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id_evento = ? AND estado = 'Publicado'");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch();

if (!$evento) {
    header("Location: index.php"); // No se encontró el evento
    exit;
}

// 2. Lógica: Obtener los tipos de ticket para este evento
$stmt_tipos = $pdo->prepare("SELECT * FROM tipos_ticket WHERE id_evento = ? ORDER BY precio");
$stmt_tipos->execute([$id_evento]);
$tipos_ticket = $stmt_tipos->fetchAll();

// 3. Renderizado
include 'includes_public/header.php';
?>

<div class="detalle-grid">
    
    <div class="card">
        <div class="card-body">
            <h1><?php echo htmlspecialchars($evento['nombre']); ?></h1>
            
            <div class="detalle-fechas">
                <div>
                    <strong>Fecha:</strong>
                    <span><?php echo date('d/m/Y H:i', strtotime($evento['fecha_evento'])); ?></span>
                </div>
                <div>
                    <strong>Lugar:</strong>
                    <span><?php echo htmlspecialchars($evento['lugar']); ?></span>
                </div>
            </div>
            
            <h4>Descripción del Evento</h4>
            <p><?php echo nl2br(htmlspecialchars($evento['descripcion'])); ?></p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h4>Inscripción / Tickets</h4>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (count($tipos_ticket) > 0): ?>
                    <form action="proceso_compra.php" method="POST">
                        <input type="hidden" name="id_evento" value="<?php echo $evento['id_evento']; ?>">
                        
                        <div class="form-group">
                            <label for="id_tipo_ticket">Selecciona tu entrada:</label>
                            <select id="id_tipo_ticket" name="id_tipo_ticket" class="form-control" required>
                                <?php foreach ($tipos_ticket as $tipo): ?>
                                    <option value="<?php echo $tipo['id_tipo_ticket']; ?>">
                                        <?php echo htmlspecialchars($tipo['nombre']); ?> 
                                        ($<?php echo number_format($tipo['precio'], 2); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Inscribirme / Comprar</button>
                    </form>
                <?php else: ?>
                    <p>Las inscripciones para este evento no están disponibles.</p>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-danger">
                    Debes <a href="login.php?redirect=evento_publico.php?id=<?php echo $id_evento; ?>"><b>iniciar sesión</b></a> o <a href="register.php"><b>registrarte</b></a> para poder inscribirte.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes_public/footer.php'; ?>