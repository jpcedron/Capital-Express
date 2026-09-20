<?php

require_once "auth_cliente.php";
require_once "../config/conexion.php";


// Conexión a la base de datos
$conexion = (new Conexion())->conectar();

// Datos del cliente que inició sesión
$cedula = $_SESSION["cliente_cedula"];

// Buscar el préstamo activo del cliente
$sql = "SELECT p.*
        FROM prestamos p
        INNER JOIN clientes c ON c.id = p.cliente_id
        WHERE c.cedula = ?
        AND p.estado != 'Pagado'
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    require_once "alerta_sin_cuotas.php";
    exit;
}

// ID del préstamo
$prestamo_id = $prestamo["id"];

// Obtener todas las cuotas del préstamo
$sql = "SELECT *
        FROM cuotas
        WHERE prestamo_id = ?
        ORDER BY numero_cuota ASC";

$stmt = $conexion->prepare($sql);
$stmt->execute([$prestamo_id]);

$cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cuotas · Capital Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Mismas tipografías que la cartilla -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Mismo diseño que la cartilla -->
    <link rel="stylesheet" href="../css/cartilla_cliente.css">
</head>

<body class="ce-body">

<!-- ===== TOP BAR ===== -->
<nav class="ce-navbar">
    <div class="ce-navbar__inner">
        <a href="panel_de_usuario.php" class="ce-navbar__brand">
            <div class="brand-icon brand-heading">CE</div>
            <div class="brand-text">
                <p class="name brand-heading">Capital Express</p>
                <p class="tagline">Finanzas con confianza</p>
            </div>
        </a>
        <a href="panel_de_usuario.php" class="ce-navbar__back">
            <i class="bi bi-grid-1x2"></i>
            <span>Inicio</span>
        </a>
    </div>
</nav>

<div class="ce-container">

    <!-- ===== BREADCRUMB ===== -->
    <div class="ce-breadcrumb">
        <a href="panel_de_usuario.php">Panel</a>
        <span class="sep">/</span>
        <span class="current">Historial de cuotas</span>
    </div>

    <!-- ===== PAGE HEAD ===== -->
    <div class="ce-page-head">
        <div>
            <h1 class="ce-page-title">Historial de cuotas</h1>
            <p class="ce-page-desc">Detalle completo de vencimientos y pagos.</p>
        </div>

        <span class="ce-id-tag">
            <i class="bi bi-list-ol"></i>
            <?= count($cuotas); ?> cuotas
        </span>
    </div>

    <!-- ===== CRONOGRAMA DE PAGOS ===== -->
    <div class="ce-card">
        <div class="ce-card__head">
            <p class="ce-card__title">
                <span class="icon-box"><i class="bi bi-calendar2-check"></i></span>
                Cronograma de pagos
            </p>
        </div>

        <div class="ce-table-wrap">
            <table class="ce-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vencimiento</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Días atraso</th>
                        <th>Mora</th>
                        <th>Fecha pago</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($cuotas)): ?>
                    <tr>
                        <td colspan="7" class="ce-empty">
                            <i class="bi bi-inbox"></i>
                            No hay cuotas registradas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cuotas as $c): ?>
                        <tr>
                            <td data-label="#"><span class="ce-num"><?= $c['numero_cuota']; ?></span></td>
                            <td data-label="Vencimiento"><?= $c['fecha_vencimiento']; ?></td>
                            <td data-label="Valor"><span class="ce-money">$<?= number_format($c['valor'], 0, ",", "."); ?></span></td>
                            <td data-label="Estado">
                                <?php if ($c['pagada']): ?>
                                    <span class="ce-pill is-paid"><i class="bi bi-check-circle-fill"></i> Pagada</span>
                                <?php else: ?>
                                    <span class="ce-pill is-pending"><i class="bi bi-hourglass-split"></i> Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Días atraso">
                                <?php if ((int)$c['dias_atraso'] > 0): ?>
                                    <span class="ce-late-days"><?= $c['dias_atraso']; ?></span>
                                <?php else: ?>
                                    <span class="ce-dash">0</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Mora">
                                <?php if ((int)$c['mora'] > 0): ?>
                                    <span class="ce-money danger">$<?= number_format($c['mora'], 0, ",", "."); ?></span>
                                <?php else: ?>
                                    <span class="ce-dash">$0</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Fecha pago">
                                <?php if ($c['fecha_pago']): ?>
                                    <?= $c['fecha_pago']; ?>
                                <?php else: ?>
                                    <span class="ce-dash">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== ACCIONES ===== -->
    <div class="ce-card">
        <div class="ce-card__body">
            <div class="ce-actions">
                <p class="ce-actions__note">
                    <i class="bi bi-info-circle"></i>
                    Documento generado a partir de la información registrada.
                </p>
                <div class="ce-actions__row">
                    <a href="panel_de_usuario.php" class="btn-ce btn-ce--ghost">
                        <i class="bi bi-arrow-left"></i>
                        Regresar al Panel
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>