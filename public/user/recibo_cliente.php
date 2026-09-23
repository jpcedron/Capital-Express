<?php

require_once "auth_cliente.php";
require_once "../../config/conexion.php";

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
        <span class="current">Comprobante</span>
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
            <h1 class="ce-page-title">Comprobante de préstamo</h1>
            <p class="ce-page-desc">Resumen informativo de las condiciones y estado del préstamo.</p>
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
                    <?= strtoupper(mb_substr($prestamo['nombre_cliente'], 0, 1)); ?>
                </div>
                <div class="ce-client__info">
                    <span class="ce-client__name"><?= htmlspecialchars($prestamo['nombre_cliente']); ?></span>
                    <span class="ce-client__id">Cédula: <?= htmlspecialchars($prestamo['cedula_cliente']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== DETALLE DEL PRÉSTAMO ===== -->
    <div class="ce-card">
        <div class="ce-card__head">
            <p class="ce-card__title">
                <span class="icon-box"><i class="bi bi-receipt"></i></span>
                Detalle del comprobante
            </p>
        </div>

        <div class="ce-card__body">
            <div class="ce-info-grid">
                <div class="ce-info-item">
                    <span class="ce-info-label">Monto</span>
                    <span class="ce-info-value money">$<?= number_format($prestamo['monto'], 0, ',', '.'); ?></span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Interés</span>
                    <span class="ce-info-value"><?= $prestamo['interes']; ?>%</span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Fecha préstamo</span>
                    <span class="ce-info-value"><?= date("d/m/Y", strtotime($prestamo['fecha_prestamo'])); ?></span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Última cuota</span>
                    <span class="ce-info-value">
                        <?php if (!empty($ultimaCuota['ultima_cuota'])): ?>
                            <?= date("d/m/Y", strtotime($ultimaCuota['ultima_cuota'])); ?>
                        <?php else: ?>
                            <span class="ce-dash">No registrada</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Mora</span>
                    <span class="ce-info-value money danger">$<?= number_format($prestamo['mora'], 0, ',', '.'); ?></span>
                </div>
                <div class="ce-info-item">
                    <span class="ce-info-label">Total pagado</span>
                    <span class="ce-info-value money good">$<?= number_format($prestamo['abonado'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== ACCIONES ===== -->
    <div class="ce-card">
        <div class="ce-card__body">
            <div class="ce-actions">
                <p class="ce-actions__note">
                    <i class="bi bi-info-circle"></i>
                    Documento informativo del préstamo · Capital Express.
                </p>
                <div class="ce-actions__row">
                    <a href="panel_de_usuario.php" class="btn-ce btn-ce--ghost">
                        <i class="bi bi-arrow-left-circle"></i>
                        Regresar al Panel
                    </a>
                    <a href="descargar_recibo.php?id=<?= $prestamo['id']; ?>" class="btn-ce btn-ce--solid">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        Descargar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>