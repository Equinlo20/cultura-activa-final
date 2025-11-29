<?php
require 'includes/db_connection.php';
require 'includes/functions.php';
require 'fpdf/fpdf.php'; // Requiere la librería FPDF

// 1. Seguridad
checkLogin();
$id_ticket = $_GET['id_ticket'] ?? null;
$id_usuario = $_SESSION['user_id'];

if (!$id_ticket) {
    die("Ticket no especificado.");
}

// 2. Validación: Obtener datos y verificar propiedad
$stmt = $pdo->prepare("
    SELECT 
        t.id_ticket,
        u.nombre_completo,
        e.nombre as evento_nombre, e.fecha_evento,
        p.estado as pago_estado
    FROM tickets t
    JOIN usuarios u ON t.id_usuario = u.id_usuario
    JOIN eventos e ON t.id_evento = e.id_evento
    LEFT JOIN pagos p ON t.id_ticket = p.id_ticket
    WHERE t.id_ticket = ? AND t.id_usuario = ?
");
$stmt->execute([$id_ticket, $id_usuario]);
$data = $stmt->fetch();

if (!$data) {
    die("Acceso denegado. Este ticket no te pertenece o no existe.");
}

// 3. Validación de Lógica de Negocio
$evento_paso = (strtotime($data['fecha_evento']) < time());
$pago_completo = ($data['pago_estado'] == 'Completado');

if (!$pago_completo) {
    die("El pago de este ticket aún no ha sido completado.");
}
if (!$evento_paso) {
    die("No puedes descargar un certificado de un evento que aún no ha sucedido.");
}

// 4. Lógica de Certificado (Crear si no existe)
$stmt_cert = $pdo->prepare("SELECT codigo_verificacion FROM certificados WHERE id_ticket = ?");
$stmt_cert->execute([$id_ticket]);
$codigo_verificacion = $stmt_cert->fetchColumn();

if (!$codigo_verificacion) {
    $codigo_verificacion = 'CERT-' . $id_ticket . '-' . strtoupper(uniqid());
    $stmt_insert = $pdo->prepare(
        "INSERT INTO certificados (id_ticket, codigo_verificacion, url_pdf, fecha_emision) 
         VALUES (?, ?, 'generado_en_vivo.pdf', CURRENT_TIMESTAMP)"
    );
    $stmt_insert->execute([$id_ticket, $codigo_verificacion]);
}

// ======================================================
// --- 5. GENERACIÓN DEL PDF (VERSIÓN MEJORADA) ---
// ======================================================

// Crear PDF (L = Landscape/Horizontal)
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// --- Borde ---
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(52, 152, 219); // Azul primario
$pdf->Rect(5, 5, $pdf->GetPageWidth() - 10, $pdf->GetPageHeight() - 10);

// --- Logo (Opcional) ---
// $pdf->Image('public/img/logo.png', $pdf->GetPageWidth() / 2 - 15, 25, 30);
// $pdf->Ln(30);

// --- Título ---
$pdf->SetFont('Arial', 'B', 32);
$pdf->SetTextColor(44, 62, 80); // Azul oscuro
$pdf->Cell(0, 35, utf8_decode('CERTIFICADO DE ASISTENCIA'), 0, 1, 'C');

// --- Texto "Se otorga a:" ---
$pdf->SetFont('Arial', '', 18);
$pdf->SetTextColor(51, 51, 51); // Gris oscuro
$pdf->Cell(0, 10, utf8_decode('Se otorga el presente certificado a:'), 0, 1, 'C');
$pdf->Ln(8);

// --- Nombre del Asistente ---
$pdf->SetFont('Arial', 'B', 28);
$pdf->SetTextColor(52, 152, 219); // Azul primario
$pdf->Cell(0, 15, utf8_decode($data['nombre_completo']), 0, 1, 'C');
$pdf->Ln(8);

// --- Texto "Por su participación..." ---
$pdf->SetFont('Arial', '', 18);
$pdf->SetTextColor(51, 51, 51);
$pdf->Cell(0, 10, utf8_decode('Por su valiosa participación en el evento:'), 0, 1, 'C');
$pdf->Ln(8);

// --- Nombre del Evento ---
$pdf->SetFont('Arial', 'I', 22);
$pdf->Cell(0, 15, utf8_decode($data['evento_nombre']), 0, 1, 'C');
$pdf->Ln(10);

// --- Línea horizontal ---
$pdf->Line(60, $pdf->GetY(), $pdf->GetPageWidth() - 60, $pdf->GetY());
$pdf->Ln(10);

// --- Fecha y Código ---
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(100, 100, 100);

// *** ¡ESTA ES LA CORRECCIÓN DE LA FECHA! ***
// *** CORRECCIÓN DE FECHA A ESPAÑOL ***
$timestamp = strtotime($data['fecha_evento']);
$dia = date('d', $timestamp);
$mes_num = date('n', $timestamp); // 'n' es el número del mes (1-12)
$ano = date('Y', $timestamp);

// Array de meses en español
$meses_es = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Construimos la fecha en español
$fecha_formateada = $dia . ' de ' . $meses_es[$mes_num] . ' de ' . $ano;

$pdf->Cell(0, 8, utf8_decode('Realizado el ' . $fecha_formateada), 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 8, utf8_decode('Código de Verificación: ' . $codigo_verificacion), 0, 1, 'C');

// --- Salida (Fuerza la descarga) ---
$pdf->Output('D', 'Certificado-' . $data['evento_nombre'] . '.pdf');
exit;
?>