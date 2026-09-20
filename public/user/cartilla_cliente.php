<?php

require_once "auth_cliente.php";
require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

$cedula = $_SESSION["cliente_cedula"];

/* =========================================================
   OBTENER PRÉSTAMO ACTIVO DEL CLIENTE
   ========================================================= */

$sql = "SELECT
            p.*,
            c.nombre AS cliente_nombre,
            c.cedula AS cliente_cedula,
            c.telefono AS cliente_telefono,
            c.direccion AS cliente_direccion,
            c.estado_cliente
        FROM prestamos p
        INNER JOIN clientes c
            ON c.id = p.cliente_id
        WHERE c.cedula = ?
        AND p.estado IN ('Activo', 'Mora')
        ORDER BY p.id DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);


/* =========================================================
   SI EL CLIENTE NO TIENE PRÉSTAMO ACTIVO
   ========================================================= */

if (!$prestamo) {

    require_once "alerta_sin_prestamo.php";
    exit;

}


/* =========================================================
   ID DEL PRÉSTAMO
   ========================================================= */

$id = $prestamo["id"];


/* =========================================================
   HISTORIAL DE PAGOS
   ========================================================= */

$sql = "SELECT
            fecha_pago,
            valor_pago,
            pago_mora,
            pago_capital,
            saldo_restante
        FROM pagos
        WHERE prestamo_id = ?
        ORDER BY fecha_pago DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis Cuotas · Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- Tipografías (mismas que la cartilla de admin) -->
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<!-- Diseño del panel de usuario -->
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
                <p class="tagline">Finanzas con Confianza</p>
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
        <a href="panel_de_usuario.php">Inicio</a>
        <span class="sep">/</span>
        <span class="current">Cartilla</span>
    </div>

    <!-- ===== PAGE HEAD ===== -->
    <?php
        $estado = $prestamo['estado'];
        $estadoClass = 'is-active';
        if ($estado == "Pagado") { $estadoClass = 'is-paid'; }
        elseif ($estado == "Mora") { $estadoClass = 'is-late'; }
    ?>
    <div class="ce-page-head">
        <div>
            <h1 class="ce-page-title">Cartilla del préstamo</h1>
            <p class="ce-page-desc">Detalle del cliente, condiciones del préstamo y plan de pagos.</p>
        </div>
    </div>

    <!-- ===== DATOS DEL CLIENTE ===== -->
    <div class="ce-card">
        <div class="ce-card__head">
            <p class="ce-card__title">
                <span class="icon-box"><i class="bi bi-person"></i></span>
                Datos del cliente
            </p>
            <span class="ce-status <?= $estadoClass ?>"><?= htmlspecialchars($estado) ?></span>
        </div>

        <div class="ce-card__body">
            <div class="ce-client">
                <div class="ce-client__avatar">
                    <?= strtoupper(substr($prestamo['cliente_nombre'], 0, 1)) ?>
                </div>
                <div class="ce-client__info">
                    <span class="ce-client__name"><?= htmlspecialchars($prestamo['cliente_nombre']) ?></span>
                    <span class="ce-client__meta">
                        <i class="bi bi-credit-card-2-front"></i>
                        Cédula: <?= htmlspecialchars($prestamo['cliente_cedula']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== INFORMACIÓN DEL PRÉSTAMO ===== -->
    <div class="ce-card">
        <div class="ce-card__head">
            <p class="ce-card__title">
                <span class="icon-box"><i class="bi bi-cash-coin"></i></span>
                Información del préstamo
            </p>
        </div>

        <div class="ce-card__body">
            <div class="ce-info-grid">
                <div class="ce-info-item">
                    <span class="ce-info-label">Monto</span>
                    <span class="ce-info-value money">$<?= number_format($prestamo['monto']) ?></span>
                </div>
                 <div class="ce-info-item">
                    <span class="ce-info-label">Interés</span>
                    <span class="ce-info-value money"><?= number_format($prestamo['interes'], 2) ?>%</span>
                </div>
                 <div class="ce-info-item">
                    <span class="ce-info-label">Total Pactado</span>
                    <span class="ce-info-value money">$<?= number_format($prestamo['total_pagar']) ?></span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Valor cuota</span>
                    <span class="ce-info-value money">$<?= number_format($prestamo['valor_cuota']) ?></span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Abonado</span>
                    <span class="ce-info-value money good">$<?= number_format($prestamo['abonado']) ?></span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Capital pendiente</span>
                    <span class="ce-info-value money">$<?= number_format($prestamo['pendiente']) ?></span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Mora acumulada</span>
                    <span class="ce-info-value money danger">$<?= number_format($prestamo['mora']) ?></span>
                </div>
            </div>
        </div>
    </div>

<!-- ===== HISTORIAL DE PAGOS ===== -->
<div class="ce-card">

    <div class="ce-card__head">
        <p class="ce-card__title">
            <span class="icon-box">
                <i class="bi bi-clock-history"></i>
            </span>
            Historial de pagos
        </p>
    </div>

    <div class="ce-table-wrap">

        <table class="ce-table">

            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Pago</th>
                    <th>Mora</th>
                    <th>Capital</th>
                    <th>Saldo restante</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($pagos)): ?>

                <?php foreach ($pagos as $pago): ?>

                    <tr>

                        <td data-label="Fecha">
                            <?= htmlspecialchars($pago['fecha_pago']) ?>
                        </td>

                        <td data-label="Pago" class="ce-money">
                            $<?= number_format($pago['valor_pago']) ?>
                        </td>

                        <td data-label="Mora" class="ce-money">
                            $<?= number_format($pago['pago_mora'] ?? 0) ?>
                        </td>

                        <td data-label="Capital" class="ce-money">
                            $<?= number_format($pago['pago_capital'] ?? 0) ?>
                        </td>

                        <td data-label="Saldo restante" class="ce-money">
                            $<?= number_format($pago['saldo_restante']) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="ce-empty">
                        <i class="bi bi-inbox"></i>
                        No hay pagos registrados para este préstamo.
                    </td>
                </tr>

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
                        Volver
                    </a>

                    <a href="../descargar_cartilla.php?id=<?= $prestamo['id'] ?>" class="btn-ce btn-ce--solid">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        Descargar Cartilla PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>