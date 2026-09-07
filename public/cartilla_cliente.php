<?php

require_once "auth_cliente.php";
require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

$cedula = $_SESSION["cliente_cedula"];

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
        AND p.estado IN ('Activo','Mora')
        ORDER BY p.id DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {

    require_once "alerta_sin_prestamo.php";
    exit;

}

$id = $prestamo["id"];

$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$datosCuota = $stmt->fetch(PDO::FETCH_ASSOC);

$fechaLimite = null;

if (!empty($datosCuota['ultima_cuota'])) {
    $fechaLimite = new DateTime($datosCuota['ultima_cuota']);
}

$hoy = new DateTime();

$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$datosCuota = $stmt->fetch(PDO::FETCH_ASSOC);

$fechaLimite = null;

if (!empty($datosCuota['ultima_cuota'])) {
    $fechaLimite = new DateTime($datosCuota['ultima_cuota']);
}

$diasAtraso = 0;

$mora = 0;

$totalActual =
$prestamo['pendiente'];

/* porcentaje inicial */

$porcentajeMora = 0;

if (
    $prestamo['pendiente'] > 0 &&
    $fechaLimite !== null &&
    $hoy > $fechaLimite
) {

$diasAtraso =
$fechaLimite
->diff(
$hoy
)
->days;

/* calcular porcentaje */

if(
$diasAtraso >= 3
&&
$diasAtraso <= 14
){

$porcentajeMora = 5;

}
elseif(
$diasAtraso >= 15
&&
$diasAtraso <= 29
){

$porcentajeMora = 10;

}
elseif(
$diasAtraso >= 30
&&
$diasAtraso <= 44
){

$porcentajeMora = 15;

}
elseif(
$diasAtraso >= 45
){

$porcentajeMora = 20;

}

/* calcular mora */

$mora =

$prestamo['pendiente']

*

(

$porcentajeMora

/

100

);

$totalActual =

$prestamo['pendiente']

+

$mora;

}

/* HISTORIAL */

$sql = "

SELECT *

FROM pagos

WHERE prestamo_id=?

ORDER BY fecha_pago DESC

";

$stmt =
$conexion->prepare($sql); $stmt->execute([
$id ]); $pagos = $stmt->fetchAll( PDO::FETCH_ASSOC );

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
                    <span class="ce-info-label">Cuotas</span>
                    <span class="ce-info-value"><?= $prestamo['cuotas'] ?></span>
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

    <!-- ===== DETALLE DE CUOTAS ===== -->
    <div class="ce-card">
        <div class="ce-card__head">
            <p class="ce-card__title">
                <span class="icon-box"><i class="bi bi-list-check"></i></span>
                Detalle de cuotas
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

                <?php if (!empty($cuotas)): ?>

                    <?php foreach ($cuotas as $c): ?>

                        <tr>
                            <td data-label="#"><span class="ce-num"><?= $c['numero_cuota'] ?></span></td>
                            <td data-label="Vencimiento"><?= $c['fecha_vencimiento'] ?></td>
                            <td data-label="Valor" class="ce-money">$<?= number_format($c['valor']) ?></td>
                            <td data-label="Estado">
                                <?php if ($c['pagada']): ?>
                                    <span class="ce-pill is-paid"><i class="bi bi-check-lg"></i> Pagada</span>
                                <?php else: ?>
                                    <span class="ce-pill is-pending"><i class="bi bi-clock"></i> Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Días atraso">
                                <?php if (($c['dias_atraso'] ?? 0) > 0): ?>
                                    <span class="ce-late-days"><?= $c['dias_atraso'] ?></span>
                                <?php else: ?>
                                    <span class="ce-dash">0</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Mora" class="ce-money">$<?= number_format($c['mora'] ?? 0) ?></td>
                            <td data-label="Fecha pago"><?= $c['fecha_pago'] ? $c['fecha_pago'] : '<span class="ce-dash">—</span>' ?></td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="ce-empty">
                            <i class="bi bi-inbox"></i>
                            No hay cuotas registradas para este préstamo.
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