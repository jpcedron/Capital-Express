<?php

require_once "auth_cliente.php";
require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

$cedula = $_SESSION["cliente_cedula"];

$sql = "SELECT 
            p.*,
            c.nombre AS nombre_cliente,
            c.cedula AS cedula_cliente
        FROM prestamos p
        INNER JOIN clientes c 
            ON c.id = p.cliente_id
        WHERE c.cedula = ?
        AND p.estado IN ('Activo','Mora')
        ORDER BY p.id DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {

    require_once "alerta_sin_recibo.php";
    exit;

}

$id = $prestamo["id"];


// Obtener la última cuota
$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$ultimaCuota = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante · Capital Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/recibo_cliente.css">
</head>
<body class="ce-body">

<!-- ===== NAVBAR ===== -->
<nav class="ce-navbar">
    <div class="ce-navbar__inner">
        <a href="panel_de_usuario.php" class="ce-navbar__brand">
            <div class="brand-icon brand-heading">CE</div>
            <div class="brand-text">
                <div class="name">Capital Express</div>
                <div class="tagline">Gestión de Préstamos</div>
            </div>
        </a>

        <a href="panel_de_usuario.php" class="ce-navbar__back">
            <i class="bi bi-grid-1x2"></i>
            <span>Panel</span>
        </a>
    </div>
</nav>

<div class="container ce-container ce-container--narrow">

    <div class="ce-card ce-receipt">

        <!-- Encabezado del recibo -->
        <div class="ce-header ce-header--center">
            <div class="ce-receipt__mark"><i class="bi bi-receipt"></i></div>
            <h1 class="ce-brand">CAPITAL EXPRESS</h1>
            <p class="ce-header__subtitle">Comprobante de Préstamo</p>
        </div>

        <div class="ce-body-inner">

            <!-- Cliente destacado -->
            <div class="ce-client mb-3">
                <div class="ce-client__avatar">
                    <?= strtoupper(mb_substr($prestamo['nombre_cliente'], 0, 1)); ?>
                </div>
                <div class="ce-client__info">
                    <span class="ce-client__name"><?= htmlspecialchars($prestamo['nombre_cliente']); ?></span>
                    <span class="ce-client__meta">
                        <i class="bi bi-hash"></i> Préstamo #<?= $prestamo['id']; ?>
                    </span>
                </div>
            </div>

            <!-- Detalle en filas -->
            <div class="ce-rows">
                <div class="ce-row">
                    <span class="ce-row__label"><i class="bi bi-cash-stack"></i> Monto</span>
                    <span class="ce-row__value ce-money">$<?= number_format($prestamo['monto'], 0, ',', '.'); ?></span>
                </div>
                <div class="ce-row">
                    <span class="ce-row__label"><i class="bi bi-percent"></i> Interés</span>
                    <span class="ce-row__value"><?= $prestamo['interes']; ?>%</span>
                </div>
                <div class="ce-row">
                    <span class="ce-row__label"><i class="bi bi-calendar-event"></i> Fecha préstamo</span>
                    <span class="ce-row__value"><?= date("d/m/Y", strtotime($prestamo['fecha_prestamo'])); ?></span>
                </div>
                <div class="ce-row">
                    <span class="ce-row__label"><i class="bi bi-calendar-check"></i> Última cuota</span>
                    <span class="ce-row__value">
                        <?= !empty($ultimaCuota['ultima_cuota'])
                            ? date("d/m/Y", strtotime($ultimaCuota['ultima_cuota']))
                            : '<span class="ce-dash">No registrada</span>'; ?>
                    </span>
                </div>
                <div class="ce-row">
                    <span class="ce-row__label"><i class="bi bi-exclamation-triangle"></i> Mora</span>
                    <span class="ce-row__value ce-money" style="color: var(--ce-bad);">
                        $<?= number_format($prestamo['mora'], 0, ',', '.'); ?>
                    </span>
                </div>
                <div class="ce-row">
                    <span class="ce-row__label"><i class="bi bi-flag"></i> Estado</span>
                    <span class="ce-row__value">
                        <?php if ($prestamo['estado'] == "Pagado"): ?>
                            <span class="ce-pill is-paid"><i class="bi bi-check-circle-fill"></i> Pagado</span>
                        <?php elseif ($prestamo['estado'] == "Mora"): ?>
                            <span class="ce-pill is-pending" style="background: var(--ce-bad-bg); color: var(--ce-bad);"><i class="bi bi-x-circle-fill"></i> Mora</span>
                        <?php else: ?>
                            <span class="ce-pill is-pending"><i class="bi bi-dot"></i> Activo</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <!-- Total pagado destacado -->
            <div class="ce-total">
                <span class="ce-total__label">Total pagado</span>
                <span class="ce-total__value">$<?= number_format($prestamo['abonado'], 0, ',', '.'); ?></span>
            </div>

            <!-- Acciones -->
            <div class="ce-actions ce-actions--stack">
                <a href="descargar_recibo.php?id=<?= $prestamo['id']; ?>" class="btn-ce btn-ce--solid">
                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                    Descargar PDF
                </a>
                <a href="panel_de_usuario.php" class="btn-ce btn-ce--ghost">
                    <i class="bi bi-arrow-left-circle"></i>
                    Regresar al Panel
                </a>
            </div>

            <!-- Pie -->
            <div class="ce-receipt__footer">
                <p class="mb-1">Documento informativo del préstamo.</p>
                <strong>Capital Express</strong>
            </div>

        </div>
    </div>

</div>

</body>
</html>
