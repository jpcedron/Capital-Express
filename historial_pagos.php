<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$prestamo_id = $_GET['prestamo_id'] ?? null;

if (!$prestamo_id) {
    die("Préstamo no encontrado");
}

$sql = "
SELECT
fecha_pago,
valor_pago,
saldo_restante,
observacion
FROM pagos
WHERE prestamo_id = ?
ORDER BY fecha_pago DESC
";

$stmt = $conexion->prepare($sql);
$stmt->execute([$prestamo_id]);

$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php  

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Historial de pagos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/historial_pagos.css">
</head>
<body>

<div class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <div class="brand-mark brand-heading">CE</div>
            <div class="brand-text">
                <div class="brand-title brand-heading">Capital Express</div>
                <div class="brand-sub">PANEL ADMINISTRATIVO</div>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap">

    <div class="breadcrumb-row">
        <a href="listado.php">Préstamos</a>
        <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
        <span class="current">Historial de pagos</span>
    </div>

    <div class="page-head">
        <div>
            <h1 class="page-title">Historial de pagos</h1>
            <div class="page-desc">Registro cronológico de los abonos realizados</div>
        </div>
        <div class="count-tag">
            <i class="bi bi-receipt"></i>
            <?= count($pagos) ?> pago<?= count($pagos) == 1 ? '' : 's' ?> registrado<?= count($pagos) == 1 ? '' : 's' ?>
        </div>
    </div>

    <div class="card-custom">

        <div class="card-section-head">
            <div class="card-section-title">
                <i class="bi bi-clock-history"></i>
                Movimientos
            </div>
        </div>

        <?php if (count($pagos) > 0): ?>
        <div class="table-wrap">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Pago</th>
                        <th>Saldo restante</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pagos as $pago): ?>
                    <tr>
                        <td class="cell-date">
                            <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                        </td>
                        <td class="cell-pay">
                            <i class="bi bi-arrow-down-circle-fill"></i>$<?= number_format($pago['valor_pago'],0,",",".") ?>
                        </td>
                        <td class="cell-balance">
                            $<?= number_format($pago['saldo_restante'],0,",",".") ?>
                        </td>
                        <td class="cell-obs">
                            <?= $pago['observacion'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Aún no se han registrado pagos.</p>
        </div>
        <?php endif; ?>

        <div class="card-footer-actions">
            <div class="footer-note">
                <i class="bi bi-info-circle"></i>
                Los valores se muestran en pesos colombianos
            </div>
            <a href="listado.php" class="btn-back-custom">
                <i class="bi bi-arrow-left"></i>
                Volver al listado
            </a>
        </div>

    </div>

</div>

</body>
</html>